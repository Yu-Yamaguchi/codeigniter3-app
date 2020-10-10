<?php

/**
 * Gmo_result_notification_logs_modelクラスのテスト
 */
class Gmo_result_notification_logs_model_test extends TestCase
{
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

    /**
     * @test
     */
    public function ログの登録が正常に完了すること():void
    {
        $this->obj->id = 999;
        $this->obj->pay_method = 'credit';
        $this->obj->parameter = '{"aaa": "bbb", "ccc": "ddd"}';
        $this->obj->create_date = date('Y-m-d h:m:s');

        $this->obj->insert_log();
        $this->assertTrue(true); // ここまできたらOK
    }

    /**
     * @test
     * @depends ログの登録が正常に完了すること
     */
    public function 登録済みのログが削除できること():void
    {
        $this->obj->id = 999;
        $this->obj->delete_log();
        $this->assertTrue(true); // ここまできたらOK
    }

    /**
     * @test
     * @depends 登録済みのログが削除できること
     */
    public function 登録した順序の降順で取得できること():void
    {
        $insert_values = array(
            array(
                'id'=> 1,
                'pay_method'=> 'credit',
                'parameter'=> '{"aaa": "bbb", "ccc": "ddd"}',
                'create_date'=> date('Y-m-d h:m:s')
            ),
            array(
                'id'=> 2,
                'pay_method'=> 'cvs',
                'parameter'=> '{"eee": "ffff", "gggg": "hhh"}',
                'create_date'=> date('Y-m-d h:m:s')
            ),
            array(
                'id'=> 3,
                'pay_method'=> 'paypay',
                'parameter'=> '{"iii": "jjj", "kkk": "lll"}',
                'create_date'=> date('Y-m-d h:m:s')
            )
        );

        foreach ($insert_values as $value) {
            $this->obj->id = $value['id'];
            $this->obj->pay_method = $value['pay_method'];
            $this->obj->parameter = $value['parameter'];
            $this->obj->create_date = $value['create_date'];

            $this->obj->insert_log();
        }

        // ログを取得
        $logs = $this->obj->get_last_ten_logs();

        // 降順に並び替え
        $reverse_values = array_reverse($insert_values);

        for ($i = 0; $i < count($reverse_values); $i++) { 
            $this->assertEquals($reverse_values[$i]['id'], $logs[$i]->id);
            $this->assertEquals($reverse_values[$i]['pay_method'], $logs[$i]->pay_method);
            $this->assertEquals($reverse_values[$i]['parameter'], $logs[$i]->parameter);
            $this->assertEquals($reverse_values[$i]['create_date'], $logs[$i]->create_date);
        }
    }

}