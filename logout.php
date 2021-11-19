<?php
require('function.php');
debug('ログアウトします');
session_destroy();
header("Location:index.php");
 ?>
