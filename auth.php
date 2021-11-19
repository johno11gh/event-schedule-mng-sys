<?php
//================================
//ログイン認証・自動ログアウト
//================================
//ログインしている場合
if(!empty($_SESSION['user_id'])){
  debug('ログイン済ユーザーです');
  //現在の日時が最終ログイン日時と有効期限を超えていた場合
  if($_SESSION['login_date'] + $_SESSION['login_limit'] < time()){
    debug('ログイン有効期限オーバーです');
    debug('ログインページへ遷移します');
    //セッション削除
    session_destroy();
    //ログインページへ遷移
    header("Location:login.php");
  }else{
    debug('ログイン有効期限内です');
    try{
      $dbh = dbConnect();
      $sql = 'SELECT twitter_account FROM users WHERE id = :user_id AND delete_flg = 0';
      $data = array(':user_id' => $_SESSION['user_id']);
      $stmt = queryPost($dbh, $sql, $data);
      $rst = $stmt->fetch(PDO::FETCH_ASSOC);
      if($rst !== $_SESSION['access_token']['screen_name']){
        $sql2 = 'UPDATE users SET twitter_account = :twitter_account WHERE id = :user_id';
        $data2 = array(':twitter_account' => $_SESSION['access_token']['screen_name'], ':user_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql2, $data2);
      }
    }catch(Exception $e){
      error_log('エラー発生:'.$e->getMesage());
      $err_msg['common'] = MSG04;
    }
    $sesLimit = 60 * 60 * 24 * 8;
    $_SESSION['login_date'] = time();
    $_SESSION['login_limit'] = $sesLimit;
    //現在実行中のスクリプトファイル名がlogin.phpの場合
    //basename関数を使う
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      debug('トップページへ遷移します');
      header("Location:index.php");
    }
  }
}else{
  debug('未ログインユーザーです');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
  header("Location:login.php");
  }
}
 ?>
