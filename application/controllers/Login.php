<?php

/**
 * Login class
 * ログイン処理関連のControllerです。
 */
class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('html');
        $this->load->helper('form');
        $this->load->helper('url_helper');

        $this->load->library('form_validation');
        
    }

    /**
     * ログイン画面を表示します。
     */
    public function show ($page = 'login')
    {
        if (! file_exists(APPPATH.'views/pages/'.$page.'.php'))    
        {
            show_404();
        }

        $data['title'] = "ログイン";

        $data['form']['login_id'] = '';
        $data['form']['pass'] = '';

        $this->load->view('templates/header', $data);
        $this->load->view('pages/login', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * ログイン処理を行います。
     * - 入力項目のValidation
     * - 外部システムのAPIを実行し認証
     */
    public function login ()
    {
        $data['form'] = $this->input->post();

        $this->form_validation->set_rules('login_id', 'ログインID', 'required');
        $this->form_validation->set_rules('pass', 'パスワード', 'required');

        if($this->form_validation->run()){
            // エラーが無い場合
            // 認証システムでログイン処理
            $this->login_api($this->input->post('login_id'), $this->input->post('pass'));

            // redirect("apply/home");
        }else{
            // エラーの場合
            $data['title'] = "ログイン";
            $this->load->view('templates/header', $data);
            $this->load->view('pages/login', $data);
            $this->load->view('templates/footer', $data);
        }
    }

    /**
     * 認証システムのAPIを実行して認証処理を行います。
     */
    private function login_api ($loginId, $pass) {
        // パラメータの設定
        $arrayParam = array(
            'id' => $loginId,
            'pass' => $pass,
            'sys_id' => 'hoge'
        );

        // リクエストコネクションの設定
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arrayParam));
        curl_setopt($curl, CURLOPT_URL, 'http://192.168.33.10/stubapi/auth?format=xml');

        // リクエスト送信
        $response = curl_exec($curl);
        $curlinfo = curl_getinfo($curl);
        curl_close($curl);

        var_dump($response);

        // レスポンスチェック
        if($curlinfo['http_code'] != 200){
            throw new Exception('認証システムのAPI実行が失敗しました。 : ' . $curlinfo['http_code']);
        }
    }
}
