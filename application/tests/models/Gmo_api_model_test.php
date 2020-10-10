<?php

/**
 * Gmo_api_modelクラスのテスト
 */
class Gmo_api_model_test extends TestCase
{
    private $curl_mock = null;

    /**
     * テスト初期処理（各テストメソッドの実行前処理）
     */
    public function setUp():void
    {
        $this->resetInstance();
        $this->CI->load->model('Gmo_api_model');
        $this->CI->load->library('Curl_request');
        $this->obj = $this->CI->Gmo_api_model;
    }

    /**
     * Curl_requestクラスのモックオブジェクトを初期化する。
     */
    private function init_curl_mock() {
        // Curl_request.phpで利用しているHttp_requestインターフェースのモックを作成してAPI実行結果をMock化
        $this->curl_mock = $this->getMockBuilder('Curl_request')
                                    ->setMethods(['init','set_option','execute','get_info','close'])
                                    ->getMock();
    }

    public function test_GMO決済リンクUrlが正常に取得できること(): void
    {
        // Mock化したAPIのcurl_exec実行結果を定義しreturnで利用
        $this->init_curl_mock();
        $ret_exec = array(
            'OrderID'=> 'sample-123456789',
            'LinkUrl'=> 'https://[ドメイン]/v2/plus/tshop11223344/checkout/0258d6e9232978d004bf776c26acb435c7bc9eca33b40798a714a9dde2dfe0c5',
            'ProcessDate'=> '20200727142656'
        );
        $this->curl_mock->method('execute')->willReturn(json_encode($ret_exec));
        // MockオブジェクトでCurl_requestへの参照を切り替え
        $this->obj->curl = $this->curl_mock;

        // Gmo_api_modelを初期化（ログインユーザ情報をセット）
        $user = array(
            'user_id'=> 1
        );
        $this->obj->init($user);

        $url = '';
        try {
            $url = $this->obj->get_payment_url();
        } catch(Exception $e) {
            $this->fail('決済URLの取得に失敗 : ' . $e->getMessage());
        } 
        // 決済URLの接続URLが取得できていればOK
        $this->assertGreaterThanOrEqual(0, strpos($url, 'https://stg.link.mul-pay.jp/v2/plus'));
        $this->assertGreaterThanOrEqual(10, strpos($url, 'checkout/'));
    }

    public function test_GMO決済リンクUrl取得失敗の詳細がExceptionで伝播されること(): void
    {
        // Mock化したAPIのcurl_exec実行結果を定義しreturnで利用
        $this->init_curl_mock();
        $ret_exec = array(
            array(
                'ErrCode'=> 'EZ1',
                'ErrInfo'=> 'EZ1004005'
            ),
            array(
                'ErrCode'=> 'EZ1',
                'ErrInfo'=> 'EZ1004001'
            )
        );
        $this->curl_mock->method('execute')->willReturn(json_encode($ret_exec));

        // Mock化したAPIのget_info実行結果を定義しreturnで利用
        $ret_info = array(
            'http_code'=> '400'
        );
        $this->curl_mock->method('get_info')->willReturn($ret_info);

        // MockオブジェクトでCurl_requestへの参照を切り替え
        $this->obj->curl = $this->curl_mock;

        // Gmo_api_modelを初期化（ログインユーザ情報をセット）
        $user = array(
            'user_id'=> 1
        );
        $this->obj->init($user);

        $url = '';
        try {
            $url = $this->obj->get_payment_url();
        } catch(Exception $ex) {
            $err_msg = $ex->getMessage();
            $this->assertMatchesRegularExpression('/API実行が失敗しました。/', $err_msg);
            $this->assertMatchesRegularExpression('/HTTPステータスコード/', $err_msg);
            $this->assertMatchesRegularExpression('/ErrCode/', $err_msg);
            $this->assertMatchesRegularExpression('/ErrInfo/', $err_msg);
            $this->assertMatchesRegularExpression('/400/', $err_msg);
            $this->assertMatchesRegularExpression('/EZ1/', $err_msg);
            $this->assertMatchesRegularExpression('/EZ1004005/', $err_msg);
        } 
    }

    public function test_GMOカード会員編集Urlが正常に取得できること(): void
    {
        // Mock化したAPIのcurl_exec実行結果を定義しreturnで利用
        $this->init_curl_mock();
        $ret_exec = array(
            'Cardeditno'=> 'CardEdit1',
            'LinkUrl'=> 'https://stg.link.mul-pay.jp/v2/plus/tshop99999999/member/7c1987623098alkje1617e7370c6899bb3df87e4dd52079e957c1acb42d5b44f5b67',
            'ProcessDate'=> '20201010143013',
            'WarnList'=> array(
                'warnCode'=> 'EZ4',
                'warnInfo'=> 'EZ4136014'
            )
        );
        $this->curl_mock->method('execute')->willReturn(json_encode($ret_exec));
        // MockオブジェクトでCurl_requestへの参照を切り替え
        $this->obj->curl = $this->curl_mock;

        // Gmo_api_modelを初期化（ログインユーザ情報をセット）
        $user = array(
            'user_id'=> 1
        );
        $this->obj->init($user);

        $url = '';
        try {
            $url = $this->obj->get_member_url();
        } catch(Exception $e) {
            $this->fail('GMOカード会員編集Urlの取得に失敗 : ' . $e->getMessage());
        } 
        // GMOカード会員編集Urlの接続URLが取得できていればOK
        $this->assertGreaterThanOrEqual(0, strpos($url, 'https://stg.link.mul-pay.jp/v2/plus'));
        $this->assertGreaterThanOrEqual(10, strpos($url, 'member/'));
    }

}