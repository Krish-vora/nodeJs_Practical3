
<?php
$folder = "myfiles";

if (is_dir($folder)) {
    $files = scandir($folder);

    echo "<h2>Files in Directory</h2>";

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo $file . "<br>";
        }
    }
} else {
    echo "Directory not found";
}
?>
