<?php

/**
 * @author danil danil.kabluk@gmail.com
 */
class Address
{
    /** @var string */
    public $label;
    /** @var string */
    public $address;
    /** @var float */
    public $availableBalance = 0;
    /** @var float */
    public $pendingReceivedBalance = 0;

    /**
     * @param stdClass $rawData Response from block.io
     * @return Address
     */
    public static function instantiate($rawData)
    {
        $address = new self;
        $address->label = $rawData->label;
        $address->address = $rawData->address;
        $address->availableBalance = (float)$rawData->available_balance;
        $address->pendingReceivedBalance = (float)$rawData->pending_received_balance;

        return $address;
    }
}
