<?php
    // Zuerst eine Verbindung zur Datenbank aufbauen!
    $db_host 		    = 'localhost';
    $db_name 		    = 'foxhost_db2';
    $db_user 		    = 'foxhost_db2';
    $db_password 	    = 'y$5LhvaLb&PB';
    $db 			    = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
?>