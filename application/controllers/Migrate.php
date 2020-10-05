<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

    function __construct()
    {   
        parent::__construct();
        if(!$this->input->is_cli_request()) {
            show_404();
            exit;
        }   
        $this->load->library('migration');
    }   

    function current()
    {   
        if ($this->migration->current()) {
            log_message('error', 'Migration Success.');
        } else {
            log_message('error', $this->migration->error_string());
        }   
    }   

    function rollback($version)
    {   
        if ($this->migration->version($version)) {
            log_message('error', 'Migration Success.');
        } else {
            log_message('error', $this->migration->error_string());
        }   
    }   

    function latest()
    {   
        if ($this->migration->latest()) {
            log_message('error', 'Migration Success.');
        } else {
            log_message('error', $this->migration->error_string());
        }   
    }

}