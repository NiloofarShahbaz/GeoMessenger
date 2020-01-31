<?php
$con = mysqli_connect("localhost","admin","Admin123!","messenger");
if (!$con) {
    die('ERROR: Could not connect: ' . mysqli_connect_error());
}