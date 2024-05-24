<?php
$conn = new PDO('sqlite:/var/www/html/galactic.db');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
