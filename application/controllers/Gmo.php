<?php

/**
 * GMO-PGの決済を利用するサンプルのためのControllerです。
 */
class Gmo extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('url_helper');

        $this->load->library('form_validation');
        
    }

    // メニュー画面を表示します。
    public function menu ($page = 'menu')
    {
        $user = $this->session->userdata('results')['user_info'];

        $this->load->view('templates/header');
        $this->load->view('pages/menu');
        $this->load->view('templates/footer');
    }

    // GMOカード会員編集画面への遷移情報を生成して案内ページへ遷移します。
    public function member()
    {
        // リンクタイプPlusの会員カード編集画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->get_member_url();
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/member', $data);
        $this->load->view('templates/footer', $data);
    }

    // GMO決済画面への遷移情報を生成して案内ページへ遷移します。
    public function payment()
    {
        // リンクタイプPlusの決済画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->get_payment_url();
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/payment', $data);
        $this->load->view('templates/footer', $data);
    }

    // GMO決済画面へのURLを生成して返却します。
    private function get_payment_url()
    {
        // 決済URL取得API
        $url = 'https://pt01.mul-pay.jp/payment/GetLinkplusUrlPayment.json';

        // GMOの決済で指定するオーダーIDは一意でなければいけない
        $orderId = 'OR' . date('YmdHis') . rand();

        $user = $this->session->userdata('results')['user_info'];

        // json形式のパラメータを生成するための配列パラメータ定義
        $arrayParam = array(
            'geturlparam'=> array(
                'ShopID'=> SHOP_ID,
                'ShopPass'=> SHOP_PASS,
                'TemplateNo'=> '1'
            ),
            'configid'=> 'test01',
            'transaction'=> array(
                'OrderID'=> $orderId,
                'Amount'=> 10000,
                'Tax'=> 1000
            ),
            'credit'=> array(
                'JobCd'=> 'AUTH',
                'Method'=> '1',
                'MemberID'=> $user['user_id']  // GMO会員IDの指定
            )
        );

        // 配列→json変換
        $param = json_encode($arrayParam);
        return $this->call_gmo_protocoltype($url, $param);
    }

    // GMOカード会員編集画面への遷移URLを生成して返却します。
    private function get_member_url()
    {
        // カード編集URL取得API
        $url = 'https://pt01.mul-pay.jp/payment/GetLinkplusUrlMember.json';

        $user = $this->session->userdata('results')['user_info'];

        // json形式のパラメータを生成するための配列パラメータ定義
        $arrayParam = array(
            'geturlparam'=> array(
                'ShopID'=> SHOP_ID,
                'ShopPass'=> SHOP_PASS,
                'TemplateNo'=> '1'
            ),
            'configid'=> 'test01',
            'member'=> array(
                'Cardeditno'=> 'CardEdit'.$user['user_id'],
                'MemberID'=> $user['user_id']  // GMO会員IDの指定
            )
        );

        // 配列→json変換
        $param = json_encode($arrayParam);
        return $this->call_gmo_protocoltype($url, $param);
    }

    // プロトコルタイプのAPIを利用し、キー型のパラメータ指定方法によるAPI実行結果から、URL情報だけを抽出して返却します。
    private function call_gmo_protocoltype($url, $param) {
        // リクエストコネクションの設定
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        curl_setopt($curl, CURLOPT_URL, $url);

        // リクエスト送信
        $response = curl_exec($curl);
        $curlinfo = curl_getinfo($curl);
        curl_close($curl);

        // 何度も同じ会員に対してカード編集URLを取得すると「EZ4136014」が発生するのでいったんチェックをコメントアウト

        // レスポンスチェック
        // if($curlinfo['http_code'] != 200){
        //     throw new Exception($url . ' API実行が失敗しました。 : ' . $curlinfo['http_code']);
        // }

        // // レスポンスのエラーチェック
        // parse_str($response, $data);
        // if(array_key_exists('ErrCode', $data)){
        //     throw new Exception($url . ' API実行が失敗しました。 : ' . $data['ErrCode']);
        // }

        // URL取得APIの実行結果からリンク情報を取得し返却
        $resJson = json_decode($response, true);
        return $resJson['LinkUrl'];
    }
}
