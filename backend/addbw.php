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
require_once '../config/mysqli_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    
    $rate_down = isset($_POST['rate_down']) ? floatval(str_replace(',', '.', $_POST['rate_down'])) : 0;
    $rate_down_unit = isset($_POST['rate_down_unit']) ? $_POST['rate_down_unit'] : 'Kbps';
    $rate_up = isset($_POST['rate_up']) ? floatval(str_replace(',', '.', $_POST['rate_up'])) : 0;
    $rate_up_unit = isset($_POST['rate_up_unit']) ? $_POST['rate_up_unit'] : 'Kbps';

    if ($rate_down_unit === 'Kbps') {
        $rate_down_bps = $rate_down * 1000;
    } elseif ($rate_down_unit === 'Mbps') {
        $rate_down_bps = $rate_down * 1048576;
    } else {
        $rate_down_bps = $rate_down;
    }

    if ($rate_up_unit === 'Kbps') {
        $rate_up_bps = $rate_up * 1000;
    } elseif ($rate_up_unit === 'Mbps') {
        $rate_up_bps = $rate_up * 1048576;
    } else {
        $rate_up_bps = $rate_up;
    }

    $creation_date = date('Y-m-d H:i:s');

    $sql = "SELECT COUNT(*) as count FROM bandwidth WHERE name = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $message = urlencode("❌ Bandwidth Name already exists.");
            header('Location: ../hotspot/addbw.php?message=' . $message);
            $conn->close();
            exit();
        }
    } else {
        echo "Error: " . $conn->error;
        $conn->close();
        exit();
    }

    $insert = "INSERT INTO bandwidth (name, rate_down, rate_up, creation_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    if ($stmt) {
        $stmt->bind_param("siis", $name, $rate_down_bps, $rate_up_bps, $creation_date);
        if ($stmt->execute()) {
            $message = urlencode("✅ Bandwidth successfully added.");
            header('Location: ../hotspot/addbw.php?message=' . $message);
        } else {
            $message = urlencode('❌ "Error: ' . $conn->error . '"');
            header('Location: ../hotspot/addbw.php?message=' . $message);
        }
        $stmt->close();
    } else {
        $message = urlencode('❌ "Error: ' . $conn->error . '"');
        header('Location: ../hotspot/addbw.php?message=' . $message);
    }

    $conn->close();
    exit();
}
?>