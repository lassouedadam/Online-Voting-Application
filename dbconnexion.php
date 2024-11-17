<?php
// Database configuration
$db_host = 'localhost';     
$db_user = 'root';           
$db_pass = 'Xhh4azsese_';   
$db_name = 'election_v2';  

// Database connection
$db = new mysqli($db_host, $db_user, $db_pass, $db_name,3307);

// Check for connection errors
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Set the character set to utf8
$db->set_charset("utf8");
?>