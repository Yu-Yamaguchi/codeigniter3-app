<div class="msg">
    申込ありがとうございます。<br>
    GMO Payment Gatewayのサイトへアクセスし、決済手続きをお願いします。
</div>

<pre>
<?php echo $form['json']; ?>
</pre>

<form method="get" action="<?php echo $form['action']; ?>">
    <input type="submit" name="submit" value="GMO-PGで決済" />
</form>
