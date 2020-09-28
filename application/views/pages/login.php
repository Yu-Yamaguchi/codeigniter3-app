<?php
// ログインページ
$login_id = array('type'=>'text', 'name'=>'login_id', 'value'=>$form['login_id'], 'maxlength'=>100);
$pass = array('type'=>'password', 'name'=>'pass', 'value'=>$form['pass'], 'maxlength'=>100);
?>

<?= form_open('login/login'); ?>

    <label for='login_id'>ログインID</label>
    <?= form_input($login_id); ?><br>
    <?= form_error('login_id'); ?>

    <label for='pass'>パスワード</label>
    <?= form_input($pass); ?><br>
    <?= form_error('pass'); ?>

    <?= form_submit("login_submit", "ログイン"); ?>

<?= form_close(); ?>