<?php

/**
 * @author danil danil.kabluk@gmail.com
 */
class Controller
{
    const MINIMAL_SEND_VALUE = 0.00002;
    /** How much to collect from users transactions */
    const FEE_PERCENT = 0.1;
    /** Address where we send collected from transactions fee */
    const FEE_ADDRESS = '2N7bip9cva9GnQx8simzn5cTUY2dNAk47Gb';

    /** Transaction fee that will be charged by sender */
    const TRANSACTION_FEE = 0.00010;

    private $_blockio;

    public function __construct($apiKey, $pin)
    {
        $apiKey = "fede-5699-cf4c-59f9";
        $pin = "8K736MA8Y5N";

        $this->_blockio = new BlockIo($apiKey, $pin, 2);
    }

    /**
     * @param $userId
     * @return Address[]
     * @throws HttpException
     */
    public function getUserAddresses($userId)
    {
        $result = $this->_blockio->get_address_balance(['labels' => implode(',', $this->generateLabels($userId))]);

        if ($result->status === 'fail') {
            throw new HttpException($result->data->error_message);
        }

        $wallet = Wallet::instantiate($result->data);

        return $wallet->addresses;
    }

    /**
     * Creates two addresses for user - public and private
     * @param $userId
     * @return Address[]
     * @throws HttpException
     */
    public function createUserAddresses($userId)
    {
        $addresses = [];
        foreach ($this->generateLabels($userId) as $label) {
            $result = $this->_blockio->get_new_address(['label' => $label]);

            // If address is already created - skip fail response,
            // except those cases when error message is not about existing address
            if ($result->status === 'fail') {
                if (strpos($result->data->error_message, 'already exists') === false) {
                    throw new HttpException($result->data->error_message);
                }
            } else {
                $addresses[] = Address::instantiate($result->data);
            }
        }

        if (!$addresses) {
            throw new BadMethodCallException('Addresses are already created, use Controller::getUserAddresses() isntead');
        }

        return $addresses;
    }

    public function getUserBalance($userId)
    {
    }

    public function send($userId, $toAddress)
    {
    }

    private function generateLabels($userId)
    {
        return ["user.{$userId}.private", "user.{$userId}.public"];
    }
}
