<?php
//開発用
require('function.php');
$sesLimit = 60 * 60 * 24 * 7;
$_SESSION['login_date'] = time();
$_SESSION['login_limit'] = $sesLimit;
$_SESSION['user_id'] = 1;
header("Location:index.php");
 ?>
