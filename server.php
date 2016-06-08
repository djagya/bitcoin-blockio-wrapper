<?php
require_once 'vendor/autoload.php';
require_once 'Controller.php';
require_once 'Address.php';
require_once 'Wallet.php';

header('Access-Control-Allow-Origin: *');

if (!$_GET['action']) {
    throw new HttpException('Not found', 404);
}

if ($_GET['action'] === 'balance') {
    if (empty($_GET['label'])) {
        throw new HttpException('Label missing', 400);
    }

    $controller = new Controller('fede-5699-cf4c-59f9', '8K736MA8Y5N');

    $wallet = $controller->getUserWallet($_GET['label']);

    header('Content-Type: application/json');

    echo json_encode([
        'balance' => $wallet->availableBalance,
        'pending' => $wallet->pendingReceivedBalance,
    ]);
}
