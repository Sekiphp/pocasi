<?php
  function describe($table) {
    return mysql_query("DESCRIBE `" . $table . "`");  
  }
  
  function max5($num1, $num2) {
    $max = max($num1, $num2);
    
    $i = 5;
    while(true) {
      if($i > $max)   // rovn�se ned�vat - kv�li rezerv� ve sr�k�ch
        return $i;
      $i += 5;  
    }
  }
  
  function maxTL($max) {
    // osetreni kvuli absenci tlaku cely mesic - rozumne rozmezi
    if ($max == -1000)
      return 1020;
      
    $i = 5;
    while(true) {
      if($i >= $max)   // rovn�se ned�vat - kv�li rezerv� ve sr�k�ch
        return $i;
      $i += 5;  
    }
  }
  
  function imageCreate1($width, $height) {
  	header("Content-Type: image/png");
  	$img = imageCreateTrueColor(1000, 670); // vyhlazov�n� �ar funguje jen na true color
    imageFilledRectangle($img, 0, 0, 1000, 900, imageColorAllocate($img, 255, 255, 255));     
    //imageantialias($img, TRUE);  
    
    return $img;
  }
  
  function predchoziMesic($rok, $mesic) {
    if ($mesic == 1)
      return ($rok-1) . "_12";  
    return $rok . "_" . ($mesic-1);
  }
  
	function imgString ($img, $x, $y, $text, $color) {
		imageString($img, 5, $x, $y, $text, $color);
	}
  
  function imageStrokeLine($img, $x1, $y1, $x2, $y2, $color) {
    imageLine($img, $x1, $y1+1, $x2, $y2+1, $color);
    imageLine($img, $x1, $y1, $x2, $y2, $color);
    imageLine($img, $x1, $y1-1, $x2, $y2-1, $color);
  }
  
  function mesice($mesic) {
    $mesice = array("Leden", "�nor", "B�ezen", "Duben", "Kv�ten", "�erven", "�ervenec", "Srpen", "Z���", "��jen", "Listopad", "Prosinec");
    return $mesice[$mesic];
  }
  
  // chybova hodnota pokud chybi data = -1000
  function err_v($num) {
    return (($num == null) ? -1000 : $num);
  }
