<?php
require_once("config.php");
require_once "core.php";

function mres($in) {
  return mysql_real_escape_string($in);
}

function pocetDnuMesice ($year, $month) {
  return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}
?>
<!DOCTYPE HTML>
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Grafy k počasí</title>
    <style type='text/css'>
      body {
        margin: 20px;
      }
      ul {
        margin: 0px 0px 10px 20px;
      } 
      small {
        display: block;      
        margin-bottom: 10px;
      }  
      .bold {
        font-weight: bold;
        cursor: pointer;
      }  
      input {
        border: 1px solid orange;
        width: 100px;
      } 
      .schovat  {visibility:hidden; height: 0px !important;}
      .zobrazit {visibility:none;}     
    </style>
    <script type="text/javascript">
      function prohodit(element, prvniTrida, druhaTrida) {
      	element.className = (element.className == prvniTrida) ? druhaTrida : prvniTrida;
      }
    </script>
  </head>
<body style='width: 80%; margin: 0 auto;'>
<h1>Generování grafů a přehledů k počasí</h1>
<p>Desetinná čísla piš s tečkami namísto desetinných čárek!
<small><a href='index.php'>Hlavní stránka</a></small></p>
<!--
<p>
  Pomocí tohoto programu získáte v podobě obrázku dva grafy a základní přehledy o počasí 
  (určí se ze zadaných hodnot). Autorem programu je Luboš Hubáček a při používání je zakázáno 
  odstraňovat informace o autorovi z výstupu.
</p>-->
<?php
// výběr jaká data budeme zadávat
if (!isset($_GET["r"])) {
  $mesice = array("Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec");
  $rokStart = 2010;
  $rokKonec = date("Y");
      
  echo "<ul type='square'>\n";
    for ($i = $rokStart; $i <= $rokKonec; $i++) {
      echo "<li class='bold' onclick=\"prohodit(e" . $i . ", 'schovat', 'zobrazit');\">Rok " . $i . "</li>\n";
      echo "<ul id='e" . $i . "' class='schovat' style='margin-left: 20px;'>\n";
      for ($j = 0; $j < 12; $j++) {
        $kdy = $mesice[$j] . " " . $i;
        $kdy2 = $i . "&m=" . ($j+1);
        $kdy3 = $i . "_" . ($j+1);
        $kdy = (describe("pocasi_" . $kdy3)) ? $kdy. " (hotovo)" : "<a href='pridavej.php?r=" . $kdy2 . "'>" . $kdy . "</a>";
        echo "<li>" . $kdy . "</li>\n";
      }
      echo "</ul>\n";
    }
    echo "</ul>\n";
}


if (isset($_GET["r"]) && isset($_GET["m"])) {
  $month = intVal($_GET["m"]);
  $year = intVal($_GET["r"]);
  
  if ($month < 1 || $month > 12) {
    $error = "Měsíc je nastaven na neplatnou hodnotu (platné hodnoty 1-12)!";
  }
  else {
    if ($year <= 2000 || $year > Date("Y")) {
      $error = "Rok je nastaven na neplatnou hodnotu (platné hodnoty 2008 - " . Date("Y") . "!";
    }
    else {  
      echo "<form method='post' action='pridavej.php?r=" . $year . "&m=" . $month . "'>\n";
      echo "<table border='1'>\n";
      echo "<tr>\n";
      echo "<th>Datum</td>\n";
      echo "<th>Teplota 8h [°C]</th>\n";
      echo "<th>Teplota 15h [°C]</th>\n";
      echo "<th>Vlhkost 8h [%]</th>\n";
      echo "<th>Vlhkost 15h [%]</th>\n";
      echo "<th>Srážky [mm]</th>\n";
      echo "<th>Tlak [hPa]</th>\n";
      echo "</tr>\n";
      
      $slovy = array ( 1 => "Ledna", "Února", "Března", "Dubna", "Května", "Června", "Července", "Srpna", "Září", "Října", "Listopadu", "Prosince");
      $dnu = pocetDnuMesice ($year, $month);
      for ($i = 1; $i <= $dnu; $i++)
      {
        echo "<tr>\n";
        echo "<td>".$i.".".$slovy[$month]."</td>";
        echo "<td><input type='text' size='5' name='teplota_r_".$i."' /></td>\n";
        echo "<td><input type='text' size='5' name='teplota_o_".$i."' /></td>\n";
        echo "<td><input type='text' size='5' name='vlhkost_r_".$i."' /></td>\n";
        echo "<td><input type='text' size='5' name='vlhkost_o_".$i."' /></td>\n";
        echo "<td><input type='text' size='5' value='0' name='srazky_".$i."' /></td>\n";
        echo "<td><input type='text' size='5' name='tlak_".$i."' /></td>\n";
        echo "</tr>\n";
      }
      echo "<tr style='text-align: center;'><td colspan='7'>Heslo:<input type='password' name='pass' /><input type='hidden' name='datum' value='" . $year . "_" . $month . "' /><input type='submit' name='submit_2' value='Zpracovat data'></td></tr>\n";
      echo "</form>\n";
    }
  }
}

// přidání dat do databáze
if (isset($_POST["submit_2"])) {
  $pass = $_POST['pass'];
  if ($pass != "---------------") {
    $err = "Kontrolní heslo je špatně!";
  }
  else {
    $datum = mres($_POST['datum']);
  
    $create = mysql_query("
      CREATE TABLE IF NOT EXISTS `pocasi_" . $datum . "` (
        `den` int(11) NOT NULL,
        `t_rano` decimal(3,1) NOT NULL,
        `t_odpoledne` decimal(3,1) NOT NULL,
        `v_rano` int(11) NOT NULL,
        `v_odpoledne` int(11) NOT NULL,
        `srazky` decimal(3,1) NOT NULL,
        `tlak` int(11) NOT NULL,
        PRIMARY KEY (`den`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    "); 
    
    if ($create == FALSE) {
      $err = "Něco se pokazilo!";
    }
    else {  
      list($rok, $mesic) = exPlode("_", $datum);
      $dnu = pocetDnuMesice($rok, $mesic);
      for ($i = 1; $i <= $dnu; $i++) {
        $tr = str_replace(",", ".", mres(err_v($_POST['teplota_r_' . $i])));
        $to = str_replace(",", ".", mres(err_v($_POST['teplota_o_' . $i])));
        $vr = str_replace(",", ".", mres(err_v($_POST['vlhkost_r_' . $i])));
        $vo = str_replace(",", ".", mres(err_v($_POST['vlhkost_o_' . $i])));
        $sr = str_replace(",", ".", mres(err_v($_POST['srazky_' . $i])));
        $tl = str_replace(",", ".", mres(err_v($_POST['tlak_' . $i])));
        
        $sql = "
          INSERT INTO `pocasi_" . $datum . "` 
          (`den`, `t_rano`, `t_odpoledne`, `v_rano`, `v_odpoledne`, `srazky`, `tlak`) 
          VALUES (
            " . $i . ",
            " . $tr . ",
            " . $to . ",
            " . $vr . ",
            " . $vo . ",
            " . $sr . ",
            " . $tl . "
          )
        ";
        mysql_query($sql);
        echo "$sql<br>";
      }
    
      //echo "<pre>";
      //print_r($_POST);
      //echo "</pre>";
      
      if ($create) {
        $err = "Hodnoty úspěšně vloženy ;)";
        echo "<a href='pridavej.php'>Zpět k zadávání dat</a>\n";
      }
    }
  }
}
  # hlášení
  if (isSet($err))
    echo $err;
?>
</body>
</html>
