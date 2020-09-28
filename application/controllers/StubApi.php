<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

/**
 * StubApi class
 * 外部システムとのAPI連携が必要な機能を開発するために、スタブとして開発段階で利用するAPIクラスです。
 * 外部システムのAPIが完成したら不要になります。
 *
 * @author    Yu-Yamaguchi
 */
class StubApi extends RestController {

    function __construct()
    {
        parent::__construct();
    }

    public function auth_post()
    {
        // パラメータを変数にセット
        $id = $this->post('id');
        $sys_id = $this->post('sys_id');

        // 固定で返却する値
        $response_data = array(
            'results'=> array(
                'user_info'=> array(
                    'user_id'=> 1234,
                    'user_name'=> 'ほげ太郎',
                    'email'=> 'foo.bar@foo.bar.jp'
                )
            )
        );

        // defaultがjson形式のレスポンスとなるためxmlに変更
        $this->response->format = 'xml';
        $this->response($response_data, 200);
    }
}