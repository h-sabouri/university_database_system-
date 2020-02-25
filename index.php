<!DOCTYPE html>
<html>
<head>
<title>qqc5531 - main project</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body>
<script>
    $(document).ready(function(){
        console.log('jquery loaded');
        $('.Student_level').on('input', function() {
            var studentLevelElm = $(this);
            var studentCreditsElm = studentLevelElm.closest('td').next('td').find('.Student_credits');
        
            var studentLevel = studentLevelElm.val().trim().toLowerCase();
            var studentCredits = studentCreditsElm.val();
            
            if (!studentCredits) {
                if (studentLevel === 'undergraduate') {
                    studentCreditsElm.val(90);
                } else if (studentLevel === 'graduate') {
                    studentCreditsElm.val(44);
                }
            }
        });
    });
</script>

<?php
include 'db.php';
require 'helpers.php';

$conn = getConnection($servername, $username, $password, $dbname);
// genrate the input form:
$tableName = isset($_GET['table_name']) ? $_GET['table_name'] : '';

if ($tableName) {
    $pk = getPrimaryKeyName($conn, $tableName);
}

include __DIR__ . '/views/dropBox.php';

if (empty($tableName)) {
    return;
}

$columns = getColumns($conn, $tableName);

include __DIR__ . '/views/showAllRecords.php';

?>
</body>
</html>
