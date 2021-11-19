<?php
//共通変数・関数ファイル読み込み
require('function.php');
debugTitle('メンテナンス表示');
debugLogStart();

$scheduled_maintenance_end_time = '';

 ?>
 <?php
 $siteTitle = 'メンテナンス|';
 require('head.php');
  ?>
  <body>
    <!-- header -->
    <?php require('header.php'); ?>

    <div class="container">
      <main class="maincontents page-1colum">
        <section class="maintenance-report" id="main">
          日頃より本管理サイトをご利用いただき、ありがとうございます。<br>
          ただいまサイトのメンテナンスを行なっております。<br>
          ご迷惑をおかけしますが、しばらく経ってから再度ごアクセスください。<br>
          メンテナンス終了時刻 <?php echo $scheduled_maintenance_end_time; ?>
        </section>
      </main>
    </div>
  <!-- footer -->
  <?php require('footer.php'); ?>
