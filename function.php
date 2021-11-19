<?php

//================================
// ローカル環境、本番環境変更点
//================================


//================================
// ログ
//================================
ini_set('log_errors', 'on'); //本番環境では記述しない
ini_set('error_log', 'php.log'); //本番環境では記述しない


//================================
// セッション
//================================
//セッションファイルの置き場を変更する(/var/tmp/以下に置くと30日は削除されない)
session_save_path("/var/tmp/");//本番環境では削除する
//ガーベージコレクションが削除するセッションの有効期限を設定
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を伸ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える
session_regenerate_id();

//開発用sessionユーザー
require('session_user.php');//本番環境では削除
//require('auth.php');

//================================
// サイトタイトル
//================================
//サイトタイトル未設定時
$siteTitle = '';



//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//================================
// ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID:'.session_id());
  debug('セッション変数の中身:'.print_r($_SESSION, true));
  debug('現在日時タイムスタンプ:'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug('ログイン期限日時タイムスタンプ:'.($_SESSION['login_date'] + $_SESSION['login_limit']));
  }
}
function debugTitle($str){
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
  debug('「'.$str);
  debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
}
function debugLogEnd(){
  debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
}

//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01', '入力してください');
define('MSG02', '指定時刻、指定のブロックには既に別の予定が入っています。場所を変更するか、時刻を変更してください');
define('MSG03', '指定時刻には既にあなたの別の企画が入っています。この企画の日時を変更するか、他の企画を変更してください');
define('MSG04', 'エラーが発生しました。しばらく経ってからやり直してください');
define('MSG05', '文字以内で入力してください');
define('MSG06', '20**-**-** **:**(年-月-日 時:分)の形式で入力してください');
define('MSG07', '3ヶ月先の末日までしか登録できません');
define('MSG08', '無効な選択です');

define('SUC01', '集会登録しました');
define('SUC02', '変更しました');
define('SUC03', '集会予定を削除しました');

//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg = array();

//================================
// DB接続関連関数
//================================
//DB接続関数
require('dbConnect.php');
/*
function dbConnect(){
    //別ファイル化
}
*/

function queryPost($dbh, $sql, $data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if(!$stmt -> execute($data)){
    debug('クエリに失敗しました');
    debug('失敗したSQL:'.print_r($stmt, true));
    $err_msg['common'] = MSG04;
    return 0;
  }
  //debug('クエリ処理終了');
  return $stmt;
}


