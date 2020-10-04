<?php

/**
 * GMOから呼び出される結果通知プログラム用のコントローラです。
 */
class Gmo_result_notification extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('url_helper');
    }

    /**
     * 決済関連の結果通知プログラムから呼び出されるfacade
     */
    public function payment_facade ()
    {
        log_message('debug', 'call Gmo_result_notification#payment_facade()');
        $param = $this->input->post();
        log_message('debug', print_r($param, true));

        // 決済方法により処理分岐
        switch ($param['PayType']) {
            // 0:クレジット
            case '0':
                $this->result_credit($param);
                break;
            // その他（契約している決済方法に応じて分岐）
            default:
                // ....その他の処理
                break;
        }
    }

    /**
     * 決済方法＝クレジットカード決済の結果を処理します。
     */
    private function result_credit($data) {

    }
}
