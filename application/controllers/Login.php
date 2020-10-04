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
        log_message('debug', 'call Login#show()');
        if (! file_exists(APPPATH.'views/pages/'.$page.'.php'))    
        {
            show_404();
        }

        $this->session->sess_destroy();

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
        log_message('debug', 'call Login#login()');
        $data['form'] = $this->input->post();

        $login_id = $this->input->post('login_id');
        $pass = $this->input->post('pass');

        $this->form_validation->set_rules('login_id', 'ログインID', 'required');
        $this->form_validation->set_rules('pass', 'パスワード', 'required');

        if ($this->form_validation->run()) {
            // エラーが無い場合
            if ($this->login_api_check($login_id, $pass)) {
                redirect("gmo/menu"); 
            }
        }

        // エラーの場合
        $data['title'] = "ログイン";
        $this->load->view('templates/header', $data);
        $this->load->view('pages/login', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * 認証システムのAPIを実行して認証処理を行います。
     */
    public function login_api_check ($loginId, $pass) {
        log_message('debug', 'call Login#login_api_check()');
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
        curl_setopt($curl, CURLOPT_URL, base_url().'stub_api/auth?format=xml');

        // リクエスト送信
        $response = curl_exec($curl);
        $curlinfo = curl_getinfo($curl);

        curl_close($curl);

        // ログイン認証 成否のリターン
        if($curlinfo['http_code'] != 200){
            $this->session->set_flashdata('login_check_error', 'ログイン認証に失敗しました。');
            return false;
        } else {
            $session_data = json_decode(json_encode(simplexml_load_string($response)),true);
            $this->session->set_userdata($session_data);
            return true;
        }
    }
}
