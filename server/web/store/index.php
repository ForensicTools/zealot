<?php
ini_set('display_errors', 'on');
$pathInfo = pathinfo($_FILES['userFile']['name']);
$fileName = time() . ".zip"; 

$target = "/var/zealot/" . $fileName;
move_uploaded_file($_FILES['userFile']['tmp_name'], $target);

echo "UPLOAD_OK";
?>
