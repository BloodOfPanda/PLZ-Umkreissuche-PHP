<!doctype html>

<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>PHP Umkreissuche</title>
  <meta name="description" content="Eine simple Umkreissuche mit PHP">
  <meta name="author" content="MArcel // FoxHost.de">

  <meta property="og:title" content="PHP Umkreissuche">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://demo.foxhost.it/">
  <meta property="og:description" content="Eine simple Umkreissuche mit PHP">
</head>

<body>
  <div style="width: 650px; text-align: center;">
    <h2>Eine simple Umkreissuche mit PHP</h2>

    <form method="post">
      <table style="border-collapse: collapse; width: 100%;" border="0">
        <tbody>
        <tr>
        <td style="width: 50%;">
          <label for="plz">PLZ:</label><br>
          <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="5" type="text" id="plz" name="plz" placeholder="03226" value="<?php echo htmlspecialchars($_POST["plz"], ENT_QUOTES); ?>" required><br>
        </td>
        <td style="width: 50%;">
          <label for="entfernung">Entfernung in KM</label><br>
          <input oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" type="text" id="entfernung" name="entfernung" placeholder="20" value="<?php echo htmlspecialchars($_POST["entfernung"], ENT_QUOTES);?>" required>
        </td>
        </tr>
        <tr>
          <td style="width: 50%;" colspan="2"><input name="suche" type="submit" value="Submit"></td>
        </tr>
        </tbody>
      </table>
    </form> 

  </div>
  <br><br>
  <?php
    //Konfiguration zur Datenbank
    require './inc/config.php';

    // PLZ nach der gesucht wird
    $plz = htmlspecialchars($_POST["plz"], ENT_QUOTES);

    // der Umkreis in Km
    $umkreis = htmlspecialchars($_POST["entfernung"], ENT_QUOTES);

    if( isset($_POST['suche']) )
      {
        $statement = $db->prepare("SELECT COUNT(*) AS plz_de FROM plz_de WHERE plz = '".$plz."'");
        $statement->execute();  
        $anzahl = $statement->fetch();

        if ($anzahl['plz_de'] == "0") {
          echo "".$_POST["plz"]." ist eine ungültige PLZ!";
          exit;
        }

        // Erdradius (geozentrischer Mittelwert) in KM
        $radius = 6368;

        $statement = $db->prepare("SELECT * FROM plz_de WHERE plz = '".$plz."'");
        $result = $statement->execute();
        while($row = $statement->fetch()) {
          $erg_lon = $row['lon'];
          $erg_lat = $row['lat'];
        }

        // Umrechnung von GRAD IN RAD
        $lon = $erg_lon / 180 * pi();
        $lat = $erg_lat / 180 * pi();

        // jetzt erfolgt die eigentliche Abfrage

          $statement = $db->prepare("SELECT *, (".$radius." * SQRT(2*(1-cos(RADIANS(lat)) * cos(".$lat.") * (sin(RADIANS(lon)) * sin(".$lon.") + cos(RADIANS(lon)) * cos(".$lon.")) - sin(RADIANS(lat)) * sin(".$lat.")))) AS Distance FROM plz_de WHERE ".$radius." * SQRT(2*(1-cos(RADIANS(lat)) * cos(".$lat.") * (sin(RADIANS(lon)) * sin(".$lon.") + cos(RADIANS(lon)) * cos(".$lon.")) - sin(RADIANS(lat)) * sin(".$lat."))) <= ".$umkreis." ORDER BY Distance ");

        function distance($lat1, $lon1, $lat2, $lon2, $unit) {

          if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
          }
          else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
              return ($miles * 1.609344);
            } else if ($unit == "N") {
              return ($miles * 0.8684);
            } else {
              return $miles;
            }
          }
        }

        // Ausgabe
          $result = $statement->execute();
          while($row = $statement->fetch()) {
            echo $row['plz']. " - ". $row['ort']. " ".number_format(distance($erg_lat, $erg_lon, $row['lat'], $row['lon'], "K"),1,",","."). " km ". $row['lon']. " - ". $row['lat']. " <br>";
          }
      }
  ?>
</body>
</html>