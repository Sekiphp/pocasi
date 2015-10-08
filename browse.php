<!DOCTYPE HTML>
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <style type='text/css'>
    a {
      display: block;
    }
  </style>
  <style media="print">
    a, br {
      display: none;
    }  
    img {
      padding: 0px;
      margin: 0px;
    }
  </style>
  <?php
    list($rok, $mesic) = exPlode("_", $_GET['d']);
  ?>
  <title>Počasí Kamenný Újezd u Rokycan (<?=$mesic?>/<?=$rok?>)</title>
  </head>
  <body>  
    <a href='index.php'>Zpět na výběr měsíce</a>
    <img src='b_page1.php?r=<?=$rok?>&m=<?=$mesic?>'>
    <img src='b_page2.php?r=<?=$rok?>&m=<?=$mesic?>'> 
  </body>
</html>
