<?php
/**
 * Email.php
 *
 * Used for sending emails.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 25.10.2014
 *
 * @package purity
 * @subpackage core
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 12.01.2014
 * @see https://github.com/fuel/email/blob/1.8/develop/classes/email/driver.php
 * @todo Testing. Cleanup.
 */

namespace purity\core;

use purity\core\Exceptions\MailException as MailException;
use purity\core\Config as Config;

class Email {

	/**
	 * Email configuration
	 *
	 * @var array
	 **/
	private $config = array();

	/**
	 * To recipients list
	 *
	 * @var array
	 **/
	private $to = array();

	/**
	 * Senders address and name
	 *
	 * @var array
	 **/
	private $from = array();

	/**
	 * Attachment list
	 *
	 * @var array
	 **/
	private $attachments = array();

	/**
	 * Email body
	 *
	 * @var string
	 **/
	private $body = '';

	/**
	 * Email subject
	 *
	 * @var string
	 **/
	private $subject = '';

	/**
	 * Email type
	 *
	 * @var string ('plain'|'html')
	 **/
	private $type = 'plain';

	/**
	 * Extra headers
	 *
	 * @var string
	 **/
	private $headers = array();

	/**
	 * Class constrcutor. Loads configuration
	 *
	 * @return void
	 **/
	public function __construct(Config $config)
	{
		$this->config = $config->email->toArray();
	}

	/**
	 * Email sender address and name
	 *
	 * @return $this
	 **/
	public function from($sender)
	{
		foreach($this->config['addresses'] as $name => $address) {
			if ($name == $sender) {
				$this->from[$address] = $name;
			}
		}

		return $this;
	}

	/**
	 * Email receiver address and name
	 *
	 * @return $this
	 **/
	public function to($address, $name = null)
	{
		if (is_array($address)) {
			$this->to = array_merge($this->to, $address);
		} else {
			$this->to[$address] = $name;
		}

		return $this;
	}

	/**
	 * Sets email subject
	 *
	 * @return $this
	 **/
	public function subject($subject)
	{
		$this->subject = $subject;
		return $this;
	}

	/**
	 * Sets email body
	 *
	 * @return $this
	 **/
	public function body($body, $type = 'plain')
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * Attach file to email
	 *
	 * @return $this
	 **/
	public function attach($file)
	{
		// @todo
	}

	/**
	 * Send mail
	 *
	 * @return bool
	 **/
	public function send()
	{
		if (!$this->to) {
			throw new MailException('Cannot send email without recipients!');
		}

		if (empty($this->from)) {
			throw new MailException('Cannot send email without `from` address!');
		}

		if ($this->type !== 'plain' && $this->type !== 'html') {
			throw new MailException('Invalid email type!');
		}

		$headers = array();
		$headers['Date'] = date('r');

		$headers['To'] = $this->format_addresses($this->to);
		$headers['From'] = $this->format_addresses($this->from);
		$headers['Subject'] = $this->subject;

		$headers['MIME-Version'] = '1.0';
		$headers['Content-Transfer-Encoding'] = '8bit';
		$headers['Content-Type'] = 'text/' . $this->type . ';charset="8bit"';

		$this->body = $this->encode($this->body, '8bit', '\n');

		// echo '<pre>From: ' . print_r($this->from, 1) . '</pre>';
		// echo '<pre>To: ' . print_r($this->format_addresses($this->to), 1) . '</pre>';
		// echo '<pre>Subject: ' . print_r($this->subject, 1) . '</pre>';
		// echo '<pre>Body: ' . print_r($this->body, 1) . '</pre>';
		// echo '<pre>Headers: ' . print_r($headers, 1) . '</pre>';

		if (!mail($this->format_addresses($this->to), $this->subject, $this->body)) {
			throw new MailException('Failed to send mail!');
		}

		return true;
	}

	/**
	 * Set custom header to mail headers
	 *
	 * @return $this
	 **/
	public function header($header, $value = null)
	{
		if (is_array($header)) {
			foreach($header as $head => $value) {
				$this->headers[$head] = $value;
			}
		} else {
			$this->header[$header] = $value;
		}

		return $this;
	}

	/**
	 * Format to 'name <email@address.com>'
	 *
	 * @return string
	 **/
	private function format_addresses(array $addresses)
	{
		$formatted = array();

		foreach ($addresses as $address => $name) {
			$formatted[] = '"' . $name . '" <' . $address . '>';
		}

		return join(', ', $formatted);
	}

	/**
	 * Encode string
	 *
	 * @return
	 * @author
	 **/
	private function encode($string, $encoding, $newline = null)
	{
		$newline or $newline = \Config::get('email.defaults.newline', "\n");

		switch ($encoding) {
			case 'quoted-printable':
				return quoted_printable_encode($string);
			case '7bit':
			case '8bit':
				return $this->prepare_newline(rtrim($string, $newline), $newline);
			case 'base64':
				return chunk_split(base64_encode($string), 76, $newline);
			default:
				throw new MailException($encoding . ' is not a supported encoding method.');
		}
	}

	/**
	 * Standardize newlines
	 *
	 * @return string
	 **/
	private function prepare_newline($string, $newline = null)
	{
		$newline = isset($newline) ? $newline : '\n';

		$replace = array(
			"\r\n"	=> "\n",
			"\n\r"	=> "\n",
			"\r"	=> "\n",
			"\n"	=> $newline
		);

		foreach ($replace as $from => $to) {
			$string = str_replace($from, $to, $string);
		}

		return $string;
	}

	/**
	 * Builds the header and body
	 *
	 * @return array
	 **/
	public function build_message()
	{
		$newline = '\n';
		$charset = 'utf-8';
		$encoding = '8bit';

		$headers = '';
	}
}