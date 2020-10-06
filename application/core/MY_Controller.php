<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 共通のController独自クラス。
 * MY_という名前にしないとControllerクラスで継承するときに見つけてくれない。。。（そういうもん？）
 */
class MY_Controller extends CI_Controller {

	/**
	 * '*' 全てのユーザーがアクセス可能なController
	 * '@' 認証済みユーザーのみがアクセス可能なController
	 * @var string
	 */
	protected $access = '@';

	public function __construct()
	{
		parent::__construct();

		// Controllerがインスタンス化される際に、必ずログインチェックを行う。
		$this->login_check();
	}

	/**
	 * セッション情報からユーザーがログイン済みかを判別して、
	 * ログイン済みでない場合はログイン画面へリダイレクトする。
	 */
	public function login_check()
	{
		if ($this->access != '*') 
		{
			if (! $this->session->userdata(SESS_LOGGED_IN)) {
				$this->load->helper('url');
				redirect('login/show');
			}
		}
	}
}