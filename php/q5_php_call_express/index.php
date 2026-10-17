
<?php
$url = "http://localhost:3000/students";

$response = file_get_contents($url);

if ($response === false) {
    die("API request failed");
}

$students = json_decode($response, true);

echo "<h2>Student List</h2>";

foreach ($students as $student) {
    echo "ID: " . $student['id'] . "<br>";
    echo "Name: " . htmlspecialchars($student['name']) . "<br>";
    echo "Course: " . htmlspecialchars($student['course']) . "<br><br>";
}
?>
