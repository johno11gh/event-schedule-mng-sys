<?php
//共通変数・関数読み込み
require('function.php');
debugTitle('集会登録編集ページ');
debugLogStart();
require('auth.php');

//================================
// 画面処理
//================================

$id = (!empty($_GET['id']))? $_GET['id']: ''; //gatherData['id'];
$user_id = $_SESSION['user_id']; //user['id'];
$dbFormData = (!empty($id))? getGatherDataOne($id, $user_id): '';
//新規登録か変更か判別
//空かどうか→空じゃなければ更新フラグ　空なら新規登録　新規：更新
$edit_flg = (empty($dbFormData))? false: true;
debug('ユーザーID:'.$user_id);
debug('集会ID:'.$id);
//パラメータ改竄チェック
//================================
//GETパラメータはあるが、改ざんされている（URLをいじった）場合、正しいデータが取れないのでマイページへ遷移させる
if(!empty($id) && empty($dbFormData)){
  debug('ユーザーIDと集会IDが照合できませんでした。トップページへ遷移します');
  header("Location:index.php");
}

//post送信がある場合
if(!empty($_POST)){
  debug('post送信があります');
  debug('post情報:'.print_r($_POST, true));

  //変数定義
  $organizer = $_SESSION['user_id']; //ツイッターアカウント
  $gather_title = $_POST['gather_title'];
  $start_datetime = $_POST['start_datetime'];
  $finish_datetime = $_POST['finish_datetime'];
  $ship = $_POST['ship'];
  $block = $_POST['block'];
  $tag = $_POST['tag'];
  if(!empty($_POST['others'])){
    $others = $_POST['others'];
  }else{
    $others = NULL;
  }

  //=============================
  //バリデーションチェック

  validRequired($gather_title, 'gather_title');
  validRequired($start_datetime, 'start_datetime');
  validRequired($finish_datetime, 'finish_datetime');
  validPlaceRequired($ship, 'ship');
  validPlaceRequired($block, 'block');
  validRequired($tag, 'tag');

  if(empty($err_msg)){
    //シップ選択チェック
    validShip($ship, 'ship');
    //validBattleBlock($ship, $block, 'block'); //バトルブロック制限関数
    //validChallengeBlock($ship, $block, 'block');//チャれぶろっく制限関数
    //開始日時終了日時形式チェック
    validDatetimeFormat($start_datetime, 'start_datetime');
    validDatetimeFormat($finish_datetime, 'finish_datetime');
    //文字数制限
    validMaxLen($gather_title, 'gather_title');
    validMaxLen($tag, 'tag');
    validMaxLen($others, 'others');
    if(empty($err_msg)){
      if(!$edit_flg){
        otherBooking($start_datetime, $finish_datetime, $ship, $block);
      }else{
        otherBookingChange($start_datetime, $finish_datetime, $id, $ship, $block);
      }
      if(empty($err_msg)){
        if(!$edit_flg){//新規登録時
          selfBooking($organizer, $start_datetime, $finish_datetime);
        }else{
          selfBookingChange($id, $organizer, $start_datetime, $finish_datetime);
        }
        if(empty($err_msg)){
          validDateRestrection($start_datetime, 'start_datetime');
          if(empty($err_msg)){
            //例外処理
            try{
              //DB接続
              $dbh = dbConnect();
              if($edit_flg){
                //変更
                $sql = 'UPDATE gatherData SET start_time = :start_datetime, finish_time = :finish_datetime, ship = :ship, block = :block, gather_title = :gather_title, tag = :tag, others = :others, delete_flg = 0 ,update_date = :update_date WHERE id = :id AND organizer = :organizer';
                      //UPDATE gatherData SET start_time = '2020-05-07 00:00:00', finish_time='2020-05-07 00:05:00' , ship = 10, block = 202, gather_title ='登録変更' , tag= '登録変更' , others ='test',  delete_flg = 0, update_date = '2020-05-07 00:00:00' WHERE id = 29 AND organizer = 1 //例文
                $data = array(':start_datetime' => $start_datetime, ':finish_datetime' => $finish_datetime, ':ship' => $ship, ':block' => $block, ':gather_title' => $gather_title, ':tag' => $tag, ':others' => $others, ':update_date' => date('Y-m-d H:i:s'), ':id' => $id, ':organizer' => $user_id);
              }else{
                //新規
                $sql = 'INSERT INTO gatherData (start_time, finish_time, ship, block, gather_title, organizer, tag, others, update_date, create_date) VALUES (:start_datetime, :finish_datetime, :ship, :block, :gather_title, :organizer, :tag, :others, :update_date, :create_date)';
                $data = array( ':start_datetime' => $start_datetime, ':finish_datetime' => $finish_datetime, ':ship' => $ship, ':block' => $block, ':gather_title' => $gather_title, ':organizer' => $organizer, ':tag' => $tag, ':others' => $others, ':update_date' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));
              }
              //クエリ実行
              $stmt = queryPost($dbh, $sql, $data);
              if($stmt){
                debug('登録成功しました');
                debug('トップページに遷移します');
                header("Location:index.php");
              }
            }catch(Exception $e){
              error_log('エラー発生:'.$e->getMessage());
              $err_msg['common'] = MSG04;
            }
          }
        }
      }
    }
  }
}
 ?>

