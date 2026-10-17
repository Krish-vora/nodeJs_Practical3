<?php
include "db.php";

$result = mysqli_query($conn, "SELECT * FROM students");

$xml = new SimpleXMLElement("<students/>");

while ($row = mysqli_fetch_assoc($result)) {
    $student = $xml->addChild("student");

    $student->addChild("id", $row['id']);
    $student->addChild("name", $row['name']);
    $student->addChild("course", $row['course']);
}

$xml->asXML("students.xml");

echo "Students exported to XML successfully!";
?>