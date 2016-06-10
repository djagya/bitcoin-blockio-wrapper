<?php

/**
 * @author danil danil.kabluk@gmail.com
 */
class Controller
{
    const MINIMAL_SEND_VALUE = 0.00002;
    /** How much to collect from users transactions */
    const FEE_PERCENT = 1;
    const MIN_FEE = 0.0001;
    /** Address where we send collected from transactions fee */
    const FEE_ADDRESS = '2N7DV7g752P6wAyi2NYqNawTNhCw4tTj8iT';

    /** Transaction fee that will be charged by sender, approx 5 Cents */
    const TRANSACTION_FEE = 0.0001;

    const TAKE_TRANSACTION_FEE_FROM_SENDER = false;

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
     * @throws Exception
     */
    public function createUserAddresses($userId)
    {
        $addresses = [];
        foreach ($this->generateLabels($userId) as $label) {
            try {
                $result = $this->_blockio->get_new_address(['label' => $label]);
                $addresses[] = Address::instantiate($result->data);
            } catch (\Exception $e) {
                // If address is already created - skip fail response,
                // except those cases when error message is not about existing address
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
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

        // Fees.
        $ourFee = sprintf('%f', $this->getOurFee($amount));

        // If we take transaction fees from the receiver, then we need to decrease sent amount.
        if (!self::TAKE_TRANSACTION_FEE_FROM_SENDER) {
            $amount -= self::TRANSACTION_FEE * 2;
        }

        // Deduct our fee from the amount.
        $amount -= $ourFee;

        // Prepare amounts: user operation + send our fee to our address.
        $amounts = implode(',', [$amount, $ourFee]);
        $toAddresses = implode(',', [$toAddress, self::FEE_ADDRESS]);

        try {
            $this->_blockio->withdraw_from_addresses([
                'amounts' => $amounts,
                'from_addresses' => $sourceAddresses,
                'to_addresses' => $toAddresses,
            ]);
        } catch (Exception $e) {
            throw new Exception("Amounts: {$amounts}, message: " . $e->getMessage());
        }

        return true;
    }

    /**
     * Returns calculated total amount that will be sent: transaction fee (if from sender), our fee.
     * @param $sendAmount
     * @return float
     */
    public function getTotalCalculatedAmount($sendAmount)
    {
        $totalAmount = (float)$sendAmount;
        // First - our fee.
        $totalAmount += $this->getOurFee($sendAmount);

        // Transaction fee.
        if (self::TAKE_TRANSACTION_FEE_FROM_SENDER) {
            $totalAmount += self::TRANSACTION_FEE;
        }

        return $totalAmount;
    }

    private function getOurFee($amount)
    {
        $percentValue = $amount / 100 * self::FEE_PERCENT;
        if (self::TAKE_TRANSACTION_FEE_FROM_SENDER) {
            // We have to add transaction fee, because our fee will be sent in separate transaction.
            $fee = self::TRANSACTION_FEE + $percentValue;
        } else {
            $fee = max(self::MIN_FEE, $percentValue);
        }

        return $fee;
    }

    private function generateLabels($userId)
    {
        return ["user.{$userId}.private", "user.{$userId}.public"];
    }
}
