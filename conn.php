<?php
$conn = mysqli_connect("localhost", "root", "", "seid_ac_lts");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
