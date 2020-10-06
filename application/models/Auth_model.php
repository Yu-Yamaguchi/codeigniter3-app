<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 認証のモデル。
 */
class Auth_model extends CI_Model {

	private $_user = array();

	/**
	 * APIを利用してログイン認証を行う。
	 * true: 認証OK
	 * false: 認証NG
	 */
	public function authenticate($login_id, $pass)
	{
		// パラメータの設定
        $arrayParam = array(
            'id' => $login_id,
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
            return false;
        } else {
            $this->_user = json_decode(json_encode(simplexml_load_string($response)),true);
            return true;
        }
	}

	/**
	 * ログインユーザ情報を取得する。
	 */
	public function get_user()
	{
		return $this->_user;
	}

}