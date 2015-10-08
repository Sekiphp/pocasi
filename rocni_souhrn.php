<?php
  require "config.php";
  require "core.php";
  
  // zpracování dat z getu
  $rok = intVal($_GET['r']);
  
  $mesice = array("lednu", "únoru", "bøeznu", "dubnu", "kvìtnu", "èervnu", "èervenci", "srpnu", "záøí", "øíjnu", "listopadu", "prosinci");
   
	header("Content-Type: image/png");
	$img = imageCreateTrueColor(1000, 680); // vyhlazování èar funguje jen na true color
      
  // Switch antialiasing on for one image
  //imageantialias($img, TRUE);
	
	$bgcolor = imageColorAllocate($img, 255, 255, 255);
	$black = imageColorAllocate($img, 0, 0, 0);
	$red = imageColorAllocate($img, 255, 0, 0);
	$green = imageColorAllocate($img, 0, 128, 0);
	$blue = imageColorAllocate($img, 0, 0, 255);
	$gray = imageColorAllocate($img, 148, 148, 148);
  
  // pøebarvení true color
  imageFilledRectangle($img, 0, 0, 1000, 900, $bgcolor);
  
  // nadpis grafu	
	imgString($img, 25, 15, $rok . " - Roèní souhrn dat; Kocanda 23 (Rokycany)", $black);
        
  // ziskavani dat
  $data = array();
  for($i = 1; $i <= 12; $i++) {
    $table = "`pocasi_" . $rok . "_" . $i . "`";
    
    $data[] = mysql_fetch_object(mysql_query("
      SELECT
        COUNT(*) AS dni,   
        
        SUM(srazky) AS sum_srazky,    
          
        SUM(t_rano) AS sum_rano,  
        MAX(t_rano) AS max_rano, 
        MIN(t_rano) AS min_rano, 
          
        SUM(t_odpoledne) AS sum_odp,  
        MAX(t_odpoledne) AS max_odp,    
        MIN(t_odpoledne) AS min_odp          
      FROM $table 
      WHERE t_odpoledne != -99.9 AND t_rano != -99.9  
    "));  
  }
  /*
  echo "<pre>";
  print_r($data);
  */
  //echo $data[0]->dni;
  $s_srazky_x1 = $s_rano_x1 = $s_odpol_x1 = 110;
  $s_srazky_y1 = 570;
  $s_rano_y1 = $s_odpol_y1 = 485;
  $sum_dni = $sum_srazky = $max_srazky = $sum_rano = $max_rano = 0;
  $max_srazky_mesic = $min_srazky_mesic = $max_rano_mesic = $min_rano_mesic = $max_odpoledne_mesic = $min_odpoledne_mesic = 0;
  $min_srazky = $min_rano = $min_odpoledne = 5000; // musí být obøí
  for($i = 0; $i <= 11; $i++) {
    $sum_dni += $data[$i]->dni;
    
    // srazky
    $sum_srazky += $data[$i]->sum_srazky;     
    if($data[$i]->sum_srazky > $max_srazky){
      $max_srazky = $data[$i]->sum_srazky;
      $max_srazky_mesic = $i;
    }
    if($data[$i]->sum_srazky < $min_srazky){
      $min_srazky = $data[$i]->sum_srazky;
      $min_srazky_mesic = $i;
    }
    
    // ranni teplota
    $sum_rano += $data[$i]->sum_rano;     
    if($data[$i]->max_rano > $max_rano){
      $max_rano = $data[$i]->max_rano;
      $max_rano_mesic = $i;
      $s_min_r = $data[$i]->sum_rano / $data[$i]->dni;
    }
    if($data[$i]->min_rano < $min_rano){
      $min_rano = $data[$i]->min_rano;
      $min_rano_mesic = $i;
      $s_max_r = $data[$i]->sum_rano / $data[$i]->dni;
    }  
    
    // odpoledni teplota
    $sum_odpoledne += $data[$i]->sum_odp;   
    if($data[$i]->max_odp > $max_odpoledne){
      $max_odpoledne = $data[$i]->max_odp;
      $max_odpoledne_mesic = $i;
      $s_min_r = $data[$i]->sum_rano / $data[$i]->dni;
    }
    if($data[$i]->min_odp < $min_odpoledne){
      $min_odpoledne = $data[$i]->min_odp;
      $min_odpoledne_mesic = $i;
      $s_max_r = $data[$i]->sum_rano / $data[$i]->dni;
    } 
    
    // vykresleni srazkove krivky
    $s_srazky_y2 = 570 - (17 * $data[$i]->sum_srazky)/10;
    imageStrokeLine($img, $s_srazky_x1, $s_srazky_y1, ($s_srazky_x1 + 15*3), $s_srazky_y2, $blue);
    $s_srazky_y1 = $s_srazky_y2;
    $s_srazky_x1 += 15*3; 
    
    // vykresleni ranni krivky
    $s_rano_y2 = 570 - ($data[$i]->sum_rano / $data[$i]->dni) * 17 - 5*17;
    imageStrokeLine($img, $s_rano_x1, $s_rano_y1, ($s_rano_x1 + 15*3), $s_rano_y2, $red);
    $s_rano_y1 = $s_rano_y2;
    $s_rano_x1 += 15*3; 
    
    // vykresleni odpoledni krivky
    $s_odpol_y2 = 570 - ($data[$i]->sum_odp / $data[$i]->dni) * 17 - 5*17;
    imageStrokeLine($img, $s_odpol_x1, $s_odpol_y1, ($s_odpol_x1 + 15*3), $s_odpol_y2, $green);
    $s_odpol_y1 = $s_odpol_y2;
    $s_odpol_x1 += 15*3; 
   
  }
  
	  
	# vodorovné èáry
  
  // kresleni
	$transl = 60;
	$max = 25;
  $maxSr = 300; //mm
	for ($i = 0; $i <= 31; $i++) {
    if ($i % 5 != 0) {
      $color = $gray;
    } else {
      $color = $black;

      imgString($img, 15, ($transl - 7), $maxSr . "mm", $blue);      
      if ($max == 0)
        $nulovyY = $transl;

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
      $maxSr -= 50;
    }
		imageLine($img, 110, $transl, 650, $transl, $color);
		$transl += 17;
	}
  // o¹etøení nulovéhoY - pokud není v grafu èíslo nula
  $nulovyYSr = ($transl - 35);
  if ($nulovyY == null) {
    $nulovyY = ($transl - 35) + ($max+5) * 17;
  }
	
	// svislé èáry
	$transl = 110;
	for ($i = 0; $i <= 12; $i++) {
    imgString($img, ($transl - 7), 605, $i, $black);
		imageLine($img, $transl, 50, $transl, 600, $black);
		$transl += 15*3;
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
		imgString($img, 725, 120, "De¹»ové srá¾ky", $black);
		
	// statistické pøehledy
  // floatVal - 1.0 -> 1 a 1.1 -> 1.1 amazing! :)
	imgString($img, 690, 170, "De¹»ové srá¾ky celkem " . floatVal($sum_srazky) . " mm.", $black);
	imgString($img, 690, 185, "Mìsíèní prùmìr " . floatVal(round($sum_srazky/12, 1)) . " mm.", $black);
  imgString($img, 690, 200, "Max. srá¾ky v " . $mesice[$max_srazky_mesic] . " " . floatVal($max_srazky) . " mm", $black);
  imgString($img, 690, 215, "Min. srá¾ky v " . $mesice[$min_srazky_mesic] . " " . floatVal($min_srazky) . " mm", $black);
    
  imgString($img, 690, 245, "Prùm. roèní ranní teplota " . floatVal(round($sum_rano / $sum_dni, 1)) . "°C", $black);
  imgString($img, 690, 260, "Max. ranní teplota v " . $mesice[$max_rano_mesic] . " " . floatVal($max_rano) . "°C", $black);
  imgString($img, 690, 275, "Min. ranní teplota v " . $mesice[$min_rano_mesic] . " " . floatVal($min_rano) . "°C", $black);
    
  imgString($img, 690, 305, "Prùm. roèní odpol. teplota " . floatVal(round($sum_odpoledne / $sum_dni, 1)) . "°C", $black);
  imgString($img, 690, 320, "Max. odpol. teplota " . $mesice[$max_odpoledne_mesic] . " " . floatVal($max_odpoledne) . "°C", $black);
  imgString($img, 690, 335, "Min. odpol. teplota " . $mesice[$min_odpoledne_mesic] . " " . floatVal($min_odpoledne) . "°C", $black);
	                                                            
	imagePng($img);
	imageDestroy($img);   