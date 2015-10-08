<?php
  # import ridicich struktur
  require "config.php";
  require "core.php";
  
  # zpracování dat z getu
  $rok = intVal($_GET['r']);
  $mesic = intVal($_GET['m']);
  
  
  # vytvori obrazek se zapnutym anti-aliasingem
	$img = imageCreate1(1000, 670);    
	
	$bgcolor = imageColorAllocate($img, 255, 255, 255);
	$black = imageColorAllocate($img, 0, 0, 0);
	$red = imageColorAllocate($img, 255, 0, 0);
	$green = imageColorAllocate($img, 0, 128, 0);
	$blue = imageColorAllocate($img, 0, 0, 255);
	$gray = imageColorAllocate($img, 148, 148, 148);

  # nadpis grafu	
	imgString($img, 25, 15, mesice($mesic-1) . " " . $rok . "; Kocanda 23 (Rokycany)", $black);
	
	# vodorovné èáry
  // urceni rozsahu popisných os
  $data = mysql_fetch_object(mysql_query("
    SELECT 
      MAX(v_rano) AS max_rano, 
      MAX(v_odpoledne) AS max_odp, 
      MAX(tlak) AS max_tlak 
    FROM `pocasi_" . $rok . "_" . $mesic . "`  
  "));
  
  // kresleni
	$transl = 60;
	$maxVL = max5($data->max_odp, $data->max_rano);
  $maxTL = maxTL($data->max_tlak);
       
	for ($i = 0; $i <= 31; $i++) {
    if ($i % 5 != 0) {
      $color = $gray;
    } else {  
      $color = $black;
      if ($maxVL >= 0) {
        imgString($img, 0, ($transl - 7), ((strlen($maxTL) == 3) ? " " : ""). $maxTL . "hPa", $blue);      
      }
      
      if ($i == 0) {
        // y nejvyssi cary
        $maxY = $transl;
        $maxYTL = $maxTL;
        $maxYVL = $maxVL;
      }

      imgString($img, 55, ($transl - 7), "  " . $maxVL . "%", $black);
      $maxVL -= 10; 
      $maxTL -= 10;
    }
		imageLine($img, 110, $transl, 650, $transl, $color);
		$transl += 17;
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
  $detekce = mysql_errno();
  // nastavení prvních koordinátù - jsou to vlastnì translace grafu
  $st8_x = 110 - 17;
                                            
  $st8_y = 60 + ($maxYVL - $dold->v_rano) * 8.5;
  $st8_y1 = 60 + ($maxYVL - $dold->v_odpoledne) * 8.5;
  $st8_y2 = 60 + ($maxYTL - $dold->tlak) * 8.5;
   
  $z = 0;
  $vch = $och = $tch = 0; // priznaky
  $data = mysql_query("SELECT * FROM `pocasi_" . $rok . "_" . $mesic . "`");
  while ($row = mysql_fetch_object($data)) {
    $y1 = 60 + ($maxYVL - $row->v_rano) * 8.5;
    $y2 = 60 + ($maxYVL - $row->v_odpoledne) * 8.5;      
    $y3 = 60 + ($maxYTL - $row->tlak) * 8.5;
    $st8_x += 17;
    
    // pokud chybi data z predchoziho mesice  
  	$z++; 
  	if($z == 1 && $detekce == 1146) {    
      $st8_y = $y1;
      $st8_y1 = $y2;
      $st8_y2 = $y3;
			continue;
		}  
             
    // ranní vlhkost 
    if ($row->v_rano > 0){
      if ($vch == 0) {
        imageStrokeLine($img, $st8_x, $st8_y, ($st8_x + 17), $y1, $red);        
      }
      else {
        $vch--;
      }  
      $st8_y = $y1; 
    }
    else {
      $vch = 1;
    }    
    
    // odpolední vlhkost
    if ($row->v_odpoledne > 0){
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
    
    // tlak
    if ($row->tlak > 0){
      if ($tch == 0) {
        // osetreni ze jde cara strasne zezdola pri chybejicich datech
        if ($st8_y2 < 1000) {
          imageStrokeLine($img, $st8_x, $st8_y2, ($st8_x + 17), $y3, $blue);
        }      
      }
      else {
        $tch--;
      }  
      $st8_y2 = $y3;   
    }
    else {
      $tch = 1;
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
		imgString($img, 725, 70, "Vlhkost vzduchu v 8 hodin", $black);
		imgString($img, 725, 95, "Vlhkost vzduchu v 15 hodin", $black);
		imgString($img, 725, 120, "Atm. tlak v hPa v 8 hodin", $black);
		
  // informaèní údaje vpravo  
	imgString($img, 690, 170, "Vlhkost vzduchu v %", $black);
	imgString($img, 690, 185, "Nadmoøská vý¹ka 384 mnm (google)", $black);
  imgString($img, 690, 200, "Nadmoøská vý¹ka 395 mnm", $black);
  imgString($img, 690, 215, "(døíve udávaná do r. 2009)", $black);
  
	
	imagePng($img);
	imageDestroy($img);