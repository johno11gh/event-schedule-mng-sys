<?php
require('function.php');
debugTitle('トップページ');
debugLogStart();
//require('auth.php'); //リマインダー機能を実装したら必須にする
require('head.php');
$dbBattleGatherData = getBattleGatherData();
$dbChallengeGatherData = getChallengeGatherData();
$dbNormalGatherData = getNormalGatherData();

function echo_block($str){
  if($str >= 10000){
    $str -= 10000;
    return '撮影'.$str;
  }else{
    return $str;
  }
}

 ?>

<body>
  <?php require('header.php'); ?>

<div class="container">
  <main class="maincontents page-1colum">
    <section class="section-schedule" id="main">
      <div class="month">
        <div class="row">
          <div class="col">
            <div class="month-select js-schedule-change js-this-month">
              <?php echo date('n月'); ?>
            </div>
            <?php for ($i=1; $i <= 3; $i++){ ?>
              <div class="month-select js-schedule-change js-<?php echo $i; ?>month-later js-monthlater">
                <?php echo date('n月', strtotime($i.' month', strtotime(date('Y-m-1')))); ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="schedule-bymonth js-schedule-change-target js-this-month-target action">
        <div class="month-name">
          <div class="row">
            <div class="col">
              <h2 class="month-name-title"><?php echo date('n月集会予定'); ?></h2>
            </div>
          </div>
        </div>
        <div class="schedule battle">
          <div class="row">
            <div class="col">
              <h2 class="schedule-title">共通バトル</h2>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <table>
                <thead>
                  <tr>
                    <th class="date">日付</th><th class="time">時刻</th><th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th><th class="organizer">主催者</th><!--<td class="remarks">備考</td>-->
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($dbBattleGatherData['data'] as $key => $value):?>
                    <tr>
                      <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td> <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td>
                      <td class="block">B<?php echo $value['block']; ?></td> <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>"><?php echo $value['gather_title']; ?></td>
                      <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td> <td class="organizer">@<?php echo $value['twitter_account']; ?></td>
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
                            <p>主催者:<?php echo $value['twitter_account']; ?></p>
                            <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo $value['block']; ?></p>
                            <p>告知ツイート: <a target="_blank" rel="noopener noreferrer" href="<?php if($value['tweet_url'] != NULL){ echo 'https://twitter.com/'.$value['twitter_account'].'/status/'.$value['tweet_url']; } ?>">該当ツイートはこちら</a></p>
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
        <div class="schedule challenge">
          <div class="row">
            <div class="col">
              <h2 class="schedule-title">共通チャレンジ</h2>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <table>
                <thead>
                  <tr>
                    <th class="date">日付</th><th class="time">時刻</th><th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th><th class="organizer">主催者</th><!--<td class="remarks">備考</td>-->
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($dbChallengeGatherData['data'] as $key => $value):?>
                    <tr>
                      <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td>
                      <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td> <td class="block">B<?php echo $value['block']; ?></td>
                      <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>"><?php echo $value['gather_title']; ?></td>
                      <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td> <td class="organizer">@<?php echo $value['twitter_account']; ?></td>
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
                            <p>主催者:<?php echo $value['twitter_account']; ?></p>
                            <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo $value['block']; ?></p>
                            <p>告知ツイート: <a target="_blank" rel="noopener noreferrer" href="<?php if($value['tweet_url'] != NULL){ echo 'https://twitter.com/'.$value['twitter_account'].'/status/'.$value['tweet_url']; } ?>">該当ツイートはこちら</a></p>
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
        <div class="schedule normal">
          <div class="row">
            <div class="col">
              <h2 class="schedule-title">所属シップ</h2>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <table>
                <thead>
                  <tr>
                    <th class="date">日付</th><th class="time">時刻</th><th class="ship">鯖</th><th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th><th class="organizer">主催者</th><!--<td class="remarks">備考</td>-->
                  </tr>
                </thead>
                <tbody>
                  <!--<tr>
                    <td class="date">4月21日(火)</td> <td class="time">21:00</td> <td class="ship">3</td><td class="block">998</td> <td class="gather-title">RPer創作勢集会 -2020 First-</td> <td class="tag">#test</td> <td class="organizer">test</td>
                  </tr> -->
                  <?php foreach ($dbNormalGatherData['data'] as $key => $value):?>
                    <tr>
                      <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td>
                      <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td> <td class="ship"><?php echo $value['ship']; ?></td>
                      <td class="block">B<?php echo echo_block($value['block']); ?></td> <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>"><?php echo $value['gather_title']; ?></td>
                      <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td> <td class="organizer">@<?php echo $value['twitter_account']; ?></td>
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
                            <p>主催者:<?php echo $value['twitter_account']; ?></p>
                            <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo echo_block($value['block']); ?></p>
                            <p>告知ツイート: <a target="_blank" rel="noopener noreferrer" href="<?php if($value['tweet_url'] != NULL){ echo 'https://twitter.com/'.$value['twitter_account'].'/status/'.$value['tweet_url']; } ?>">該当ツイートはこちら</a></p>
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
      </div>

    <?php for ($i=1; $i <= 3; $i++) { ?>
      <?php
      $dbMonthryBattleGatherData = getMonthryBattleGatherData($i);
      $dbMonthryChallengeGatherData = getMonthryChallengeGatherData($i);
      $dbMonthryNormalGatherData = getMonthryNormalGatherData($i);
        ?>
      <div class="schedule-bymonth js-schedule-change-target js-<?php echo $i; ?>month-later-target js-monthlater-target">
        <div class="month-name">
          <div class="row">
            <div class="col">
              <h2 class="month-name-title"><?php echo date('n月', strtotime($i.' month', strtotime(date('Y-m-1')))).'集会予定'; ?></h2>
            </div>
          </div>
        </div>
        <div class="schedule battle">
          <div class="row">
            <div class="col">
              <h2 class="schedule-title">共通バトル</h2>
            </div>
          </div>
            <div class="row">
              <div class="col">
                <table>
                  <thead>
                    <tr>
                      <th class="date">日付</th><th class="time">時刻</th><th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th><th class="organizer">主催者</th><!--<td class="remarks">備考</td>-->
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($dbMonthryBattleGatherData['data'] as $key => $value):?>
                    <tr>
                      <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td> <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td>
                      <td class="block">B<?php echo $value['block']; ?></td> <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>"><?php echo $value['gather_title']; ?></td>
                      <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td> <td class="organizer">@<?php echo $value['twitter_account']; ?></td>
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
                            <p>主催者:<?php echo $value['twitter_account']; ?></p>
                            <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo $value['block']; ?></p>
                            <p>告知ツイート: <a target="_blank" rel="noopener noreferrer" href="<?php if($value['tweet_url'] != NULL){ echo 'https://twitter.com/'.$value['twitter_account'].'/status/'.$value['tweet_url']; } ?>">該当ツイートはこちら</a></p>
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
        <div class="schedule challenge">
          <div class="row">
            <div class="col">
              <h2 class="schedule-title">共通チャレンジ</h2>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <table>
                <thead>
                  <tr>
                    <th class="date">日付</th><th class="time">時刻</th><th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th><th class="organizer">主催者</th><!--<td class="remarks">備考</td>-->
                  </tr>
                </thead>
                <tbody>
                <?php foreach ($dbMonthryChallengeGatherData['data'] as $key => $value):?>
                  <tr>
                    <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td>
                    <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td>
                    <td class="block">B<?php echo $value['block']; ?></td> <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>"><?php echo $value['gather_title']; ?></td>
                    <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td> <td class="organizer">@<?php echo $value['twitter_account']; ?></td>
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
                          <p>主催者:<?php echo $value['twitter_account']; ?></p>
                          <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo $value['block']; ?></p>
                          <p>告知ツイート: <a target="_blank" rel="noopener noreferrer" href="<?php if($value['tweet_url'] != NULL){ echo 'https://twitter.com/'.$value['twitter_account'].'/status/'.$value['tweet_url']; } ?>">該当ツイートはこちら</a></p>
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
        <div class="schedule normal">
          <div class="row">
            <div class="col">
              <h2 class="schedule-title">所属シップ</h2>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <table>
                <thead>
                  <tr>
                    <th class="date">日付</th><th class="time">時刻</th><th class="ship">鯖</th><th class="block">場所</th><th class="gathering">集会名</th><th class="tag">タグ</th><th class="organizer">主催者</th><!--<td class="remarks">備考</td>-->
                  </tr>
                </thead>
                <tbody>
                  <!--<tr>
                    <td class="date">4月21日(火)</td> <td class="time">21:00</td> <td class="ship">3</td><td class="block">998</td> <td class="gather-title">RPer創作勢集会 -2020 First-</td> <td class="tag">#test</td> <td class="organizer">test</td>
                  </tr> -->
                  <?php foreach ($dbMonthryNormalGatherData['data'] as $key => $value):?>
                  <tr>
                    <td class="date"><?php $date['start_time'] = strtotime($value['start_time']); echo date('n月d日', $date['start_time']).'('.$week[date('w', strtotime($value['start_time']))].')'; ?></td>
                    <td class="time"><?php $date['start_time'] = strtotime($value['start_time']); echo date('G:i', $date['start_time']); ?></td>
                    <td class="ship"><?php echo $value['ship']; ?></td> <td class="block">B<?php echo echo_block($value['block']); ?></td> <td class="gather-title js-modal-toggle" data-target="modal<?php echo $value['id']; ?>"><?php echo $value['gather_title']; ?></td>
                    <td class="tag"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/search?q=%23<?php echo $value['tag']; ?>&src=typed_query">#<?php echo $value['tag']; ?></a></td> <td class="organizer">@<?php echo $value['twitter_account']; ?></td>
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
                          <p>主催者:<?php echo $value['twitter_account']; ?></p>
                          <p>場所:<?php if($value['ship'] == 12){$value['ship'] = '共通バトル';}elseif($value['ship'] == 11){$value['ship'] = '共通チャレンジ';} echo $value['ship']; ?>鯖 B-<?php echo echo_block($value['block']); ?></p>
                          <p>告知ツイート: <a target="_blank" rel="noopener noreferrer" href="<?php if($value['tweet_url'] != NULL){ echo 'https://twitter.com/'.$value['twitter_account'].'/status/'.$value['tweet_url']; } ?>">該当ツイートはこちら</a></p>
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
      </div>
    <?php } ?>
    </section>

    <!-- sidebar-->
    <?php //require('sidebar.php') ?>

  </main>
</div>


<?php require('footer.php'); ?>
