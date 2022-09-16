<?php
  //Konfiguration zur Datenbank
  require './inc/config.php';

  // PLZ nach der gesucht wird
  $plz = $_GET["plz"];

  // der Umkreis in Km
  $umkreis = $_GET["entfernung"];

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

  // die Ausgabe (vereinfacht)
    $result = $statement->execute();
    while($row = $statement->fetch()) {
      echo $row['plz']. " - ". $row['ort']. " ".number_format(distance($erg_lat, $erg_lon, $row['lat'], $row['lon'], "K"),1,",","."). " km ". $row['lon']. " - ". $row['lat']. " <br>";
    }
?>