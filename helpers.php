<?php

function getConnection($servername, $username, $password, $dbname)
{

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function getAllTables($conn)
{
    $sql = "SHOW TABLES";
    $res = $conn->query($sql);

    $r = [];
    while ($row = $res->fetch_array()) {
        $r[] = $row[0];
    }
    return $r;
}

function getColumns($conn, $tableName)
{
    $sql = "SHOW COLUMNS FROM $tableName";
    $res = $conn->query($sql);

    $columns = [];
    while ($row = $res->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    return $columns;
}

function insertIntoTable($conn, $tableName, $columns, $postData)
{
    $implodedFieldsName = implode(",", $columns);
    $implodedValues = "'" . implode("','", $postData) . "'";
    $sql_Insert = "INSERT INTO $tableName ($implodedFieldsName)
		VALUES ($implodedValues)";

    if ($conn->query($sql_Insert) === TRUE) {
        return "New record created successfully";
    } else {
        return "Error: " . $sql_Insert . "<br>" . $conn->error;
    }
}

function getAllRecords($conn, $tableName)
{
    $sql_Display = "SELECT * FROM $tableName";
    return $conn->query($sql_Display);
}

function deleteFromTable($conn, $tableName, $pkName, $id)
{
    $sql_Delete = "DELETE FROM " . $tableName . " WHERE " . $pkName . " = " . $id;
    if ($conn->query($sql_Delete) === TRUE) {
        return "Record was deleted successfully.";
    } else {
        return "Error: " . $sql_Delete . $conn->error;
    }
}

function getPrimaryKeyName($conn, $tableName)
{
    $sql = "show keys from $tableName where key_name = 'PRIMARY'";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        return $row['Column_name'];
    }

    return '';
}

function updateTable($conn, $tableName, $postData, $pkName, $pkValue)
{
    $sql_Update = "UPDATE $tableName SET ";

    $setPart = '';
    foreach ($postData as $k => $v) {
        $setPart = $setPart . $k . ' = "' . $v . '", ';
    }

    $setPart = rtrim($setPart, ', ');

    $wherPart = " WHERE $pkName = $pkValue";

    $sql_Update = $sql_Update . $setPart . $wherPart;

    if ($conn->query($sql_Update) === TRUE) {
        return "Record was updated successfully.";
    } else {
        return "Error: " . $sql_Update . $conn->error;
    }
}

function isStudentCompletedPrereq($conn, $courseId, $studentId) 
{
    $passingGradeTag = "D-";
    $sql = "SELECT score FROM ScoreDescription WHERE scoreTag = '{$passingGradeTag}'";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $passedGrade = $row['score'];
    }

    $sql = "
        SELECT prereqId FROM Prerequisite
        WHERE  courseId = {$courseId}
    ";

    $res = $conn->query($sql);

    $prerequisites = [];
    while ($row = $res->fetch_assoc()) {
        $prerequisites[] = $row['prereqId'];
    }

    foreach ($prerequisites as $prerequisite) {
        $sql = "
            SELECT score FROM StudentRegisterSection
            WHERE courseId = {$prerequisite}
            AND pId = {$studentId}
        ";

        $res = $conn->query($sql);
        
        if ($res->num_rows == 0) {
            return false;
        }

        while ($row = $res->fetch_assoc()) {
            
            $r = $row['score'];
            
            if ($r < $passedGrade) {
                return false;
            }
        }
    }

    return true;
}


function isStudentEligibleRf($conn, $studentId) 
{
	// Retrieve supervisorId
	/* Student(pId, gpa, programId, level, credits, advisorId, supervisorId, base) */
	$sql = "SELECT supervisorId, gpa FROM Student WHERE studentId = '{$studentId}'";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $supervisorId = $row['supervisorId'];
        $gpa = $row['gpa'];
    }
	
	// Retrieve if this professor is registered for an available ResearchFund
	//  without a student occupying it
	/* ResearchFund(rfId, pId_i, pId_s, rfDescription, rfAmount, rfTerm) */
	$sql = "SELECT pId_i FROM ResearchFund WHERE pId_i = '{$supervisorId}' AND pId_s=NULL";
    $res = $conn->query($sql);
	
	// Returns restriction result
	return !($res->num_rows == 0 || $gpa<3.0);
}

function isStudentEligibleTa($conn, $pId, $courseId, $hourToAdd)
{
	// Retrieve gpa
	/* Student(pId, gpa, programId, level, credits, advisorId, supervisorId, base) */
	$sql = "SELECT level, gpa FROM Student WHERE pId = '{$pId}' AND level='Graduate'";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $gpa = $row['gpa'];
        $level = $row['level'];
    }
	
	if ($gpa<3.2 || $level!="Graduate"){
		return false;
	}
	else {
		// Retrieve workHours
		/* TaActivity(pId, courseId, sectionId, activity, workHours, taTerm) */
		$sql = "SELECT COUNT(DISTINCT courseId) AS numActivities, SUM(workHours) AS sumWorkHours FROM TaActivity WHERE pId = '{$pId}'";
		$res = $conn->query($sql);
		while ($row = $res->fetch_assoc()) {
			$sumWorkHours = $row['sumWorkHours'];
			$numActivities = $row['numActivities'];
		}
		return($sumWorkHours + $hourToAdd <= 260 and numActivities == 1);
	}
}

// Checks if a certain period overlaps with a with a timeSlot
function isTimeConflict($conn, $timeSlotId, $givenStart, $givenEnd)
{
	// Walk through timeSlots
	/* TimeSlot(timeSlotId, begin, end) */
	$sql = "SELECT begin,end FROM TimeSlot WHERE timeSlotId = '{$timeSlotId}'";
	$res = $conn->query($sql);
	while ($row = $res->fetch_assoc()) {
		$begin = $row['begin'];
		$end = $row['end'];
	}
	return (($begin <= $givenEnd) and ($givenStart <= $end));
}