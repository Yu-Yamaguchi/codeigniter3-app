<?php

class Login_test extends TestCase
{
    /**
     * @test
     */
    public function ログイン画面_初期画面への遷移ができること()
    {
        // controller名/function名での画面遷移を確認
        $output = $this->request('GET', 'login/show');
        $this->assertStringContainsString('<legend>ログイン</legend>', $output);

        // $route['default_controller'] の設定通り、ルートにアクセスした際もログイン画面に遷移できること
        $output = $this->request('GET', '/');
        $this->assertStringContainsString('<legend>ログイン</legend>', $output);
    }

    /**
     * @test
     */
    public function ログイン_ログインOKでメニューへ遷移できること()
    {
        $this->request(
            'POST',
            ['login', 'login'],
            [
                'login_id' => 1,
                'pass' => 'abcde',
            ]
        );

        // login処理が成功すると gmo/menu へredirectされるのでそのテスト
        // 第２引数はHTTPステータスコード 302=redirect
        $this->assertRedirect('gmo/menu', 302);
    }
    
	/**
     * @test
     */
    public function ログイン_ID_PASSの必須チェックが動作すること()
    {
        $output = $this->request(
            'POST',
            ['login', 'login'],
            [
                'login_id' => '',
                'pass' => '',
            ]
        );
        $this->assertStringContainsString('ログインID欄は必須フィールドです', $output);
        $this->assertStringContainsString('パスワード欄は必須フィールドです', $output);
    }

    /**
     * @test
     */
    public function ログイン_ログイン認証に失敗すること()
    {
        $output = $this->request(
            'POST',
            ['login', 'login'],
            [
                'login_id' => '1',
                'pass' => 'test',
            ]
        );
        $this->assertStringContainsString('ログイン認証に失敗しました', $output);
    }
}
