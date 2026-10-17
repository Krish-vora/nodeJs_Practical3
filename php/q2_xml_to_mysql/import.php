
<?php
include "db.php";

$xml = simplexml_load_file("students.xml");

if ($xml === false) {
    die("XML file not found or invalid");
}

foreach ($xml->student as $s) {
    $id = (int)$s->id;
    $name = mysqli_real_escape_string($conn, (string)$s->name);
    $course = mysqli_real_escape_string($conn, (string)$s->course);

    $sql = "INSERT INTO students (id, name, course)
            VALUES ($id, '$name', '$course')";

    if (mysqli_query($conn, $sql)) {
        echo "Student inserted: " .
             htmlspecialchars($name) . "<br>";
    } else {
        echo "Insert failed for ID $id: " .
             mysqli_error($conn) . "<br>";
    }
}

mysqli_close($conn);
?>
