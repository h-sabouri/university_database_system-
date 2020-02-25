<?php
include 'db.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$count = 0;
$fn = fopen("theBigInserter.txt","r");
  
while(! feof($fn))  {
	$result = fgets($fn);
	echo $result;
	$r = $conn->query($result);
	
	if (!$r) {
		echo ($conn->error);
	}
}

fclose($fn);
