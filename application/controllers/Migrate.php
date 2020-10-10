<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration用のControllerクラス
 */
class Migrate extends MY_Controller {

    /**
     * 全ての利用者がアクセス可能なControllerとして定義
     * 一応コンストラクタ内でコマンド実行じゃなければエラーにするようにしている。
     */
    protected $access = "*";

    function __construct()
    {   
        parent::__construct();
        if(!$this->input->is_cli_request()) {
            show_404();
            exit;
        }   
        $this->load->library('migration');
    }   

    /**
     * currentバージョンにマイグレーションする
     */
    function current()
    {   
        if ($this->migration->current()) {
            log_message('error', 'Migration Success.');
        } else {
            log_message('error', $this->migration->error_string());
        }   
    }   

    /**
     * 指定されたバージョンにロールバックする
     */
    function rollback($version)
    {   
        if ($this->migration->version($version)) {
            log_message('error', 'Migration Success.');
        } else {
            log_message('error', $this->migration->error_string());
        }   
    }   

    /**
     * 最新バージョンにマイグレーションする
     */
    function latest()
    {   
        if ($this->migration->latest()) {
            log_message('error', 'Migration Success.');
        } else {
            log_message('error', $this->migration->error_string());
        }   
    }

}