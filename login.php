<?php

//session_start();
require('function.php');
debugTitle('ログイン処理');
require_once 'common.php';
require_once './vendor/autoload.php';

use Abraham\TwitterOAuth\TwitterOAuth;

//TwitterOAuthをインスタンス化
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);
$content = $connection->get("account/verify_credentials");
//debug($connection);
//コールバックURLセット
$request_token = $connection -> oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
//debug($request_token);
//var_damp($request_token);
//callback.phpで使うのでセッションに入れる
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
debug('oauth:token:'.$_SESSION['oauth_token']);
debug('oauth_token_secret:'.$_SESSION['oauth_token_secret']);
//var_damp($_SESSION['oauth_token']);
//var_damp($_SESSION{'oauth_token_secret'});

//Twitter.com上の認証画面のURLを取得
$url = $connection -> url('oauth/authenticate', array('oauth_token' => $request_token['oauth_token']));
//oauth/authenticateで２回め以降の認証画面がスキップされる アカウント変更等のため毎回認証画面を出したい場合はoauth/authorizeに変更する

//Twitter.comの認証画面へリダイレクト
header('location:'.$url);
 ?>