//================================
// バリデーション関数
//================================
//エラーメッセージ表示
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}
//post情報表示
function getPost($key){
  if(!empty($_POST[$key])){
    return $_POST[$key];
  }
}
//未入力チェック
function validRequired($str, $key){
  if($str == ''){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
//セレクトボックス用未入力チェック
function validPlaceRequired($str, $key){
  if($str == 0){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//シップ選択セレクトボックス改変チェック
function validShip($ship, $key){
  if($ship >= 13 || $ship < 0){
    debug($ship);
    debug($block);
    global $err_msg;
    $err_msg['ship'] = MSG08;
  }
}
//バトルシップブロック選択改変チェック
function validBattleBlock($ship, $block, $key){
  if($ship == 12){
  if($block > 904 /*|| $block >= 909*/){
      global $err_msg;
      $err_msg['block'] = MSG08;
    }
  }
}
//チャレンジシップ選択改変チェック
function validChallengeBlock($ship, $block, $key){
  if($ship == 11){
    if($block >= 801 && $block <= 803){
      debug('チャれぶろデバッグシップ'.$ship);
      debug($block);
    global $err_msg;
    $err_msg['block'] = MSG08;
    }
  }
}

//最大文字数チェック
function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = $max.MSG05;
  }
}


//datetime形式バリデーション
function validDatetimeFormat($str, $key){
  if(!preg_match("/^(20)[0-9]{2}(-|\/)([0-1][0-9]|[0-9])(-|\/)([1-9]|[0-2][0-9]|[3][0-1])([T]|\s)(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/",$str)){//  /[T]|\s/
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

//3ヶ月先の末日までの予定かチェック
function validDateRestrection($str, $key){
  $add_date = date('Y-m-d', strtotime('+4 month'));
  $year_add = date("Y", strtotime($add_date));
  $month_add = date("m", strtotime($add_date));
  $first_date = $year_add."-".$month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  //$end_date = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($first_date)));
  if($str >= $first_datetime){
    global $err_msg;
    $err_msg[$key] = MSG07;
  }
}

//新規登録時の他の企画とのブッキング
function otherBooking($start_datetime, $finish_datetime, $ship, $block){
  global $err_msg;
  //DBに接続し、指定日時とブロックの情報がないか確認する
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM gatherData WHERE ( (`start_time` BETWEEN :start_time AND :finish_time ) OR (`finish_time` BETWEEN :start_time AND :finish_time) OR (:start_time BETWEEN `start_time` AND `finish_time`) OR (:finish_time BETWEEN `start_time` AND `finish_time`)) AND ship = :ship AND block = :block AND delete_flg = 0';
    //SELECT * FROM gatherData WHERE ( (`start_time` BETWEEN '2020-06-10 18:00:00' AND '2020-06-10 18:01:00' ) OR (`finish_time` BETWEEN '2020-06-10 18:00:00' AND '2020-06-10 18:01:00') OR ( '2020-06-10 18:00:00' BETWEEN `start_time` AND `finish_time`) OR ( '2020-06-10 18:01:00' BETWEEN `start_time` AND `finish_time`)) AND ship = 11 AND block = 213 AND delete_flg = 0
    $data = array(':start_time' => $start_datetime, ':finish_time' => $finish_datetime, ':ship'=> $ship, ':block' => $block);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt -> fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['block'] = MSG02;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $errr_msg['common'] = MSG04;
  }
}

//新規登録時自分の企画とのセルフブッキング
function selfBooking($organizer, $start_datetime, $finish_datetime){
  global $err_msg;
  //DBに接続し、企画者と指定日時の情報がないか確認する
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM gatherData WHERE ( (`start_time` BETWEEN :start_time AND :finish_time ) OR (`finish_time` BETWEEN :start_time AND :finish_time) OR (:start_time BETWEEN `start_time` AND `finish_time`) OR (:finish_time BETWEEN `start_time` AND `finish_time`)) AND organizer = :organizer AND delete_flg = 0';
    //他の人との企画ブッキングとほぼ同様のSQL
    $data = array(':organizer' => $organizer, ':start_time' => $start_datetime, ':finish_time' => $finish_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt-> fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['start_datetime'] = MSG03;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG04;
  }
}

//編集の場合の他の企画とのブッキング
function otherBookingChange($start_datetime, $finish_datetime, $id, $ship, $block){
  global $err_msg;
  //DBに接続し、指定日時とブロックの情報がないか確認する
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM gatherData WHERE ( (`start_time` BETWEEN :start_time AND :finish_time ) OR (`finish_time` BETWEEN :start_time AND :finish_time) OR (:start_time BETWEEN `start_time` AND `finish_time`) OR (:finish_time BETWEEN `start_time` AND `finish_time`)) AND NOT id = :id AND ship = :ship AND block = :block AND delete_flg = 0';
    //SELECT * FROM gatherData WHERE ((`start_time` BETWEEN '2020-04-25 23:30:00' AND '2020-04-25 23:40:00' )OR (`finish_time` BETWEEN '2020-04-25 23:30:00' AND '2020-04-25 23:40:00') OR ('2020-04-25 23:30:00' BETWEEN `start_time` AND `finish_time`) OR ('2020-04-25 23:40:00' BETWEEN `start_time` AND `finish_time`)) AND block = 901 AND delete_flg = 0
    $data = array(':start_time' => $start_datetime, ':finish_time' => $finish_datetime, ':id' => $id, ':ship'=> $ship, ':block' => $block);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt -> fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['block'] = MSG02;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $errr_msg['common'] = MSG04;
  }
}

//編集の場合の自分の企画とのセルフブッキング
function selfBookingChange( $id ,$organizer, $start_datetime, $finish_datetime){
  global $err_msg;
  //DBに接続し、企画者と指定日時の情報がないか確認する
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM gatherData WHERE ( (`start_time` BETWEEN :start_time AND :finish_time ) OR (`finish_time` BETWEEN :start_time AND :finish_time) OR (:start_time BETWEEN `start_time` AND `finish_time`) OR (:finish_time BETWEEN `start_time` AND `finish_time`)) AND NOT id = :id AND organizer = :organizer AND delete_flg = 0';
    //他の人との企画ブッキングとほぼ同様のSQL
    $data = array(':id' => $id ,':organizer' => $organizer, ':start_time' => $start_datetime, ':finish_time' => $finish_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt-> fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['start_datetime'] = MSG03;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG04;
  }
}


//================================
// DB参照関連関数
//================================
//index.php 全集会情報取得関数
function getGatherData(){
  debug('今月の全集会情報取得します');
  $end_add_date = date('Y-m-d', strtotime('1 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others, g.tweet_url FROM gatherData as g inner join users as u on g.organizer = u.id WHERE current_date() <= finish_time AND start_time > date_format(now(),"%Y-%m-01") AND start_time <= :end_datetime AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':end_dateitime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}

// バトルシップの集会情報取得
function getBattleGatherData(){
  debug('今月のバトルシップの集会情報取得します');
  $end_add_date = date('Y-m-d', strtotime('1 month', strtotime(date('Y-m-1'))));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others, g.tweet_url FROM gatherData as g inner join users as u on g.organizer = u.id WHERE current_date() <= finish_time AND start_time > date_format(now(),"%Y-%m-01") AND start_time <= :end_datetime AND g.ship = 12 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
// チャレンジシップの集会情報取得
function getChallengeGatherData(){
  debug('今月のチャレンジシップの集会情報取得します');
  $end_add_date = date('Y-m-d', strtotime('1 month', strtotime(date('Y-m-1'))));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others, g.tweet_url FROM gatherData as g inner join users as u on g.organizer = u.id WHERE current_date() <= finish_time AND start_time > date_format(now(),"%Y-%m-01") AND start_time <= :end_datetime AND g.ship = 11 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}

// 所属シップの集会情報取得
function getNormalGatherData(){
  debug('今月の所属シップの集会情報取得します');
  $end_add_date = date('Y-m-d', strtotime('1 month', strtotime(date('Y-m-1'))));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others, g.tweet_url FROM gatherData as g inner join users as u on g.organizer = u.id WHERE (current_date() <= finish_time) AND start_time > date_format(now(),"%Y-%m-01") AND start_time <= :end_datetime AND NOT (g.ship = 11 OR g.ship = 12) AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.ship ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}

//マイページ用　自分の企画全取得
function getMyGatherData(){
  debug('自分の企画中の集会情報を取得します');
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE current_date() <= finish_time AND start_time > date_format(now(),"%Y-%m-01") AND g.organizer = :id AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':id' => $_SESSION['user_id']);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}

//マイページ用　中止した企画取得
function getMyCancelGatherData(){
  debug('中止した企画の集会情報を取得します');
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE g.organizer = :id AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 1 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':id' => $_SESSION['user_id']);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}

/*
//1ヶ月後の予定取得 バトルシップ
function getNextMonthBattleGatherData(){
  debug('来月開催のバトル鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('1 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('2 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 12 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//チャレンジ
function getNextMonthChallengeGatherData(){
  debug('来月開催のチャレンジ鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('1 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('2 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 11 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//通常鯖
function getNextMonthNormalGatherData(){
  debug('来月開催の通常鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('1 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('2 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND NOT (g.ship = 11 OR g.ship = 12) AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//2ヶ月後の予定 バトル
function get2MonthLaterBattleGatherData(){
  debug('再来月開催のバトル鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('2 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('3 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 12 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//チャレンジ
function get2MonthLaterChallengeGatherData(){
  debug('再来月開催のチャレンジ鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('2 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('3 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 11 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//通常鯖
function get2MonthLaterNormalGatherData(){
  debug('再来月開催の通常鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('2 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('3 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND NOT (g.ship = 11 OR g.ship = 12) AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//3ヶ月後 バトル
function get3MonthLaterBattleGatherData(){
  debug('3ヶ月後のバトル鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('3 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('4 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 12 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//チャレンジ鯖
function get3MonthLaterChallengeGatherData(){
  debug('3ヶ月後のチャレンジ鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('3 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('4 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 11 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//通常鯖
function get3MonthLaterNormalGatherData(){
  debug('3ヶ月後の通常鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime('3 month'));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime('4 month'));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND NOT (g.ship = 11 OR g.ship = 12) AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
*/

//forによる自動計算1ヶ月後の予定取得 バトルシップ
function getMonthryBattleGatherData($i){
  //debug($i.'ヶ月先開催のバトル鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime($i.' month', strtotime(date('Y-m-1'))));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime(($i+1).' month', strtotime(date('Y-m-1'))));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others, g.tweet_url FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 12 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//チャレンジ
function getMonthryChallengeGatherData($i){
  debug($i.'ヶ月先の開催のチャレンジ鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime($i.' month', strtotime(date('Y-m-1'))));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime(($i+1).' month', strtotime(date('Y-m-1'))));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others, g.tweet_url FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND g.ship = 11 AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}
//通常鯖
function getMonthryNormalGatherData($i){
  debug($i.'ヶ月先の開催の通常鯖集会情報を取得します');
  $first_add_date = date('Y-m-d', strtotime($i.' month', strtotime(date('Y-m-1'))));
  $first_year_add = date("Y", strtotime($first_add_date));
  $first_month_add = date("m", strtotime($first_add_date));
  $first_date = $first_year_add."-".$first_month_add."-1";
  $first_datetime = date('Y-m-d 00:00:00', strtotime($first_date));
  $end_add_date = date('Y-m-d', strtotime(($i+1).' month', strtotime(date('Y-m-1'))));
  $end_year_add = date("Y", strtotime($end_add_date));
  $end_month_add = date("m", strtotime($end_add_date));
  $end_date = $end_year_add."-".$end_month_add."-1";
  $end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT g.id, g.start_time, g.finish_time, g.ship, g.block, g.gather_title, u.twitter_account, g.tag, g.others, g.tweet_url FROM gatherData as g inner join users as u on g.organizer = u.id WHERE start_time > :first_datetime AND start_time <= :end_datetime AND NOT (g.ship = 11 OR g.ship = 12) AND u.ban_flg = 0 AND u.delete_flg = 0 AND g.delete_flg = 0 ORDER by date(g.start_time) ASC, g.block ASC, time(g.start_time) ASC';
    $data = array(':first_datetime' => $first_datetime,':end_datetime' => $end_datetime);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      debug('クエリ成功');
      $rst['data'] = $stmt -> fetchAll();
      return $rst;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e ->getMessage());
    $err_msg['common'] = MSG04;
  }
}

// 集会予定変更用
function getGatherDataOne($id, $user_id){
  debug('ユーザーIDと集会IDを照合します');
  //例外処理
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT * FROM gatherData WHERE id = :id AND organizer = :user_id';
    $data = array(':id' => $id, ':user_id' => $user_id);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      //クエリ結果のデータを１レコード返却
      return $stmt -> fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生:'.$e->getMessage());
    $err_msg['common'] = MSG04;
  }
}

/*とりあえず使わない
//マイページ用　シップ名コール関数
function callShip(){
  if($value['ship'] == 12){
    $value['ship'] == 'B';
    echo $value['ship'];
  }elseif($value['ship'] == 11){
    $value['ship'] == 'C';
    echo $value['ship'];
  }
}
*/


//================================
// その他
//================================
//サニタイズ
function sanitize($str){
  return htmlspecialchars($str, ENT_QUOTES);
}
//フォーム入力保持
function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  //ユーザーデータがある場合
  if(!empty($dbFormData)){
    //フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      //POSTにデータがある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        //ない場合
        return sanitize($dbFormData[$str]);
      }
    }else{
      //フォームのエラーがない場合
      //POSTにデータがあり、DBの情報と違う場合
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    //ユーザーデータがない場合
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}

//セッション一回だけ取得
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
 ?>
