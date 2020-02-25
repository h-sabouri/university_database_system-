<?php

//Process deletion by getting the input from $POST
// The primary key and record number for deletion are asked 
echo '<form action="" method="post">';
echo 'Primary_Key: <input type="text" name="name1"><br>';
echo 'Record_Number_To_Delete: <input type="text" name="name2"><br>';
echo '<input type="submit"></form>';
echo '<br />';
if (!empty($_POST) && !empty($_POST['name1']) && !empty($_POST['name2'])) {
	echo deleteFromTable($conn, $tableName, $_POST['name1'], $_POST['name2']);
}
