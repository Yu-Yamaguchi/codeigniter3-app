<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

/**
 * Stub_api class
 * 外部システムとのAPI連携が必要な機能を開発するために、スタブとして開発段階で利用するAPIクラスです。
 * 外部システムのAPIが完成したら不要になります。
 *
 * @author    Yu-Yamaguchi
 */
class Stub_api extends RestController {

    function __construct()
    {
        parent::__construct();
    }

    public function auth_post()
    {
        // パラメータを変数にセット
        $id = $this->post('id');
        $pass = $this->post('pass');
        $sys_id = $this->post('sys_id');

        $http_status = 400;

        if ($pass != 'test') {
            $http_status = 200;
        }

        // 固定で返却する値
        $response_data = array(
            'results'=> array(
                'user_info'=> array(
                    'user_id'=> $id,
                    'user_name'=> 'ほげ太郎'
                )
            )
        );

        // defaultがjson形式のレスポンスとなるためxmlに変更
        $this->response->format = 'xml';
        $this->response($response_data, $http_status);
    }
}
