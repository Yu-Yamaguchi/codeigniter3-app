<?php

/**
 * Gmo_result_notification_logs_modelクラスのテスト
 */
class Gmo_result_notification_logs_model_test extends TestCase
{
    private $curl_mock = null;

    /**
     * テストクラス内の初回（１回）のみ実行する初期化処理
     */
    public static function setUpBeforeClass():void
    {
        // テスト用DBを初期化（migration）して利用
        $CI =& get_instance(); 
        $CI->load->database('phpunit');
        $CI->load->library('migration');
        $CI->migration->current();
    }

    /**
     * テスト初期処理（各テストメソッドの実行前処理）
     */
    public function setUp():void
    {
        $this->resetInstance();

        $this->CI->load->model('Gmo_result_notification_logs_model');
        $this->obj = $this->CI->Gmo_result_notification_logs_model;
    }

    /**
     * テストクラス内の最後（１回）のみ実行する終了処理
     */
    public static function tearDownAfterClass():void
    {
        $CI =& get_instance(); 
        $CI->load->database('phpunit');
        $CI->load->library('migration');
        $CI->migration->version(0);
    }

    public function test_ログの登録が正常に完了すること():void
    {
        // public $id;
        // public $pay_method;
        // public $parameter;
        // public $create_date;
        $this->obj->id = 1;
        $this->obj->pay_method = 'credit';
        $this->obj->parameter = '{"aaa": "bbb", "ccc": "ddd"}';
        $this->obj->create_date = date('Y/m/d h:m:s');

        $this->obj->insert_log();

        $this->assertTrue(true);
    }

}