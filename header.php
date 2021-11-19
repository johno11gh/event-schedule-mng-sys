<div class="container-fluid background-header">
  <div class="row">
    <div class="col">
      <div class="container">
        <header class="header  background-header" id="header">
          <h1 class="header-title"><a class="header-title-link" href="index.php">PSO2集会管理システム</a></h1>
          <div class="menu-trigger js-toggle-sp-menu">
            <span></span>
            <span></span>
            <span></span>
          </div>
          <nav class="nav-menu js-toggle-sp-menu-target">
            <ul class="menu">
              <li class="menu-item"><a class="menu-link" href="index.php">トップ</a></li>
              <li class="menu-item"><a class="menu-link" href="news.php">お知らせ</a></li>
              <li class="menu-item"><a class="menu-link" href="howtouse.php">使い方</a></li>
              <?php
              //echo '<li class="menu-item"><a class="menu-link" href="phpython.php">パイソンテスト</a></li>';
              //echo '<li class="menu-item"><a class="menu-link" href="phpinfo.php">phpinfo</a></li>';
               ?>

              <?php
              if(isset($_SESSION['user_id'])){ ?>
                <li class="menu-item"><a class="menu-link" href="regist.php">集会新規登録</a></li>
                <li class="menu-item"><a class="menu-link" href="mypage.php">マイページ</a></li>
                <li class="menu-item"><a class="menu-link"><?php //echo $_SESSION['access_token']['screen_name']; ?></a></li>
                <li class="menu-item"><img src="<?php //echo $_SESSION['access_token']['profile_image_url_https']; ?>" alt=""></li>
              <?php }else{ ?>
                <li class="menu-item"><a class="menu-link" href="login.php">ログイン</a></li>
              <?php } ?>
            </ul>
          </nav>
        </header>
      </div>
    </div>
  </div>
</div>
