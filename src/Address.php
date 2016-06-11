<?php
/**
 * Address class.
 *
 * @copyright Copyright (c) 2016 Danil Zakablukovskii
 * @package djagya/bitcoin
 * @author Danil Zakablukovskii <danil.kabluk@gmail.com>
 */

namespace djagya\bitcoin;

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
     * @param \stdClass $rawData Response from block.io
     * @return Address
     */
    public static function instantiate(\stdClass $rawData)
    {
        $address = new self;
        $address->label = $rawData->label;
        $address->address = $rawData->address;
        $address->availableBalance = isset($rawData->available_balance) ?
            (float)$rawData->available_balance : 0;
        $address->pendingReceivedBalance = isset($rawData->pending_received_balance) ?
            (float)$rawData->pending_received_balance : 0;

        return $address;
    }
}
