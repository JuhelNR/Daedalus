<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_SESSION['uid'])) {
    header('location: index.php');
}

?>
