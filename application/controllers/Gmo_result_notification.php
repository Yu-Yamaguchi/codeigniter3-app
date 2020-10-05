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
        $param = $this->input->post();

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

        // クレジットカード決済が正常に完了している場合、決済後会員登録の手続きをする。
        // 決済後会員登録を行うことで、ユーザーがGMOカード会員登録の処理をリンクタイプPlusの画面で
        // 意図的に行う必要がない。（もちろん勝手に登録しちゃまずいと思うので、なんらかの方法で
        // ユーザーに確認して同意しておく必要はあると思います。）
        if ($data['Status'] == 'CAPTURE' or $data['Status'] == 'SALES') {
            $order_id = $data['OrderID'];

            // 注文IDの先頭4桁を会員IDにしているのでそこから抽出
            // 本来はDBに登録されている注文IDから会員を識別するなどちゃんとした処理は必要ですよね。
            $member_id = intval(substr($order_id, 1, 4));

            log_message('debug', print_r($data, true));
            log_message('debug', 'order_id:'.$order_id.'/member_id:'.$member_id);

            $this->gmo_payment_save_member($member_id);
            $this->gmo_payment_traded_card($order_id, $member_id);
        }
    }

     /**
     * 会員登録を行います。
     */
    private function gmo_payment_save_member($member_id) {
        $param = [
            'SiteID'           => SITE_ID,
            'SitePass'         => SITE_PASS,
            'MemberID'         => $member_id,
            'MemberName'       => 'テスト　太郎'
        ];

        // リクエストコネクションの設定
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        curl_setopt($curl, CURLOPT_URL, 'https://pt01.mul-pay.jp/payment/SaveMember.idPass');

        // リクエスト送信
        $response = curl_exec($curl);
        $curlinfo = curl_getinfo($curl);
        curl_close($curl);

        // TODO：この辺で処理結果を正しく判別してあげる

        return true;
    }

    /**
     * 決済後会員登録の処理を行います。
     */
    private function gmo_payment_traded_card($order_id, $member_id) {

        $param = [
            'ShopID'           => SHOP_ID,
            'ShopPass'         => SHOP_PASS,
            'OrderID'          => $order_id,
            'SiteID'           => SITE_ID,
            'SitePass'         => SITE_PASS,
            'MemberID'         => $member_id,
            'SeqMode'          => '0',
            'DefaultFlag'      => '1', // 洗替・継続課金フラグは、継続課金は利用しないが、洗替の機能は利用する。という場合にも`1`をセットする。
            'UseSiteMaskLevel' => '1'
        ];


        // リクエストコネクションの設定
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        curl_setopt($curl, CURLOPT_URL, 'https://pt01.mul-pay.jp/payment/TradedCard.idPass');

        // リクエスト送信
        $response = curl_exec($curl);
        $curlinfo = curl_getinfo($curl);
        curl_close($curl);

        // TODO：この辺で処理結果を正しく判別してあげる

        return true;
    }
}
