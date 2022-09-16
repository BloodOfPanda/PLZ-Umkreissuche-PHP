<?php
    // Zuerst eine Verbindung zur Datenbank aufbauen!
    $db_host 		    = 'localhost';
    $db_name 		    = 'foxhost_DB';
    $db_user 		    = 'foxhost_USER';
    $db_password 	    = 'PASSWORD';
    $db 			    = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
?>