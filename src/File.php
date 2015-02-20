<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace purity\core;

use purity\core\Exceptions\FileAccessException as FileAccessException;

/**
 * Description of file
 *
 * @author user
 */
class File {

    // @todo chmod support, exceptions n shit
    public static function create($path, $filename, $chmod = null)
    {
        // See if file already exists
        if(!is_file($path . $filename)) {
            fclose(fopen($path . $filename,"x")); //create the file and close it
            return true;
        }

        return false;
    }

    public static function exists($path)
    {
        return is_file($path);
    }

    public static function append($path, $string)
    {
        $handle = fopen($path, 'a');

        if (!$handle) {
            throw new FileAccessException();
        }

        if (fwrite($handle, $string)) {
            fclose($handle);
            return true;
        }

        return false;
    }

    public static function update($path, $contents)
    {
        $handle = fopen($path, 'w+');

        if (!$handle) {
            throw new FileAccessException();
        }

        fwrite($handle, $contents);

        return true;
    }

    public static function delete($path)
    {
        $path = realpath($path);

        if (!is_readable($path)) {
            throw new FileAccessException();
        }

        unlink($path);

        return true;
    }

    public static function read($path, $to_buffer = false)
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            throw new FileAccessException();
        }

        if ($to_buffer) {
            $content = readfile($path);
        } else {
            $content = file_get_contents($path);
        }


        fclose($handle);
        return $content;
    }

    // prepend
    // if true = pointer to beginning of the file
    // false = pointer to end of the file
    public static function open($path, $lock = true, $prepend = false)
    {
        if ($prepend) {
            $handle = fopen($path, 'x+');
        } else {
            $handle = fopen($path, 'a+');
        }

        if (!$handle) {
            throw new FileAccessException('No handle');
        }

        if ($lock) {
            if (!flock($handle, LOCK_SH)) {
                throw new FileAccessException('File is locked!');
            }
        }

        return $handle;

   }

   public static function close($handle)
   {
       if (!flock($handle, LOCK_SH | LOCK_NB)) {
            flock($handle, LOCK_UN);
        }

        return fclose($handle);
   }
}