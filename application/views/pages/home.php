<?php
$name = array('type'=>'text', 'name'=>'name', 'value'=>$form['name'], 'maxlength'=>100);
$email = array('type'=>'text', 'name'=>'email', 'value'=>$form['email'], 'maxlength'=>100);
?>

<div class="msg">
    特に怪しいサイトではありません。<br>
    たった１万円で素敵な商品が手に入ります。<br>
    お名前とメールアドレスを入力して`申し込む`ボタンを押してください。
</div>

<?php echo form_open('apply/post'); ?>

    <label for="name">お名前</label>
    <?php echo form_input($name);?><br>
    <?php echo form_error("name");?>

    <label for="email">E-Mail</label>
    <?php echo form_input($email);?><br>
    <?php echo form_error("email");?>

    <input type="submit" name="submit" value="申し込む" />

</form>
