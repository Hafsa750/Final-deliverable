<?php
  error_reporting(E_ALL ^ E_DEPRECATED);
  session_start();

  session_destroy();
  header("refresh: 0; URL = login.php");
?>