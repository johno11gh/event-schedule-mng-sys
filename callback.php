<?php

require('function.php');
//session_start();
debug('コールバック処理');

require_once 'common.php';
require_once './vendor/autoload.php';

USE Abraham\TwitterOAuth\TwitterOAuth;

//login.phpでセットしたセッション
$request_token = [];
//エラーが出る場合は使用しているサーバーのphpバージョンが古いため別のサーバーに乗り換える
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
debug('$request_token["oauth_token"]:'.$request_token['oauth_token']);
debug('$request_token["oauth_token_secret"]:'.$request_token['oauth_token_secret']);

//Twitterから返されたOAuthトークンと予めlogin.phpで入れておいたセッション上のものと一致するかをチェック
if(isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']){
  die('error');
}
//OAuthトークンも用いて、TwitterOAuthをインスタンス化
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
//配列access_tokenにOauthトークンとTokenSecretを入れる
$_SESSION['access_token'] = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));

debug($_SESSION['access_token']['user_id']);
debug($_SESSION['access_token']['screen_name']);

try{
  //DB接続
  debug('DBにアクセストークンから受け取ったtwitter_user_idがあるかチェック');
  $dbh = dbConnect();
  //SQL文
  $sql = 'SELECT count(*) FROM users WHERE twitter_user_id = :twitter_user_id AND delete_flg = 0';
  $data = array(':twitter_user_id' => $_SESSION['access_token']['user_id']);
  //クエリ実行
  $stmt = queryPost($dbh, $sql, $data);
  //クエリ結果の値を取得
  $result = $stmt -> fetch(PDO::FETCH_ASSOC);

  if(!empty(array_shift($result))){
    //twitter_user_idがある場合
    debug('DBにtwitter_user_idがある場合の処理');
    debug('DBからサイトIDとtwitter_account名を取得する');
    $sql2 = 'SELECT id, twitter_account FROM users WHERE twitter_user_id = :twitter_user_id';
    $data2 = array(':twitter_user_id' => $_SESSION['access_token']['user_id']);
    //クエリ実行
    $stmt = queryPost($dbh, $sql2, $data2);
    $rst = $stmt -> fetch(PDO::FETCH_ASSOC);
    //twitter：screen_nameがデータベースと違った場合
    if($_SESSION['access_token']['screen_name'] !== $rst['twitter_account']){
      debug('取得したscreen_nameとDB保存のアカウント名が違います');
      //twitter_account名を変更
      $sql3 = 'UPDATE users SET twitter_account = :twitter_account WHERE twitter_user_id = :twitter_user_id';
      $data3 = array(':twitter_account' => $_SESSION['access_token']['screen_name'], ':twitter_user_id' => $_SESSION['access_token']['user_id']);
      //クエリ実行
      $stmt = queryPost($dbh, $sql3, $data3);
    }
    //twitter_accountのデータベース名とログイン時のscreen_nameが違った場合、同じだった場合を合わせ、共にサイト用ユーザーIDを格納する
    $_SESSION['user_id'] = $rst['id'];
    $sesLimit = 60 * 60 * 24 * 7;
    $_SESSION['login_date'] = time();
    $_SESSION['login_limit'] = $sesLimit;
    debug('セッション変数の中身:'.print_r($_SESSION, true));
  }else{
    //ツイッターuser_idがない場合
    debug('DBにtwitter_user_idがない場合の処理');
    $sql2 = 'INSERT INTO users (twitter_account, twitter_user_id, login_time, update_date, create_date) VALUES (:twitter_account, :twitter_user_id, :login_time, :update_date, :create_date)';
    $data2 = array(':twitter_account' => $_SESSION['access_token']['screen_name'], ':twitter_user_id' => $_SESSION['access_token']['user_id'], 
     ':login_time' => date('Y-m-d H:i:s'), ':update_date' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));
    //クエリ実行
    $stmt = queryPost($dbh, $sql2, $data2);
    if($stmt){
      //ログイン有効期限
      $sesLimit = 60 * 60 * 24 * 7;
      $_SESSION['login_date'] = time();
      $_SESSION['login_limit'] = $sesLimit;
      $_SESSION['user_id'] = $dbh->lastInsertId();
      debug('セッション変数の中身:'.print_r($_SESSION, true));
    }
  }
}catch(Exception $e){
  error_log('エラー発生:'.$e->getMessage());
}
//リダイレクト
header('location: /index.php');
 ?>
