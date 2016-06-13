<?php
/**
 * Controller class.
 *
 * @copyright Copyright (c) 2016 Danil Zakablukovskii
 * @package djagya/bitcoin
 * @author Danil Zakablukovskii <danil.kabluk@gmail.com>
 */

namespace djagya\bitcoin;

class Transaction
{
    const TYPE_RECEIVED = 0;
    const TYPE_SENT = 1;

    public $id;
    public $time;
    public $type;
    public $amount;
    public $sender;
    public $receiver;

    /**
     * @param \stdClass $rawData Response from block.io
     * @return self
     */
    public static function instantiate(\stdClass $rawData)
    {
        $transaction = new self;
        $transaction->id = $rawData->txid;
        $transaction->time = $rawData->time;
        $transaction->type = isset($rawData->amounts_sent) ? self::TYPE_SENT : self::TYPE_RECEIVED;
        $transaction->sender = $rawData->senders[0];

        if ($transaction->type === self::TYPE_SENT) {
            $transaction->amount = $rawData->total_amount_sent;
            $transaction->receiver = $rawData->amounts_sent[0]->recipient;
        } else {
            $transaction->amount = $rawData->amounts_received[0]->amount;
            $transaction->receiver = $rawData->amounts_received[0]->recipient;
        }

        return $transaction;
    }
}
