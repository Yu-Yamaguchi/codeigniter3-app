<?php

class Gmo_result_notification_test extends TestCase
{
    /**
     * @test
     */
    public function GMOからの結果通知プログラム呼び出しが正常に動作すること()
    {
        // $this->warningOff();
        // set_is_cli(false);
        $post_param = array(
            'PayType'=>'0',
            'ShopID'=>'tshop99999999',
            'ShopPass'=>'**********',
            'AccessID'=>'3fcf502455bb9511567aeada6c20d6e3',
            'AccessPass'=>'********************************',
            'OrderID'=>'12345OR20201011131110',
            'Status'=>'CAPTURE',
            'JobCd'=>'CAPTURE',
            'Amount'=>'10000',
            'Tax'=>'1000',
            'Currency'=>'JPN',
            'Forward'=>'2a99662',
            'Method'=>'1',
            'PayTimes'=>'',
            'TranID'=>'2010111310111111111111811578',
            'Approve'=>' 019292',
            'TranDate'=>'20201011131126',
            'ErrCode'=>'',
            'ErrInfo'=>''
        );

        // これでモックを利用すると、
        // RuntimeException: The model name you are loading is the name of a resource that is already being used: log_model
        // というエラーが発生してしまう。

        // $this->request->setCallablePreConstructor(
        //     function () {
        //         // Get mock object
        //         $log_model = $this->getDouble('Gmo_result_notification_logs_model',['save_post_param_log' => ''], false);
        //         // Inject mock object
        //         load_class_instance('log_model', $log_model);
        //     }
        // );

        // これでモックを利用しても、モックで動作せず、実物が動作してしまう。。。
        // $this->request->setCallable(
        //     function ($CI) {
        //         $log_model = $this->getDouble('Gmo_result_notification_logs_model', ['save_post_param_log' => '']);
        //         $CI->log_model = $log_model;
        //     }
        // );

        // $this->request->setCallable(
        //     function ($CI) {
        //         $api_model = $this->getDouble('gmo_api_model', ['result_notification' => '']);
        //         $CI->api_model = $api_model;
        //     }
        // );


        $output = $this->request('POST', 'gmo_result_notification/payment_facade', $post_param);
        $this->assertResponseCode(200);

        $this->assertTrue(true);
        // set_is_cli(true);
        // $this->warningOn();
    }
}
