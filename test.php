<?php
require_once 'vendor/autoload.php';

$apiKey = "fede-5699-cf4c-59f9";
$pin = "8K736MA8Y5N";
$version = 2; // the API version to use

$block_io = new BlockIo($apiKey, $pin, $version);

echo "Confirmed Balance: " . $block_io->get_balance()->data->available_balance . "\n";


//$balance = $block_io->get_balance(array('label' => 'default'));
//echo $balance->data->available_balance . "\n";

// Labels:
//      $withdraw = $block_io->withdraw(array('amount' => '50.0', 'to_address' => 'WALLET-ADDRESS-HERE'));


//With Block.io, you can create wallet addresses for users inside your games, auction sites, stores, etc.
//
//To create a user's wallet on your account, create addresses for them using get_new_address. You should specify a sequence of labels for that user. For instance, if we wish to create a number of addresses for User A, we'd want to call get_new_address with label=userAx{address_number} as many times as wish to create a new address for User A.
//
//Once we have addresses for User A, we can query balances for their addresses, and send coins on the user's behalf.



// TODO: instant operations: if target address is in our system (check with DB) - mark operation as instant:
//Our Green Addresses () allow our users to transact coins instantly with enabled sites. More specifically, we ensure coins cannot be double spent, so our users can use coins without network confirmations. This removes the Blockchain confirmation delay (10min for Bitcoin, 1min for Dogecoin, etc.), while maintaining integrity of the transactions*.
//
//You can speed up transactions for your sites using this same method. For example, if a user sends you coins for merchandize, you can check if the sending address(es) are Block.io Green Addresses, and elect to give them the merchandize without waiting for confirmations. This drastically improves user experience on your site.
//
//To check if a transaction or sending address is guaranteed to be honest by Block.io, use our is_green_address and is_green_transaction API calls.
