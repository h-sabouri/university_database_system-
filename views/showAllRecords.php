<?php
if (!empty($_POST) && isset($_POST['__delete'])) {
	deleteFromTable($conn, $tableName, $_POST['pk_name'], $_POST['pk_value']);
}

if (!empty($_POST) && isset($_POST['__update'])) {
	$postData = $_POST;
	unset($postData['__update']);
	unset($postData['pk_name']);
	unset($postData['pk_value']);
	
	if ($tableName == 'StudentRegisterSection') {
		$isAllowed = isStudentCompletedPrereq($conn, $postData['courseId'], $postData['pId']);
		if (!$isAllowed) {
			echo 'Student does not statisfy prerequisite conditions';
			return;
		} 
	}

	echo updateTable($conn, $tableName, $postData, $_POST['pk_name'], $_POST['pk_value']);
}

$postData = $_POST;
//process insert by getting the input from $POST
if (!empty($_POST) && isset($_POST['__insert'])) {
	unset($postData['__insert']);
	if ($tableName == 'StudentRegisterSection') {
		$isAllowed = isStudentCompletedPrereq($conn, $postData['courseId'], $postData['pId']);
		if (!$isAllowed) {
			echo 'Student does not statisfy prerequisite conditions';
			return;
		}
	}
	echo insertIntoTable($conn, $tableName, $columns, $postData);
}

$result = getAllRecords($conn, $tableName);
// output data of each row
echo "<table>";
echo '<tr>';
foreach ($columns as $column) {
	echo "<th>$column</th>";
}
echo "<th></th>";
echo "<th></th>";
echo '</tr>';

if ($result->num_rows > 0) {
	
    while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<form action="" method="post">';
		foreach ($row as $k => $v) {
			echo "<td>";
			echo "<input type=\"text\" class=\"{$tableName}_{$k}\" name=\"{$k}\" value=\"$v\">";
			echo "</td>";
		}
		echo '<input type="hidden" name="__update" value="">';
		echo '<input type="hidden" name="pk_name" value="'. $pk .'">';
		echo '<input type="hidden" name="pk_value" value="'. $row[$pk] .'">';
		echo "<td><input type=\"submit\" value=\"Update\"></td>";
		echo '</form>';

		echo '<form action="" method="post">';
		echo '<input type="hidden" name="__delete" value="">';
		echo '<input type="hidden" name="pk_name" value="'. $pk .'">';
		echo '<input type="hidden" name="pk_value" value="'. $row[$pk] .'">';
		echo "<td><input type=\"submit\" value=\"Delete\"></td>";
		echo '</form>';
		echo '</tr>';
    }

} else {
	echo "0 results, create new record:<br />";	
}
echo '<tr>';
echo '<form action="" method="post">';
echo '<input type="hidden" name="__insert" value=""><br>';
foreach ($columns as $column) {
	//echo $column;
	echo "<td>";
	echo '<input type="text" class="' . "{$tableName}_{$column}" . '" name="'. $column. '" value="">';
	echo "</td>";
}
echo '<td> <input type="submit" value="Insert"></td></form>';
echo '</tr>';
echo "</table>";
