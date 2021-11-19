<?php
require('function.php');
debugTitle('マイページ');
debugLogStart();
require('auth.php');

$dbMyGatherData = getMyGatherData();
$dbMyCancelGatherData = getMyCancelGatherData();

 ?>
<?php
$siteTitle = 'マイページ|';
require('head.php');
 ?>
<body>
  <?php require('header.php'); ?>

<div class="container">
  <main class="maincontents page-1colum">
    <section class="section-schedule" id="main">
      <div class="schedule myPlan" id="myPlan">
        <div class="row">
          <div class="col">
            <h2 class="schedule-title">掲載中の集会</h2>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <table>
              <thead>
                <tr>
                  <!--<th class="id">集会id</th>--><th class="date">日付</th><th class="time">開始</th><th class="time">終了</th><th class="ship">鯖</th><th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th> <!--<th class="remarks">備考</th>--> <th class="change"></th><th class="delete"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($dbMyGatherData['data'] as $key => $value):?>
                  <tr>
                    <!--<td class="id"><?php //echo $value['id']; ?></td>--> <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td> <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td>
                    <td class="time"><?php $date['finish_time'] = strtotime($value['finish_time']); echo date('G:i', $date['finish_time']); ?></td>
                    <td class="ship"><?php if($value['ship'] == 12){ $value['ship'] = 'B';}elseif($value['ship'] == 11){$value['ship'] = 'C';} echo $value['ship']; ?></td>
                    <td class="block">B<?php echo $value['block']; ?></td> <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>" ><?php echo $value['gather_title']; ?></td>
                    <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td>
                    <!--<td class="remarks"><?php //echo $value['others']; ?></td>--><td class="change"><a href="regist.php?id=<?php echo $value['id']; ?>">変更</a></td> <td class="delete"><a href="cancellation.php?id=<?php echo $value['id']; ?>">中止</a></td>
                  </tr>
                  <!-- modal start -->
                  <div class="modal fade modal<?php echo $value['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close modal__bg" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <!--<p>id:<?php //echo $value['id']; ?></p>-->
                          <p>開催日:<?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?> <?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?> ~ <?php $date['finish_time'] = strtotime($value['finish_time']); echo date('G:i', $date['finish_time']); ?></p>
                          <p>集会:<?php echo $value['gather_title']; ?></p>
                          <p>タグ:<?php echo $value['tag']; ?></p>
                          <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo $value['block']; ?></p>
                          <p>備考:<?php echo $value['others']; ?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- modal end -->
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="schedule myCancelPlan" id="myCancelPlan">
        <div class="row">
          <div class="col">
            <h2 class="schedule-title">中止した企画</h2>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <table>
              <thead>
                <tr>
                  <!--<th class="id">集会id</th>--><th class="date">日付</th><th class="time">開始</th><th class="time">終了</th> <th class="ship">鯖</th> <th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th> <!--<th class="remarks">備考</th>--> <th class="change"></th><th class="delete"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($dbMyCancelGatherData['data'] as $key => $value):?>
                  <tr>
                    <!--<td class="id"><?php //echo $value['id']; ?></td>--> <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td> <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td>
                    <td class="time"><?php $date['finish_time'] = strtotime($value['finish_time']); echo date('G:i', $date['finish_time']); ?></td>
                    <td class="ship"><?php if($value['ship'] == 12){ $value['ship'] = 'B';}elseif($value['ship'] == 11){$value['ship'] = 'C';} echo $value['ship']; ?></td> <td class="block">B<?php echo $value['block']; ?></td>
                    <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>"><?php echo $value['gather_title']; ?></td> <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td>
                    <!--<td class="remarks"><?php //echo $value['others']; ?></td>--> <td class="change"><a href="regist.php?id=<?php echo $value['id']; ?>">復帰</a></td> <td class="delete"><a href="cancellation.php?id=<?php echo $value['id']; ?>">削除</a></td>
                  </tr>
                  <!-- modal start -->
                  <div class="modal fade modal<?php echo $value['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close modal__bg" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <!--<p>id:<?php //echo $value['id']; ?></p>-->
                          <p>開催日:<?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?> <?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?> ~ <?php $date['finish_time'] = strtotime($value['finish_time']); echo date('G:i', $date['finish_time']); ?></p>
                          <p>集会:<?php echo $value['gather_title']; ?></p>
                          <p>タグ:<?php echo $value['tag']; ?></p>
                          <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo $value['block']; ?></p>
                          <p>備考:<?php echo $value['others']; ?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- modal end -->
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- modal start -->
      <!--
      <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close modal__bg" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>id:<?php echo $value['id']; ?></p>
              <p>開催日:<?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']); ?> <?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?> ~ <?php $date['finish_time'] = strtotime($value['finish_time']); echo date('G:i', $date['finish_time']); ?></p>
              <p>集会:<?php echo $value['gather_title']; ?></p>
              <p>タグ:<?php echo $value['tag']; ?></p>
              <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo $value['block']; ?></p>
              <p>備考:<?php echo $value['others']; ?></p>
            </div>
          </div>
        </div>
      </div>
    -->
      <!-- modal end -->

    </section>
  </main>
</div>


<?php require('footer.php'); ?>
