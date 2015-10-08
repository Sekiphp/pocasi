<?php     
  require "config.php";
  require "core.php";
  
  // zpracování dat z getu
  $rok = intVal($_GET['r']);
  $mesic = intVal($_GET['m']);
  
  // nadpis grafu
  $mesice = array("Leden", "Únor", "Bøezen", "Duben", "Kvìten", "Èerven", "Èervenec", "Srpen", "Záøí", "Øíjen", "Listopad", "Prosinec");
 
 
	header("Content-Type: image/png");
	$img = imageCreateTrueColor(1000, 680); // vyhlazování èar funguje jen na true color
      
  // Switch antialiasing on for one image
  //imageantialias($img, false);
	
	$bgcolor = imageColorAllocate($img, 255, 255, 255);
	$black = imageColorAllocate($img, 0, 0, 0);
	$red = imageColorAllocate($img, 255, 0, 0);
	$green = imageColorAllocate($img, 0, 128, 0);
	$blue = imageColorAllocate($img, 0, 0, 255);
	$gray = imageColorAllocate($img, 148, 148, 148);
  
  // pøebarvení true color
  imageFilledRectangle($img, 0, 0, 1000, 900, $bgcolor);
  	
	imgString($img, 25, 15, $mesice[$mesic-1] . " " . $rok . "; Kocanda 23 (Rokycany)", $black);
	   
	# vodorovné èáry
  // urceni rozsahu popisných os
  $mm = mysql_fetch_object(mysql_query("
    SELECT 
      MAX(t_rano) AS max_rano, 
      MIN(t_rano) AS min_rano, 
      MAX(t_odpoledne) AS max_odp, 
      MIN(t_odpoledne) AS min_odp,
      MAX(srazky) AS max_srazky, 
      MIN(srazky) AS min_srazky
    FROM `pocasi_" . $rok . "_" . $mesic . "`  
  "));
  
  // kresleni
	$transl = 60;
	$max = max5($mm->max_odp, $mm->max_rano);
  $maxSr = 30; //mm
	for ($i = 0; $i <= 31; $i++) {
    if ($i % 5 != 0) {
      $color = $gray;
    } else {
      $color = $black;
      //if ($max >= 0) {
        //imgString($img, 15, ($transl - 7), ((strlen("" . $max . "°C") == 3) ? " " . $maxSr . "mm" : $maxSr . "mm"), $blue);
        imgString($img, 15, ($transl - 7), $maxSr . "mm", $blue);      
        if ($max == 0)
          $nulovyY = $transl;
      //}
      $str = $max . "°C";
      switch (strLen($str)) {
        case 4: $string = " ";
          break;
        case 3: $string = "  "; 
          break;
        default: $string = "";
      }
      imgString($img, 55, ($transl - 7), $string . $str, $black);
      $max -= 5;
      $maxSr -= 5;
    }
		imageLine($img, 110, $transl, 650, $transl, $color);
		$transl += 17;
	}
  // ošetøení nulovéhoY - pokud není v grafu èíslo nula
  $nulovyYSr = ($transl - 35);
  if ($nulovyY == null) {
    $nulovyY = ($transl - 35) + ($max+5) * 17;
  }
	
	// svislé èáry
	$transl = 110;
	for ($i = 0; $i <= 31; $i++) {
    if ($i % 5 != 0) {
      $color = $gray;
    } else {
      $color = $black;
      imgString($img, ($transl - 7), 605, $i, $black);
    }
		imageLine($img, $transl, 50, $transl, 600, $color);
		$transl += 17;
	}
  
  # vykreslovani spojnicoveho grafu
  
  // kvuli prvni hodnote (posledni z predchoziho mesice)
  @$dold = mysql_fetch_object(mysql_query("SELECT * FROM `pocasi_" . predchoziMesic($rok, $mesic) . "` ORDER BY den DESC LIMIT 1"));
  // nastavení prvních koordinátù - jsou to vlastnì translace grafu
  $st8_x = 110 - 17;
  $detekce = mysql_errno();
                                            
  $st8_y = $nulovyY - $dold->t_rano * 17;   
  $st8_y1 = $nulovyY - $dold->t_odpoledne * 17;   
  $st8_y2 = $nulovyYSr - $dold->srazky * 17;
  
  $z = 0;
  $rch = $och = 0; // priznakove promenne - resi chybu s vykresnenim krivky pro chybejicich datech
  $data = mysql_query("SELECT * FROM `pocasi_" . $rok . "_" . $mesic . "`");
  while ($row = mysql_fetch_object($data)) {
    $y1 = $nulovyY - $row->t_rano * 17;
    $y2 = $nulovyY - $row->t_odpoledne * 17;      
    $y3 = $nulovyYSr - $row->srazky * 17;
    $st8_x += 17; 
    
    // pokud chybi data z predchoziho mesice  
  	$z++; 
  	if($z == 1 && $detekce == 1146) {    
      $st8_y = $y1;
      $st8_y1 = $y2;
      $st8_y2 = $y3;
			continue;
		}
    // ranní teplota
    if ($row->t_rano != -99.9){    
      if ($rch == 0) {
        imageStrokeLine($img, $st8_x, $st8_y, ($st8_x + 17), $y1, $red);        
      }
      else {
        $rch--;
      }  
      $st8_y = $y1; 
    }
    else {
      $rch = 1;
    }
    
    // odpolednÍ teplota
    if ($row->t_odpoledne != -99.9){    
      if ($och == 0) {
        imageStrokeLine($img, $st8_x, $st8_y1, ($st8_x + 17), $y2, $green);        
      }
      else {
        $och--;
      }  
      $st8_y1 = $y2; 
    }
    else {
      $och = 1;
    }
    
    // srazky
    if ($row->srazky >= 0){
      imageStrokeLine($img, $st8_x, $st8_y2, ($st8_x + 17), $y3, $blue);    
      $st8_y2 = $y3;
    } 
  }   
	
	# legenda
	imageFilledRectangle($img, 695, 65, 965, 150, $gray);	
	imageFilledRectangle($img, 691, 61, 959, 144, $bgcolor);	
	imageRectangle($img, 690, 60, 960, 145, $black);	
		// ètvereèek s barvou
		imageFilledRectangle($img, 700, 70, 715, 85, $red);
		imageFilledRectangle($img, 700, 95, 715, 110, $green);
		imageFilledRectangle($img, 700, 120, 715, 135, $blue);
	
		// ohranièení ètvereèku s barvou
		imageRectangle($img, 699, 69, 716, 86, $black);
		imageRectangle($img, 699, 94, 716, 111, $black);
		imageRectangle($img, 699, 119, 716, 136, $black);
		
		// popisky ètvereèkù
		imgString($img, 725, 70, "Stupnì °C v 8 hodin", $black);
		imgString($img, 725, 95, "Stupnì °C v 15 hodin", $black);
		imgString($img, 725, 120, "De¹»ové srá¾ky za 24 hodin", $black);
		
	// statistické pøehledy
  $st = mysql_fetch_object(mysql_query("
    SELECT 
      MAX(t_rano) AS max_rano, 
      MIN(t_rano) AS min_rano,
      AVG(t_rano) AS avg_rano, 
      MAX(t_odpoledne) AS max_odp, 
      MIN(t_odpoledne) AS min_odp,
      AVG(t_odpoledne) AS avg_odp, 
      MAX(srazky) AS max_srazky, 
      SUM(srazky) AS sum_srazky,
      AVG(srazky) AS avg_srazky 
    FROM `pocasi_" . $rok . "_" . $mesic . "` 
    WHERE `t_rano` != -99.9 AND `t_odpoledne` != -99.9  
  "));
  $ms_datum = mysql_result(mysql_query("SELECT den FROM `pocasi_" . $rok . "_" . $mesic . "` ORDER BY srazky DESC LIMIT 1"), 0);
  $maxtr = mysql_result(mysql_query("SELECT den FROM `pocasi_" . $rok . "_" . $mesic . "` ORDER BY t_rano DESC LIMIT 1"), 0);
  $mintr = mysql_result(mysql_query("SELECT den FROM `pocasi_" . $rok . "_" . $mesic . "` WHERE `t_rano` != -99.9 ORDER BY t_rano ASC LIMIT 1"), 0);
  $maxto = mysql_result(mysql_query("SELECT den FROM `pocasi_" . $rok . "_" . $mesic . "` ORDER BY t_odpoledne DESC LIMIT 1"), 0);
  $minto = mysql_result(mysql_query("SELECT den FROM `pocasi_" . $rok . "_" . $mesic . "` WHERE `t_odpoledne` != -99.9 ORDER BY t_odpoledne ASC LIMIT 1"), 0);
 
  // floatVal - 1.0 -> 1 a 1.1 -> 1.1 amazing! :)
	imgString($img, 690, 170, "De¹»ové srá¾ky celkem " . floatVal($st->sum_srazky) . " mm.", $black);
	imgString($img, 690, 185, "Denní prùmìr " . floatVal(round($st->avg_srazky, 2)) . " mm.", $black);
  imgString($img, 690, 200, "Maximální srá¾ky " . floatVal($st->max_srazky) . " mm (" . $ms_datum . "." . $mesic . ".)", $black);
    
  imgString($img, 690, 230, "Prùmìrná ranní teplota " . floatVal(round($st->avg_rano, 1)) . "°C", $black);
  imgString($img, 690, 245, "Max. ranní teplota " . floatVal(ceil($st->max_rano)) . "°C (" . $maxtr . "." . $mesic . ".)", $black);
  imgString($img, 690, 260, "Min. ranní teplota " . floatVal($st->min_rano) . "°C (" . $mintr . "." . $mesic . ".)", $black);
    
  imgString($img, 690, 290, "Prùmìrná odpol. teplota " . floatVal(round($st->avg_odp, 1)) . "°C", $black);
  imgString($img, 690, 305, "Max. odpol. teplota " . floatVal(ceil($st->max_odp)) . "°C (" . $maxto . "." . $mesic . ".)", $black);
  imgString($img, 690, 320, "Min. odpol. teplota " . floatVal($st->min_odp) . "°C (" . $minto . "." . $mesic . ".)", $black);
	 
	imagePng($img);
	imageDestroy($img);  