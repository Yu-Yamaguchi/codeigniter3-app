<?php

/**
 * Controllerの処理実行に対してログ出力をhookするクラス。
 * SpringのAOPみたいなことがしたくて作ってみた。
 */
class Log {

    protected $CI;
    
    public function __construct() {
        // CIオブジェクトを取得
        $this->CI =& get_instance();

        $this->CI->load->helper('form');
        $this->CI->load->helper('url_helper');
    }

    /**
     * Controllerのメソッド実行後に実行された処理内容などをログ出力する。
     */
    function log_called_function() {
        // ログ出力用の変数セット
        $controller_name = $this->CI->router->fetch_class();
        $method_name = $this->CI->router->fetch_method();
        $client_ip_address = $this->CI->input->ip_address();
        $user_agent = $this->CI->input->user_agent();

        // ログメッセージを整形
        $logmsg = array(
            'controller_name'=> $controller_name,
            'method_name'=> $method_name,
            'client_ip_address'=> $client_ip_address,
            'user_agent'=> $user_agent
        );

        // ログ出力
        log_message('debug', json_encode($logmsg));
    }
}