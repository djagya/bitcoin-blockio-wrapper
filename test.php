<?php
require_once 'vendor/autoload.php';

$apiKey = "fede-5699-cf4c-59f9";
$pin = "8K736MA8Y5N";
$version = 2; // the API version to use

$block_io = new BlockIo($apiKey, $pin, $version);

$balance = $block_io->get_balance();
var_dump($balance);
echo "Confirmed Balance: " . $balance->data->available_balance . "\n";

// todo: request pass every 5 minutes on the transactions page OR transaction

// todo: archive addresses on user delete
// send fees to the 'default' address OR to another wallet (better)

// FIXME: just info.
// Wallet - set of addresses with the same part of the label for user.
// Allow max 3-4 addresses for user, where one will be a public.
// Constant minimal transaction fee - 0.0001 BTC =  5 cents
// Minimal value to send - 0.00002 BTC
// + 1% fee

// TODO:
// get_new_address - for new addresses. label must be unique
if (false) {
    $address = $block_io->get_new_address(['label' => 'user_id_1_addr1']);
    $address2 = $block_io->get_new_address(['label' => 'user_id_1_addr2']);
}
$address3 = $block_io->get_new_address();
var_dump($address3);


//$balance = $block_io->get_balance(array('label' => 'default'));
//echo $balance->data->available_balance . "\n";

// Labels:
//      $withdraw = $block_io->withdraw(array('amount' => '50.0', 'to_address' => 'WALLET-ADDRESS-HERE'));


// TODO:
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
