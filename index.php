<?php
require_once 'vendor/autoload.php';
require_once 'Controller.php';

header('Access-Control-Allow-Origin: *');

if (empty($_GET['action']) || !in_array($_GET['action'], ['balance', 'send'])) {
    throw new Exception('Not found', 404);
}

if (empty($_GET['label']) || !in_array($_GET['label'], ['test-user-1', 'test-user-2'])) {
    throw new Exception('Bad request', 400);
}
$label = $_GET['label'];

$controller = new \Controller('', '');

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
