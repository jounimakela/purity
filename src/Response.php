<?php
/**
 * Response.php
 *
 * The response class contains the methods to deal with HTTP response and
 * browser output.
 *
 * PHP version 5.6
 *
 * Copyright (c) Jouni Mäkelä - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Jouni Mäkelä <jouni.img@gmail.com>, 26.10.2014
 *
 * @package purity
 * @subpackage core
 * @author Jouni Mäkelä <jouni.img@gmail.com>
 * @copyright (c) 2014, Jouni Mäkelä <jouni.img@gmail.com>
 * @version 1.0
 * @since 26.10.2014
 */

namespace purity\core;

class Response
{

    /**
     * @var  array  An array of status codes and messages
     *
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * for the complete and approved list, and links to the RFC's that define them
     */
    public $statusCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a Teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    );

    /**
     * Holds the HTTP status code
     *
     * @var int
     */
    protected $status = 200;

    /**
     * Array of HTTP headers
     *
     * @var array
     */
    protected $headers = array();

    /**
     * The content of the response
     *
     * @var string
     */
    protected $body = null;

    /**
     * Redirect-method redirects to another uri/url. Sets the redirect header,
     * sends the headers and exits. Can redirect via a Location header or
     * using a refresh header. Refresh header works better on certain servers
     * like IIS.
     *
     * @param string $url
     * @param string $method
     * @param int $status
     * @return void
     */
    public static function redirect($url = '', $method = 'location', $status = 302) {
        $response = new static;

        $response->setStatus($status);

        if ($method == 'location') {
            $response->setHeader('Location', $url);
        } elseif ($method == 'refresh') {
            $response->setHeader('Refresh', '0;url=' . $url);
        } else {
            return;
        }

        $response->send(true);
        exit;
    }

    /**
     * Class constructor.
     *
     * @param string $body
     * @param int $status
     * @param array $headers
     */
    public function __construct($body = null, $status = 200, array $headers = array()) {
        foreach ($headers as $key => $val) {
            $this->setHeader($key, $val);
        }

        $this->body = $body;
        $this->status = $status;
    }

    /**
     * Sets the response HTTP status code.
     *
     * @param int $status
     * @return \purity\core\Response
     */
    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    /**
     * Adds individual HTTP header to the queue. Can be used to replace
     * current headers.
     *
     * @param string $name
     * @param string $value
     * @param bool $replace
     * @return \purity\core\Response
     */
    public function setHeader($name, $value, $replace = true) {
        if ($replace) {
            $this->headers[$name] = $value;
        } else {
            $this->headers[] = array($name, $value);
        }

        return $this;
    }

    /**
     * Returns header information from the queue. If name is null, returns
     * complete header array.
     *
     * @param string $name
     * @return mixed
     */
    public function getHeader($name = null) {
        if ($name === null) {
            return $this->headers;
        }

        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * Sets the body for the response
     *
     * @param type $value
     * @return \purity\core\Response
     */
    public function setBody($value) {
        $this->body = $value;
        return $this;
    }

    /**
     * Sends the headers if they haven't already been sent. Return value
     * indicates if they were send or not.
     *
     * @return boolean
     */
    public function sendHeaders() {
        if (headers_sent()) {
            return false;
        }

        if (!empty($_SERVER['FCGI_SERVER_VERSION'])) {
            header('Status: ' . $this->status . ' ' . $this->statusCodes[$this->status]);
        } else {
            $protocol = $_SERVER['SERVER_PROTOCOL'] ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
            header($protocol . ' ' . $this->status . ' ' . $this->statusCodes[$this->status]);
        }

        // Parse non-replace headers
        foreach ($this->headers as $name => $value) {
            if (is_int($name) and is_array($value)) {
                isset($value[0]) and $name = $value[0];
                isset($value[1]) and $value = $value[1];
            }

            is_string($name) and $value = $name . ': ' . $value;

            header($value, true);
        }

        return true;
    }

    /**
     * Sends the response to the output buffer. Optionally will send the headers
     * as well.
     *
     * @param bool $send_headers
     */
    public function send($send_headers = false) {
        $body = $this->__toString();

        if ($send_headers) {
            $this->sendHeaders();
        }

        if ($this->body != null) {
            echo $body;
        }
    }

    /**
     * Magic method. Returns the body as string.
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->body;
    }

    /**
     * Create a JSON response
     *
     * @return void
     **/
    public function json($content, $pretty = false) {
        if ($pretty) {
            $encoded = json_encode($content, JSON_PRETTY_PRINT);
        } else {
            $encoded = json_encode($content);
        }

        // Correct Accept header
        $this->setHeader('Content-Type', 'application/json; charset=UTF-8', true);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $this->setBody($encoded);
                break;

            case JSON_ERROR_DEPTH:
                $this->setBody('Maximum stack depth exceeded');
                break;

            case JSON_ERROR_STATE_MISMATCH:
                $this->setBody('Underflow or the modes mismatch');
                break;

            case JSON_ERROR_CTRL_CHAR:
                $this->setBody('Unexpected control character found');
                break;

            case JSON_ERROR_SYNTAX:
                $this->setBody('Syntax error, malformed JSON');
                break;

            case JSON_ERROR_UTF8:
                $clean = $this->utf8($content);
                $this->json($clean, $pretty);
                break;

            default:
                $this->setBody('Unknown error');
                break;
        }
    }

    /**
     * Utf8 encodes given variable.
     *
     * @param mixed
     * @return mixed
     */
    private function utf8($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->utf8($value);
            }
        } else if (is_string ($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

    /**
     * Create a download response
     *
     * @todo Download response
     * @return void
     **/
    public function download($pathToFile)
    {

    }

}