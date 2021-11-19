<?php
require('function.php');
debugTitle('パイソンPHP接続テスト');
debugLogStart();

 ?>

 <?php
 $siteTitle = 'pythonテスト';
 require('head.php');
  ?>
  <body>
    <?php require('header.php'); ?>

    <div class="container">
      <main class="maincontents page-1colum">
        <section class="" id="main">
          <div class="row">
            <div class="col">
              <h2>pythonテストページ</h2>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <?php
              /*$python = "test.py";
              $json = file_get_contents($python);
              $arr = json_decode($json, true);*/
              #$json = "test.json";
              $json = "package.json";
              $json = file_get_contents($json);
              $json = mb_convert_encoding($json, 'UTF8', 'ASCII, JIS, UTF-8, EUC-JP, SJIS-WIN');
              $arr = json_decode($json, true);
              var_dump($arr);
              foreach ($arr as $data) :
                //$contents = "id:".$data."\n"."<br>".PHP_EOL;
                /*$tweet_data  = array(
                  $id
                );*/
                //echo implode('', $contents);
              endforeach;

               ?>
              <?php
                //$x = exec('python test.py');
                #$command = 'python test.py';
                #$command = 'python twitterscraping.py';
                #exec($command, $output);
                //echo json_decode(json_encode($x))[0];
                #echo $output[0];
                #print "$output[0]\n";
                #print "$output[1]\n"
                #print "$output[0]\n";
               ?>
              <?php
              //exec('export LANG=ja_JP.UTF-8; test.py',$ret_array);

               ?>
            </div>
          </div>
        </section>
      </main>
    </div>

    <?php require('footer.php'); ?>
