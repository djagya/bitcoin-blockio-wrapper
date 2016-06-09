<?php
require_once 'vendor/autoload.php';

$apiKey = "5867-ba4c-0d7e-b0a4";
$pin = "3498g9ijij";
$version = 2; // the API version to use

$block_io = new BlockIo($apiKey, $pin, $version);
//
//$block_io->get_new_address(['label' => 'test-user-1']);
//$block_io->get_new_address(['label' => 'test-user-2']);
//var_dump($block_io->get_new_address(['label' => 'fees'])->data);

$block_io->withdraw_from_labels([
    'amounts' => '0.009',
    'from_labels' => 'default',
    'to_labels' => 'test-user-1',
]);

//$balance = $block_io->get_balance();
//var_dump($balance);
//echo "Confirmed Balance: " . $balance->data->available_balance . "\n";

// todo: request pass every 5 minutes on the transactions page OR transaction
// send fees to the 'default' address OR to another wallet (better)

// form with input for value and target address
// below - text changed to include transaction fee and our fee, e.g.:
// asdkdjgksdfgdsf, 0.5 - user inputs
// will be sent 0.6 - 0.5 + 0.0001 + 1%



// Labels:
//      $withdraw = $block_io->withdraw(array('amount' => '50.0', 'to_address' => 'WALLET-ADDRESS-HERE'));


// TODO: instant operations: if target address is in our system (check with DB) - mark operation as instant:
//Our Green Addresses () allow our users to transact coins instantly with enabled sites. More specifically, we ensure coins cannot be double spent, so our users can use coins without network confirmations. This removes the Blockchain confirmation delay (10min for Bitcoin, 1min for Dogecoin, etc.), while maintaining integrity of the transactions*.
//
//You can speed up transactions for your sites using this same method. For example, if a user sends you coins for merchandize, you can check if the sending address(es) are Block.io Green Addresses, and elect to give them the merchandize without waiting for confirmations. This drastically improves user experience on your site.
//
//To check if a transaction or sending address is guaranteed to be honest by Block.io, use our is_green_address and is_green_transaction API calls.
