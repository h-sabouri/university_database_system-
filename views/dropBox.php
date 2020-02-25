<?php
$tables = getAllTables($conn);
?>

<form action="" method="get">
       <fieldset>
          <legend>Select a table</legend>
          <p>
            <label>Table:</label>
            <select id = "table_name" name='table_name'>
               <?php
                  foreach ($tables as $table) {
                     $selected = ($tableName == $table) ? 'selected' : '';
                     echo "<option value = \"{$table}\" $selected>{$table}</option>";
                  }
               ?>
            </select>
            <input type="submit" value="GO">
          </p>
       </fieldset>
</form>
