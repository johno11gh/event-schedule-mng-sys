<?php
function dbConnect(){
  //DBへの接続準備
  $dsn = 'mysql:dbname=; host=localhost; charset=utf8';//ローカル開発用
  $user = '****'; //ローカル開発
  $password = '****';//ローカル開発
  $options = array(
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_SILENT,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    //バッファードクエリを使う(一度に結果セットを全て取得し、サーバー負荷を軽減)
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクト作成
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

 ?>
