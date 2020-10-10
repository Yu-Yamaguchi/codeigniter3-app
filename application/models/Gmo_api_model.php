<?php

/**
 * GMO-PGへのAPI操作を責務とするモデル
 */
class Gmo_api_model extends CI_Model {

    /** セッションに格納されたログインユーザ情報 */
    private $user = null;

    /** curl API実行用オブジェクト */
    public $curl = null;

    public function __construct() {
        parent::__construct();

        // curlでのAPI実行用ライブラリのロードとインスタンス変数へのセット
        $this->load->library('Curl_request');
        $this->curl = $this->curl_request;
    }

    /**
     * API操作に必要なユーザ情報をセットする
     */
    public function init($u) {
        $this->user = $u;
    }

    /**
     * GMO決済画面へのURLを生成して返却する
     */
    public function get_payment_url($flg_conf_member = false)
    {
        // 決済URL取得API
        $url = 'https://pt01.mul-pay.jp/payment/GetLinkplusUrlPayment.json';

        // GMOの決済で指定するオーダーIDは一意でなければいけない（注文ID先頭4桁を会員IDにした。）
        $orderId = str_pad($this->user['user_id'], 4, '0', STR_PAD_LEFT) . 'OR' . date('YmdHis');

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
                'Tax'=> 1000,
                'PayMethods'=> ['credit']
            ),
            'credit'=> array(
                'JobCd'=> 'CAPTURE',
                'Method'=> '1'
            )
        );

        // GMO会員IDのパラメータを設定するか判別してパラメータに追加
        $arr_member_id = array(
            'credit'=> array(
                'MemberID'=> $this->user['user_id']  // GMO会員IDの指定
            )
        );

        if ($flg_conf_member) {
            if ($this->gmo_exists_member($this->user['user_id'])) {
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

    /**
     * GMO決済画面へのURLを生成して返却する
     */
    public function get_secure_payment_url($flg_conf_member = false)
    {
        // 決済URL取得API
        $url = 'https://pt01.mul-pay.jp/payment/GetLinkplusUrlPayment.json';

        // GMOの決済で指定するオーダーIDは一意でなければいけない（注文ID先頭4桁を会員IDにした。）
        $orderId = str_pad($this->user['user_id'], 4, '0', STR_PAD_LEFT) . 'OR' . date('YmdHis');

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
                'Tax'=> 1000,
                'PayMethods'=> ['credit']
            ),
            'credit'=> array(
                'JobCd'=> 'CAPTURE',
                'Method'=> '1',
                'TdFlag'=> '2', // 3Dセキュア認証を契約に従って実施
                'Tds2Type'=> '1'
            )
        );
        // 3Dセキュア認証テスト用カード
        // 3DS1.0用　＝　https://faq.gmo-pg.com/service/detail.aspx?id=1681&a=102&isCrawler=1
        // 3DS2.0用　＝　https://faq.gmo-pg.com/service/detail.aspx?id=2379&a=102&isCrawler=1

        // GMO会員IDのパラメータを設定するか判別してパラメータに追加
        $arr_member_id = array(
            'credit'=> array(
                'MemberID'=> $this->user['user_id']  // GMO会員IDの指定
            )
        );

        if ($flg_conf_member) {
            if ($this->gmo_exists_member($this->user['user_id'])) {
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

    /**
     * GMOカード会員編集画面への遷移URLを生成して返却する
     */
    public function get_member_url()
    {
        // カード編集URL取得API
        $url = 'https://pt01.mul-pay.jp/payment/GetLinkplusUrlMember.json';

        // json形式のパラメータを生成するための配列パラメータ定義
        $arrayParam = array(
            'geturlparam'=> array(
                'ShopID'=> SHOP_ID,
                'ShopPass'=> SHOP_PASS,
                'TemplateNo'=> '1'
            ),
            'configid'=> 'test01',
            'member'=> array(
                'Cardeditno'=> 'CardEdit'.$this->user['user_id'],
                'MemberID'=> $this->user['user_id']  // GMO会員IDの指定
            )
        );

        // 配列→json変換
        $param = json_encode($arrayParam);
        return $this->get_gmo_linkurl($url, $param);
    }

    /**
     * 指定されたGMO会員IDがGMOサイトに存在するか確認し、
     * その結果をtrue / falseで返却する
     */
    public function gmo_exists_member($member_id) {
        $param = [
            'SiteID'           => SITE_ID,
            'SitePass'         => SITE_PASS,
            'MemberID'         => $member_id
        ];

        // リクエストコネクションの設定
        $this->curl->init('https://pt01.mul-pay.jp/payment/SearchMember.idPass');
        $this->curl->set_option(CURLOPT_POST, true);
        $this->curl->set_option(CURLOPT_RETURNTRANSFER, true);
        $this->curl->set_option(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->curl->set_option(CURLOPT_POSTFIELDS, $param);

        // リクエスト送信
        $response = $this->curl->execute();
        $curlinfo = $this->curl->get_info();
        $this->curl->close();

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

    /**
     * プロトコルタイプのAPIを利用し、キー型のパラメータ指定方法によるAPI実行結果から、URL情報だけを抽出して返却する。
     * API実行に失敗した場合はExceptionをthrow
     */
    private function get_gmo_linkurl($url, $param) {

        // リクエストコネクションの設定
        $this->curl->init($url);
        $this->curl->set_option(CURLOPT_POST, true);
        $this->curl->set_option(CURLOPT_RETURNTRANSFER, true);
        $this->curl->set_option(CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
        $this->curl->set_option(CURLOPT_CUSTOMREQUEST, 'POST');
        $this->curl->set_option(CURLOPT_POSTFIELDS, $param);

        // リクエスト送信
        $response = $this->curl->execute();
        $curlinfo = $this->curl->get_info();
        $this->curl->close();

        log_message('debug', 'kiteru?');
        log_message('debug', $response);

        $resJson = json_decode($response, true);

        // LinkUrlが取得できなければエラーとして扱う
        if (!array_key_exists('LinkUrl', $resJson)) {
            $http_stscd = $curlinfo['http_code'];
            
            $errmsg = <<< EOD
            $url . ' API実行が失敗しました。 : '
            'HTTPステータスコード ： ' . $http_stscd
            'GMO Error : ' . $response
            EOD;

            throw new Exception($errmsg);
        }

        // URL取得APIの実行結果からリンク情報を取得し返却
        return $resJson['LinkUrl'];
    }
}