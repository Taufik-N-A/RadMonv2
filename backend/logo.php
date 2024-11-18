<?php
/*
*******************************************************************************************************************
* Warning!!!, Tidak untuk diperjual belikan!, Cukup pakai sendiri atau share kepada orang lain secara gratis
*******************************************************************************************************************
* Author : @Maizil https://t.me/maizil41
*******************************************************************************************************************
* © 2024 Mutiara-Net By @Maizil
*******************************************************************************************************************
*/
$targetDir = "../img/logo/";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES["UploadLogo"])) {
    $newFileName = "radmon-logo.png";
    $targetFilePath = $targetDir . $newFileName;
    $fileType = pathinfo($_FILES["UploadLogo"]["name"], PATHINFO_EXTENSION);
    
    $allowTypes = array('jpg', 'png', 'jpeg');
    if (in_array($fileType, $allowTypes)) {
        if (file_exists($targetFilePath)) {
            unlink($targetFilePath);
        }

        if (move_uploaded_file($_FILES["UploadLogo"]["tmp_name"], $targetFilePath)) {
            $message = urlencode("✅ Success");
            header('Location: ../hotspot/hslogo.php?message=' . $message);
            exit;
        }
    }
    $message = urlencode("❌ Failed.");
    header('Location: ../hotspot/hslogo.php?message=' . $message);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['file'])) {
    $file = $targetDir . $_GET['file'];
    if (file_exists($file)) {
        unlink($file);
    }
    $message = urlencode("✅ Success");
    header('Location: ../hotspot/hslogo.php?message=' . $message);
    exit;
}
?>
