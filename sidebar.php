<section class="section" id="sidebar">
  <p><?php echo date('Y-m-d'); ?></p>
  <p id="RealtimeClockArea2"></p>

  <?php //echo date('Y-m-d H:i:s'); ?><!--現在運用可能<br>
  サイドバー工事中-->
  <!--
  <blockquote class="twitter-tweet"><p lang="ja" dir="ltr"><a href="https://twitter.com/hashtag/PSO2%E9%9B%86%E4%BC%9A%E5%91%8A%E7%9F%A5?src=hash&amp;ref_src=twsrc%5Etfw">#PSO2集会告知</a> 集会主催者やまとめさん等に <a href="https://twitter.com/hashtag/%E6%8B%A1%E6%95%A3%E5%B8%8C%E6%9C%9B?src=hash&amp;ref_src=twsrc%5Etfw">#拡散希望</a><br>●纏め作るのが大変<br>●纏めても更新が遅れると重複する<br>以上の事を踏まえて、開催者や管理者が自由に登録無しで集会スケジュールを組んで纏める事ができる無料サイトを見つけ、専用板を組んだのでどうぞご利用ください。<a href="https://t.co/nTR8B3uibH">https://t.co/nTR8B3uibH</a></p>&mdash; ２Pカラーアスタルテ アテル＠次回ｰｰ/ｰｰ ＃ｰｰｰｰｰｰ (@Koutuki_Atelu) <a href="https://twitter.com/Koutuki_Atelu/status/1252181063398387712?ref_src=twsrc%5Etfw">April 20, 2020</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
  <blockquote class="twitter-tweet"><p lang="ja" dir="ltr">第二回ツインテ集会開催🌈✨<br>指定はツインテっぽいこと🍀<br>✾日にち 5/3 ✾場所B907<br>✾時間21時～23時<a href="https://twitter.com/hashtag/PSO2%E9%9B%86%E4%BC%9A%E5%91%8A%E7%9F%A5?src=hash&amp;ref_src=twsrc%5Etfw">#PSO2集会告知</a> <a href="https://twitter.com/hashtag/%E3%83%84%E3%82%A4%E3%83%B3%E3%83%86%E9%9B%86%E4%BC%9A?src=hash&amp;ref_src=twsrc%5Etfw">#ツインテ集会</a><a href="https://twitter.com/hashtag/%E3%83%95%E3%82%A9%E3%83%AD%E3%83%AF%E3%83%BC%E3%81%AE%E3%82%A2%E3%83%BC%E3%82%AF%E3%82%B9%E3%81%8Crt%E3%81%97%E3%81%A6%E3%81%8F%E3%82%8C%E3%81%A6%E3%81%BE%E3%81%A0%E8%A6%8B%E3%81%AC%E3%82%A2%E3%83%BC%E3%82%AF%E3%82%B9%E3%81%A8%E7%B9%8B%E3%81%8C%E3%82%8A%E3%81%9F%E3%81%84?src=hash&amp;ref_src=twsrc%5Etfw">#フォロワーのアークスがrtしてくれてまだ見ぬアークスと繋がりたい</a> <a href="https://t.co/nEcgZxPgNE">pic.twitter.com/nEcgZxPgNE</a></p>&mdash; みぃしゃ𝕤𝕙𝕚𝕡4♥5 (@misa_maria_d) <a href="https://twitter.com/misa_maria_d/status/1250372913259147264?ref_src=twsrc%5Etfw">April 15, 2020</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
-->
<?php //echo date('Y-m-d', strtotime('first day of next month', strtotime(date('Y-m-d')))); // 2017-02-01 ?><br>
<?php //echo date('t'); ?><br>

<?php for ($i=1; $i <= 3; $i++){ ?>
  <div class="month-select js-schedule-change js-<?php echo $i; ?>month-later js-monthlater">
    <?php //echo date('n月', strtotime($i.' month', strtotime(date('Y-m-1')))); ?>
  </div>
<?php } ?>
<?php /*
$end_add_date = date('Y-m-d', strtotime('1 month', strtotime(date('Y-m-1'))));
$end_year_add = date("Y", strtotime($end_add_date));
$end_month_add = date("m", strtotime($end_add_date));
$end_date = $end_year_add."-".$end_month_add."-1";
$end_datetime = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($end_date)));
echo $end_add_date.'</br>';
echo $end_year_add.'</br>';
echo $end_month_add.'<br>';
echo $end_date.'<br>';
echo $end_datetime.'<br>';
*/ ?>
</section>
