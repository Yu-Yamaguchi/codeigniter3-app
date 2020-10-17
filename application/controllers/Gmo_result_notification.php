<?php

/**
 * GMOから呼び出される結果通知プログラム用のコントローラです。
 */
class Gmo_result_notification extends MY_Controller {

    // GMOから呼び出される結果通知プログラムなので誰からでもアクセス可能と定義
    // グローバルIPなどが分かるなら、そのIPで制限掛けても良いか。
    protected $access = "*";

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('url_helper');

        $this->load->model('Gmo_result_notification_logs_model', 'log_model');
        $this->load->model('gmo_api_model', 'api_model');
    }

    /**
     * 決済関連の結果通知プログラムから呼び出されるfacade
     */
    public function payment_facade ()
    {
        $param = $this->input->post();

        log_message('debug', '******* call payment_facade');
        log_message('debug', print_r($param, true));

        // 結果通知プログラムで受け取った情報をログテーブルに登録
        $this->log_model->save_post_param_log($param);

        // 決済方法により適切な処理を行う
        $this->api_model->result_notification($param);
    }
}
