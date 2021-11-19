<?php
require('function.php');
debugTitle('test');
debugLogStart();

//debug('セッションuser_id:'.$_SESSION['user_id']);
//debug('セッションの中身のtwitter_account:'.$_SESSION['twitter_account']);
//debug('ツイッターアカウントscreen_naame:'.$_SESSION['access_token']['screen_name']);

require('head.php');

$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$tommorow = date('Y-m-d', strtotime("1 day"));
$nowtime = date('H:i:s');
//$todaytime = new datetime($today, $nowtime);
//$todaytime = ($today.' '.$nowtime) -> format('Y-m-d H:i:s');
$tommorow_now = date('Y-m-d H:i:s', strtotime("1 day"));
$twelveHour_ago = date('Y-m-d H:i:s', strtotime("-12 hour"));
$tommorow_twelveHour_ago = date('Y-m-d H:i:s', strtotime('1 day -12 hour'));

if(!empty($_POST)){
  $post_datetime_local = $_POST['datetime_local'];
  $post_week = $_POST['week'];
  $post_time = $_POST['time'];
  $post_month = $_POST['month'];
  $post_date = $_POST['date'];
  $post_next_date = new DateTime($post_date);
  $post_next_date -> modify('+1 day');
  $post_next_date2 = $post_next_date -> format('Y-m-d');
  $post_color = $_POST['color'];
  $post_date_time = date($post_date.' '.$post_time);
  $post_time_after1hour = new DateTime($post_date_time);
  $post_time_after1hour -> modify('+1hour');
  $post_next_datetime = date($post_next_date2.' '.$post_time);
}else{
  $post_datetime_local = '';
  $post_week = '';
  $post_time = '';
  $post_month = '';
  $post_date = '';
  $post_color = '';
  $post_date_time = '';
  $post_time_after1hour = '';
}



 ?>

 <body>
   <?php require('header.php'); ?>

   <main>
     <div class="container">
       <div class="row">
         <div class="col">
           現在日時<?php echo $now; ?>
           <br>
           今日<?php echo $today; ?>
           <br>
           時刻<?php echo $nowtime; ?>
           <br>
           日時と時刻の結合:<?php //echo date('Y-m-d H:i:s',$todaytime); ?>
           <br>
           明日<?php echo $tommorow; ?>
           <br>
           明日の今<?php echo $tommorow_now; ?>
           <br>
           12時間前<?php echo $twelveHour_ago; ?>
           <br>
           明日の今の12時間前<?php echo $tommorow_twelveHour_ago; ?>
         </div>
       </div>

       <div class="row">
         <div class="col">
           <form class="form" action="" method="post">
             <div class="">
               ローカルデイトタイム<input type="datetime-local" name="datetime_local" value=""><br>
               月日<input type="date" name="date" value=""><br>
               時刻<input type="time" name="time" value=""><br>
               月<input type="month" name="month" value=""><br>
               週<input type="week" name="week" value=""><br>
               色<input type="color" name="color" value=""><br>
             </div>
             <div class="">
               <input type="submit" value="送信">
             </div>
           </form>
         </div>
       </div>

       <div class="row">
         <div class="col">
           post情報<br>
           datetime_local <?php echo $post_datetime_local; ?><br>
           date <?php echo $post_date; ?><br>
           time <?php echo $post_time; ?><br>
           曜日:<?php echo $week[date("w", strtotime($post_date))]; ?><br>
           next date <?php echo $post_next_date->format('Y-m-d'); ?><br>
           date_time <?php echo $post_date_time; ?><br>
           time after1hour <?php //echo $post_time_after1hour -> format('Y-m-d H:i:s'); ?><br>
           next date_time <?php //echo $post_next_datetime -> format('Y-m-d H:i:s'); ?><br>
           month <?php echo $post_month; ?><br>
           week <?php echo $post_week; ?><br>
           color <?php echo $post_color; ?><br>
           IPアドレス<?php echo $_SERVER['REMOTE_ADDR']; ?>
           <?php debug($_SERVER['REMOTE_ADDR']); ?>
         </div>
       </div>
        <?php
        if($edit_flg = false){if($dbFormData['ship'] == 11){ echo 'selected';}}
        if($edit_flg = false || $dbFormData['ship'] == 11){ echo 'selected';}
         ?>
     </div>
   </main>

   <?php require('footer.php'); ?>
