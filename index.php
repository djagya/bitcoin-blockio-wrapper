<?php
require_once 'vendor/autoload.php';
require_once 'Controller.php';
require_once 'Address.php';
require_once 'Wallet.php';

header('Access-Control-Allow-Origin: *');

if (!$_GET['action']) {
    throw new HttpException('Not found', 404);
}

if (empty($_GET['label'])) {
    throw new HttpException('Label missing', 400);
}
$label = $_GET['label'];

$controller = new Controller('5867-ba4c-0d7e-b0a4', '3498g9ijij');

if ($_GET['action'] === 'balance') {
    $wallet = $controller->getUserWallet($_GET['label']);

    header('Content-Type: application/json');

    echo json_encode([
        'balance' => $wallet->availableBalance,
        'pending' => $wallet->pendingReceivedBalance,
    ]);
}

if ($_GET['action'] === 'send') {
    $toAddress = $_POST['address'];
    $amount = $_POST['amount'];

    $controller->send($label, $toAddress, $amount);
    header("location:javascript://history.go(-1)");
}