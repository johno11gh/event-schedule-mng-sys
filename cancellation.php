<?php
require('function.php');
debugTitle('企画中止');
debugLogStart();
//require('auth.php');

$id = (!empty($_GET['id']))? $_GET['id'] : '';
if(empty($id)){
  debug('集会idが未入力です。マイページに戻ります');
  header('Location:mypage.php');
}
debug('処理対象集会id:'.$id);
$user_id = $_SESSION['user_id'];
debug('ユーザー:'.$user_id);
  //例外処理
  //ユーザーIDと集会IDに齟齬がないかチェックする
  try{
    //DB接続
    $dbh = dbConnect();
    $sql = 'SELECT * FROM gatherData WHERE id = :id AND organizer = :user_id';
    $data = array(':id' => $id, ':user_id' => $user_id);
    $stmt = queryPost($dbh, $sql, $data);
    $resultCount = $stmt -> rowCount();
    if(!empty($resultCount)){
      debug($resultCount.'ユーザーとIDが一致しました');
      debug('中止、完全削除判定を行います');
      $sql2 = 'SELECT delete_flg FROM gatherData WHERE id = :id AND organizer = :user_id AND delete_flg = 0';
      $stmt = queryPost($dbh, $sql2, $data);
      $rst = $stmt -> fetch(PDO::FETCH_ASSOC);
      if($rst){
        debug('delete_flgは0でした。');
        debug('企画中止処理を開始します');
        $sql3 = 'UPDATE gatherData SET delete_flg = 1 WHERE id = :id AND organizer = :user_id AND delete_flg = 0';
        $stmt = queryPost($dbh, $sql3, $data);
        debug('企画中止の処理を完了しました');
      }else{
        debug('delete_flgは1でした');
        debug('完全削除処理を行います');
        $sql4 = 'DELETE FROM gatherData WHERE id = :id AND organizer = :user_id AND delete_flg = 1';
        $stmt = queryPost($dbh, $sql4, $data);
        debug('完全削除処理完了しました');
      }
    }else{
      debug($resultCount.'ユーザーIDが一致しませんでした。マイページへ戻ります');
      header('Location:mypage.php');
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG04;
  }
debugLogEnd();
header('Location:mypage.php');
 ?>
