<?php
session_start();
if (!isset($_SESSION['Usuario']) || !isset($_SESSION['verificado_2fa'])) {
    header("Location: login.php");
    exit();
}
?>