<?php
$siteTitle = (!$edit_flg)? '新規登録|': '変更|';
require('head.php');
 ?>

 <body>
   <?php require('header.php'); ?>
   <div class="container">
     <main class="maincontents page-1colum">
       <section class="section-form" id="regist">
         <h2 class="section-title"><?php echo (!$edit_flg)? '新規登録': '変更'; ?></h2>
         <div class="row">
           <div class="col">
             <form class="form" method="post">
               <label class="label">
                 集会名
                 <input type="text" name="gather_title" value="<?php echo getFormData('gather_title'); ?>">
               </label>
               <div class="area-msg">
                 <?php echo getErrMsg('gather_title'); ?>
               </div>
               <label class="label ">
                 開催日時
                 <input type="datetime-local" name="start_datetime" value="<?php echo getFormData('start_datetime'); ?>">
               </label>
               <div class="area-msg">
                 <?php echo getErrMsg('start_datetime'); ?>
               </div>
               <label class="label">
                 終了日時
                 <input type="datetime-local" name="finish_datetime" value="<?php echo getFormData('finish_datetime'); ?>">
               </label>
               <div class="area-msg">
                 <?php echo getErrMsg('finish_datetime'); ?>
               </div>
               <label class="label">
                 シップ
                 <select name="ship" id="ship">
                   <option value=""></option>
                   <?php for ($i=1; $i <= 10; $i++) {
                     if($edit_flg == true &&  $dbFormData['ship'] ==  $i){
                       $selected = 'selected';
                     }else{
                       $selected = '';
                     }
                     echo '<option value="'.$i.'"'.$selected.'>ship'.$i.'</option>';
                   } ?>
                   <option value="11" <?php if(!empty($dbFormData)){ if($dbFormData['ship'] == 11){ echo 'selected';}} ?>>共通チャレンジ</option>
                   <option value="12" <?php if(!empty($dbFormData)){ if($dbFormData['ship'] == 12){ echo 'selected';}} ?>>共通バトル</option>
                 </select>
               </label>
               <div class="area-msg">
                 <?php echo getErrMsg('ship'); ?>
               </div>
               <label class="label">
                 ブロック
                 <select name="block" id="block">
                    <option value=""></option>
                   <?php if($edit_flg = false){ ?>
                     <!--<option value=""></option>-->
                   <?php }else{ ?>
                     <?php if($dbFormData['ship'] >= 1 && $dbFormData['ship'] <= 10){ ?>
                       <?php
                       //for文で選択肢生成
                       for ($i=1; $i <=78 ; $i++) {
                         // code...
                         if($dbFormData['block'] == $i){
                           $block_selected = 'selected';
                         }else{
                           $block_selected = '';
                         }
                         echo '<option value="'.($i).'"'.$block_selected.'>'.($i).'</option>';
                       }
                        ?>
                     <?php }elseif($dbFormData['ship'] == 11){ ?>
                       <?php
                       //for文で選択肢生成
                       for ($i=1; $i <=6 ; $i++) {
                         // code...
                         if($dbFormData['block'] == 600+$i){
                           $block_selected = 'selected';
                         }else{
                           $block_selected = '';
                         }
                         echo '<option value="'.(600+$i).'"'.$block_selected.'>'.(600+$i).'</option>';
                       }
                       for ($i=1; $i <=6 ; $i++) {
                         // code...
                         if($dbFormData['block'] == 800+$i){
                           $block_selected = 'selected';
                         }else{
                           $block_selected = '';
                         }
                         echo '<option value="'.(800+$i).'"'.$block_selected.'>'.(800+$i).'</option>';
                       }
                        ?>
                     <?php }elseif($dbFormData['ship'] == 12){ ?>
                       <?php
                       //for文で選択肢生成
                       for ($i=1; $i <=8 ; $i++) {
                         // code...
                        if($dbFormData['block'] == 900 + $i){
                          $block_selected = 'selected';
                        }else{
                          $block_selected = '';
                        }
                        echo '<option value="'.(900+$i).'"'.$block_selected.'>'.(900+$i).'</option>';
                       }
                        ?>
                     <?php } ?>
                   <?php } ?>
                 </select>
               </label>
               <div class="area-msg">
                 <?php echo getErrMsg('block'); ?>
               </div>
               <label class="label">
                 タグ　
                 #<input type="text" name="tag" value="<?php echo getFormData('tag'); ?>">
               </label>
               <div class="area-msg">
                 <?php echo getErrMsg('tag'); ?>
               </div>
               <label class="label">
                 備考
                 <textarea name="others" rows="8" cols="80"><?php echo getFormData('others'); ?></textarea>
               </label>
               <div class="area_msg">
                 <?php echo getErrMsg('others'); ?>
               </div>
               <div class="btn-container">
                 <input type="submit" value="登録">
               </div>
             </form>
           </div>
         </div>
       </section>
     </main>
   </div>

<?php require('footer.php'); ?>
