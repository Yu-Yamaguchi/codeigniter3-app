
<div class='container'>

<div class="list-group">
  <a href="<?= base_url().'gmo/member'; ?>" class="list-group-item list-group-item-action flex-column align-items-start">
    <div class="d-flex w-100 justify-content-between">
      <h5 class="mb-1">リンクタイプPlusでの会員カード編集</h5>
    </div>
    <p class="mb-1">GMO-PGのリンクタイプPlusを利用し、会員カード編集画面へ遷移します。</p>
    <small class="text-muted">GMO会員IDを指定し、クレジットカード情報を会員IDに紐づけて保存するための機能です。</small>
  </a>
  <a href="<?= base_url().'gmo/payment'; ?>" class="list-group-item list-group-item-action flex-column align-items-start">
    <div class="d-flex w-100 justify-content-between">
      <h5 class="mb-1">リンクタイプPlusでの決済（無条件に会員IDをパラメータセット）</h5>
    </div>
    <p class="mb-1">GMOのリンクタイプPlusを利用し、決済画面へ遷移します。</p>
    <small class="text-muted">GMO会員IDを指定して決済画面へ遷移します。<br>すでに会員登録済みか否かに関わらず、会員IDをパラメータで指定します。<br>会員登録が完了していない場合は、決済画面にエラーが表示されます。</small>
  </a>
  <a href="<?= base_url().'gmo/payment_mem'; ?>" class="list-group-item list-group-item-action flex-column align-items-start">
    <div class="d-flex w-100 justify-content-between">
      <h5 class="mb-1">リンクタイプPlusでの決済（会員登録済みの場合のみ会員IDをパラメータセット）</h5>
    </div>
    <p class="mb-1">GMOのリンクタイプPlusを利用し、決済画面へ遷移します。</p>
    <small class="text-muted">GMO会員IDを指定して決済画面へ遷移します。<br>会員IDが登録済みの場合のみ会員IDをパラメータにセットして決済画面に遷移するURLを生成します。</small>
  </a>
</div>

</div>