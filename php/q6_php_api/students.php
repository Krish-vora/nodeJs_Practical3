
<?php
header("Content-Type: application/json");

$students = [
    [
        "id" => 1,
        "name" => "Rahul",
        "course" => "MSc ICT"
    ],
    [
        "id" => 2,
        "name" => "Amit",
        "course" => "BCA"
    ]
];

echo json_encode($students);
?>
