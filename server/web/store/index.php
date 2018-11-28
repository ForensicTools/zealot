<?php
ini_set('display_errors', 'on');

$fileName = time() . ".zip"; 
$target = "/var/zealot/" . $fileName;
move_uploaded_file($_FILES['file']['tmp_name'], $target);

echo "UPLOAD_OK";
?>
