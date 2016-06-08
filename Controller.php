<?php

/**
 * @author danil danil.kabluk@gmail.com
 */
class Controller
{
    const MINIMAL_SEND_VALUE = 0.00002;
    /** How much to collect from users transactions */
    const FEE_PERCENT = 1;
    /** Address where we send collected from transactions fee */
    const FEE_ADDRESS = '2N7bip9cva9GnQx8simzn5cTUY2dNAk47Gb';

    /** Transaction fee that will be charged by sender, approx 5 Cents */
    const TRANSACTION_FEE = 0.0001;

    private $_blockio;

    public function __construct($apiKey, $pin)
    {
        $this->_blockio = new BlockIo($apiKey, $pin, 2);
    }

    /**
     * @param $userId
     * @return Wallet
     * @throws HttpException
     */
    public function getUserWallet($userId)
    {
        // Disabled for gh-pages
//        $labels = implode(',', $this->generateLabels($userId));
        $labels = $userId;
        $result = $this->_blockio->get_address_balance(['labels' => $labels]);

        if ($result->status === 'fail') {
            throw new HttpException($result->data->error_message);
        }

        $wallet = Wallet::instantiate($result->data);

        return $wallet;
    }

    /**
     * Creates two addresses for user - public and private
     * @param $userId
     * @return Wallet
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

        $wallet = new Wallet();
        $wallet->addresses = $addresses;

        return $wallet;
    }

    /**
     * TODO: we don't need to add transaction fee - it will be deducted automatically
     * @param $userId
     * @param $toAddress
     * @param float $amount
     * @return bool
     * @throws HttpException
     */
    public function send($userId, $toAddress, $amount)
    {
        $wallet = $this->getUserWallet($userId);

        // Prepare user source addresses.
        $sourceAddresses = array_map(function (Address $address) {
            return $address->address;
        }, $wallet->addresses);
        $sourceAddresses = implode(',', $sourceAddresses);

        $fee = $this->getOurFee($amount);

        // Prepare amounts: user operation + send our fee to our address.
        $amounts = [$amount, $fee];
        $toAddresses = [$toAddress, self::FEE_ADDRESS];

        $result = $this->_blockio->withdraw_from_addresses([
            'amounts' => $amounts,
            'from_addresses' => $sourceAddresses,
            'to_addresses' => $toAddresses,
        ]);
        if ($result->status === 'fail') {
            throw new HttpException('Error by sending the amount: ' . print_r($result->data, true));
        }

        return true;
    }

    /**
     * Returns calculated total amount that will be sent: transaction fee, our fee.
     * @param $sendAmount
     * @return float
     */
    public function getTotalCalculatedAmount($sendAmount)
    {
        $totalAmount = (float)$sendAmount;
        // First - our fee.
        $totalAmount += $this->getOurFee($sendAmount);
        // Transaction fee.
        $totalAmount += self::TRANSACTION_FEE;

        return $totalAmount;
    }

    private function getOurFee($amount)
    {
        $fee = $amount / 100 * self::FEE_PERCENT;

        return $fee;
    }

    private function generateLabels($userId)
    {
        return ["user.{$userId}.private", "user.{$userId}.public"];
    }
}
