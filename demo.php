<?php
session_start();

// Function to add a new record to the session
function addRecord($name, $surname) {
    $record = array('name' => $name, 'surname' => $surname);
    $_SESSION['records'][] = $record;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and get the input values
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $surname = isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : '';

    // Add the record to the session
    addRecord($name, $surname);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Management</title>
</head>
<body>

<h2>Add Record</h2>
<form method="post">
    <label for="name">Name:</label>
    <input type="text" name="name" required>
    <br>
    <label for="surname">Surname:</label>
    <input type="text" name="surname" required>
    <br>
    <button type="submit">Save</button>
</form>

<h2>Record Table</h2>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Surname</th>
    </tr>
    <?php
    // Display records in the table
    if (isset($_SESSION['records'])) {
        foreach ($_SESSION['records'] as $record) {
            echo '<tr>';
            echo '<td>' . $record['name'] . '</td>';
            echo '<td>' . $record['surname'] . '</td>';
            echo '</tr>';
        }
    }
    ?>
</table>

</body>
</html>
