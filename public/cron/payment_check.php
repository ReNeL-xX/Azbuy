<?php
// public/cron/payment_check.php
session_start();
require_once '../../config/Database.php';
require_once '../../app/models/Auction.php';

$database = new Database();
$conn = $database->connect();
$auctionModel = new Auction($conn);
$auctionModel->checkExpiredPayments();
$conn->close();

echo "Payment checks completed at " . date('Y-m-d H:i:s');