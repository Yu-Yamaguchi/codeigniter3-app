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
        
        $user = $this->session->userdata('results')['user_info'];
        $this->load->model('gmo_api_model');
        $this->gmo_api_model->init($user);
    }

    // メニュー画面を表示します。
    public function menu ($page = 'menu')
    {
        $this->load->view('templates/header');
        $this->load->view('pages/menu');
        $this->load->view('templates/footer');
    }

    // GMOカード会員編集画面への遷移情報を生成して案内ページへ遷移します。
    public function member()
    {
        // リンクタイプPlusの会員カード編集画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->gmo_api_model->get_member_url();
        
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
        // リンクタイプPlusの決済画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->gmo_api_model->get_payment_url();
        
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
        // リンクタイプPlusの決済画面へ遷移するURLをセットして画面表示
        $data['form']['action'] = $this->gmo_api_model->get_payment_url(true);
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/payment', $data);
        $this->load->view('templates/footer', $data);
    }
}
