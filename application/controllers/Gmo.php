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
        log_message('debug', 'call Gmo#menu()');
        $user = $this->session->userdata('results')['user_info'];

        $this->load->view('templates/header');
        $this->load->view('pages/menu');
        $this->load->view('templates/footer');
    }

    // GMOカード会員編集画面への遷移情報を生成して案内ページへ遷移します。
    public function member()
    {
        log_message('debug', 'call Gmo#member()');
        // リンクタイプPlusの会員カード編集画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->get_member_url();
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/member', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * GMO決済画面への遷移情報を生成して案内ページへ遷移します。
     * 特に会員IDが登録済みかどうかに関わらず、無条件に会員IDを指定します。
     */
    public function payment()
    {
        log_message('debug', 'call Gmo#payment()');
        // リンクタイプPlusの決済画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->get_payment_url();
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/payment', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * GMO決済画面への遷移情報を生成して案内ページへ遷移します。
     * 会員IDが登録済みの場合のみ会員IDを指定します。
     */
    public function payment_mem()
    {
        log_message('debug', 'call Gmo#payment()');
        // リンクタイプPlusの決済画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->get_payment_url(true);
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/payment', $data);
        $this->load->view('templates/footer', $data);
    }

    // GMO決済画面へのURLを生成して返却します。
    private function get_payment_url($flg_conf_member = false)
    {
        log_message('debug', 'call Gmo#get_payment_url()');
        // 決済URL取得API
        $url = 'https://pt01.mul-pay.jp/payment/GetLinkplusUrlPayment.json';

        // GMOの決済で指定するオーダーIDは一意でなければいけない
        $orderId = 'OR' . date('YmdHis') . rand();

        $user = $this->session->userdata('results')['user_info'];

        // json形式のパラメータを生成するための配列パラメータ定義
        $arr_param = array(
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
                'JobCd'=> 'CAPTURE',
                'Method'=> '1'
            )
        );

        // GMO会員IDのパラメータを設定するか判別してパラメータに追加
        $arr_member_id = array(
            'credit'=> array(
                'MemberID'=> $user['user_id']  // GMO会員IDの指定
            )
        );

        if ($flg_conf_member) {
            if ($this->gmo_exists_member($user['user_id'])) {
                $arr_param = array_merge_recursive($arr_param, $arr_member_id);
            }
        } else {
            // $flg_conf_member=falseの場合は無受験に会員IDを指定する。
            $arr_param = array_merge_recursive($arr_param, $arr_member_id);
        }

        // 配列→json変換
        $param = json_encode($arr_param);
        return $this->get_gmo_linkurl($url, $param);
    }

    // GMOカード会員編集画面への遷移URLを生成して返却します。
    private function get_member_url()
    {
        log_message('debug', 'call Gmo#get_member_url()');
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
        return $this->get_gmo_linkurl($url, $param);
    }

    // プロトコルタイプのAPIを利用し、キー型のパラメータ指定方法によるAPI実行結果から、URL情報だけを抽出して返却します。
    private function get_gmo_linkurl($url, $param) {
        log_message('debug', 'call Gmo#get_gmo_linkurl()');
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

        $resJson = json_decode($response, true);

        // LinkUrlが取得できなければエラーとして扱う
        if (!array_key_exists('LinkUrl', $resJson)) {
            $http_stscd = $curlinfo['http_code'];
            
            log_message('error', print_r($response ,true));

            // GMO エラーコードが返却されていればセットする
            $gmo_errcd = '';
            parse_str($response, $data);
            if(array_key_exists('ErrCode', $data)) {
                $gmo_errcd = $data['ErrCode'];
            }

            $errmsg = <<< EOD
            $url . ' API実行が失敗しました。 : '
            'HTTPステータスコード ： ' . $http_stscd
            'GMOエラーコード ： ' . $gmo_errcd
            EOD;
            
            throw new Exception($errmsg);
        }

        // URL取得APIの実行結果からリンク情報を取得し返却
        return $resJson['LinkUrl'];
    }

    
    /**
     * 指定されたGMO会員IDがGMOサイトに存在するか確認し、
     * その結果をtrue / falseで返却します。
     */
    private function gmo_exists_member($member_id) {
        log_message('debug', 'call Gmo#gmo_exists_member()');
        $param = [
            'SiteID'           => SITE_ID,
            'SitePass'         => SITE_PASS,
            'MemberID'         => $member_id
        ];

        // リクエストコネクションの設定
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        curl_setopt($curl, CURLOPT_URL, 'https://pt01.mul-pay.jp/payment/SearchMember.idPass');

        // リクエスト送信
        $response = curl_exec($curl);
        $curlinfo = curl_getinfo($curl);
        curl_close($curl);

        // 会員IDが見つかればtrue / 正しく見つからない「E01390002」場合はfalseを返却
        $http_stscd = $curlinfo['http_code'];
        parse_str( $response, $data );

        if($http_stscd != 200) {
            $gmo_errcd = '';
            if (array_key_exists('ErrCode', $data)) {
                $gmo_errcd = $data['ErrCode'];
            }
            $errmsg = <<< EOD
            $url . 'SearchMember.idPass API実行が失敗しました。 : '
            'HTTPステータスコード ： ' . $http_stscd
            'GMOエラーコード ： ' . $gmo_errcd
            EOD;
            throw new Exception($errmsg);
        }

        $err_info = '';
        if (array_key_exists('ErrInfo', $data)) {
            $err_info = $data['ErrInfo'];
            if ($err_info == 'E01390002'){
                return false;
            } else {
                throw new Exception('SearchMember.idPassのErrInfoで予期せぬリターン：' . $err_info);
            }
        }
        return true;
    }
}
