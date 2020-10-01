
<div class='container'>

<?= form_open('login/login'); ?>

<legend>ログイン</legend>

    <?= $this->session->flashdata('login_check_error'); ?>

    <div class="form-group">
        <label for="login_id">ID</label>
        <input type="text" id="login_id" name="login_id" value="<?= $form['login_id']; ?>" maxlength="100" class="form-control" aria-describedby="loginIdHelp" placeholder="Enter login id">
        <?= form_error('login_id'); ?>
    </div>

    <div class="form-group">
        <label for="pass">パスワード</label>
        <input type="password" id="pass" name="pass" value="<?= $form['pass']; ?>" maxlength="100" class="form-control" placeholder="Password">
        <?= form_error('pass'); ?>
    </div>

    <button class="btn btn-primary" type="submit">ログイン</button>

<?= form_close(); ?>

</div>