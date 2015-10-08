<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Statistiky počasí - Kamenný Újezd u Rokycan</title>
    <style type='text/css'>
      * {
        margin: 0px;
        padding: 0px;
      }
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
      .schovat  {visibility:hidden; height: 0px !important;}
      .zobrazit {visibility:none;}     
    </style>
    <script type="text/javascript">
      function prohodit(element, prvniTrida, druhaTrida) {
      	element.className = (element.className == prvniTrida) ? druhaTrida : prvniTrida;
      }
    </script>
  </head>
  <body>
    <?php
      require_once "config.php";
      require_once "core.php";
    ?>
    <h1>Statistiky počasí - Kamenný Újezd u Rokycan</h1>
    <p>Grafy počasí lze zobrazit pouze k měsícům, ke kterým byly zadány data</p>
    <small>&copy;Luboš Hubáček 2013 - 2014; <a href='pridavej.php'>Přidávání dat</a></small>
    
    <?php
      $mesice = array("Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec");
      $rokStart = 2012;
      $rokKonec = date("Y");
      
      echo "<ul type='square'>\n";
      for ($i = $rokStart; $i <= $rokKonec; $i++) {
        echo "<li class='bold' onclick=\"prohodit(e" . $i . ", 'schovat', 'zobrazit');\">Rok " . $i . "</li>\n";
        echo "<ul id='e" . $i . "' class='schovat' style='margin-left: 20px;'>\n";
        for ($j = 0; $j < 12; $j++) {
          $kdy = $mesice[$j] . " " . $i;
          $kdy2 = $i . "_" . ($j+1);
          $kdy = (!describe("pocasi_" . $kdy2)) ? $kdy : "<a href='browse.php?d=" . $kdy2 . "'>" . $kdy . "</a>";
          echo "<li>" . $kdy . "</li>\n";
        }
        echo "<li><a href='rocni_souhrn.php?r=" . $i . "'>Roční přehled</a></li>\n";
        echo "</ul>\n";
      }
      echo "</ul>\n";
    ?>
  </body>
</html>
