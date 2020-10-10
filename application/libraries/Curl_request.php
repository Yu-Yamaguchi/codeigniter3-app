<?php

defined('BASEPATH') OR exit('No direct script access allowed');

interface Http_request
{
    public function set_option($name, $value);
    public function execute();
    public function get_info();
    public function close();
}

class Curl_request implements Http_request
{
    private $handle = null;

    public function __construct() {
    }

    public function init($url) {
        $this->handle = curl_init($url);
    }

    public function set_option($name, $value) {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute() {
        return curl_exec($this->handle);
    }

    public function get_info() {
        return curl_getinfo($this->handle);
    }

    public function close() {
        curl_close($this->handle);
    }
}