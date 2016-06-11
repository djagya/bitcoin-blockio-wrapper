<?php
/**
 * Wallet class.
 *
 * @copyright Copyright (c) 2016 Danil Zakablukovskii
 * @package djagya/bitcoin
 * @author Danil Zakablukovskii <danil.kabluk@gmail.com>
 */

namespace djagya\bitcoin;

/**
 * @author danil danil.kabluk@gmail.com
 */
class Wallet
{
    /** @var float */
    public $availableBalance = 0;
    /** @var float */
    public $pendingReceivedBalance = 0;
    /** @var Address[] */
    public $addresses = [];

    /**
     * @param \stdClass $rawData Response from block.io
     * @return Wallet
     */
    public static function instantiate(\stdClass $rawData)
    {
        $wallet = new self;
        $wallet->availableBalance = (float)$rawData->available_balance;
        $wallet->pendingReceivedBalance = (float)$rawData->pending_received_balance;

        foreach ($rawData->balances as $balance) {
            $wallet->addresses[] = Address::instantiate($balance);
        }

        return $wallet;
    }
}
