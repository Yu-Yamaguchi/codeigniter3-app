<?php

/**
 * Login class
 * ログイン処理関連のControllerです。
 */
class Login extends MY_Controller {

    // 全ての利用者がアクセス可能なControllerとして定義
    protected $access = "*";

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
    public function show ()
    {
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
        $data['form'] = $this->input->post();

        $login_id = $this->input->post('login_id');
        $pass = $this->input->post('pass');

        $this->form_validation->set_rules('login_id', 'ログインID', 'trim|required');
        $this->form_validation->set_rules('pass', 'パスワード', 'trim|required');

        if ($this->form_validation->run()) {
            // エラーが無い場合、ログイン認証処理を実行
            $this->load->model('auth_model', 'auth');
            if ($this->auth->authenticate($login_id, $pass)) {
                // ログイン認証OKの情報をセッションに格納
                $this->session->set_userdata(SESS_LOGGED_IN, true);
                // ログイン認証が通ったユーザ情報をセッションに格納
                $this->session->set_userdata($this->auth->get_user());
                // メニュー画面に遷移（redirect）
                redirect("gmo/menu"); 
            } else {
                $this->session->set_flashdata('login_check_error', 'ログイン認証に失敗しました。');
            }
        }

        // エラーの場合
        $data['title'] = "ログイン";
        $this->load->view('templates/header', $data);
        $this->load->view('pages/login', $data);
        $this->load->view('templates/footer', $data);
    }
}
