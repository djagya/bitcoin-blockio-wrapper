<?php
/**
 * Controller class.
 *
 * @copyright Copyright (c) 2016 Danil Zakablukovskii
 * @package djagya/bitcoin
 * @author Danil Zakablukovskii <danil.kabluk@gmail.com>
 */

namespace djagya\bitcoin;

/**
 * @author danil danil.kabluk@gmail.com
 */
class Controller
{
    /** Transaction fee that will be charged by sender, approx 5 Cents */
    const TRANSACTION_FEE = 0.0001;

    static public $minimalSendValue = 0.0003;
    /** How much to collect from users transactions */
    static public $feePercent = 1;
    static public $minFee = 0.0001;
    /** Address where we send collected from transactions fee */
    static public $feeAddress = '';
    static public $transactionFeeFromSender = true;

    private $_blockio;

    public function __construct($apiKey, $pin)
    {
        $this->_blockio = new \BlockIo($apiKey, $pin, 2);
    }

    /**
     * @param $userId
     * @return Wallet
     * @throws \HttpException
     */
    public function getUserWallet($userId)
    {
        $labels = implode(',', $this->generateLabels($userId));
        $result = $this->_blockio->get_address_balance(['labels' => $labels]);

        $wallet = Wallet::instantiate($result->data);

        return $wallet;
    }

    /**
     * Creates two addresses for user - public and private
     * @param $userId
     * @return Wallet
     * @throws \Exception
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
            throw new \BadMethodCallException('Addresses are already created, use Controller::getUserAddresses() isntead');
        }

        $wallet = new Wallet();
        $wallet->addresses = $addresses;

        return $wallet;
    }

    /**
     * We don't need to add transaction fee to the sent amount - it will be deducted automatically.
     * @param $userId
     * @param $toAddress
     * @param float $amount
     * @return bool
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
        if (!self::$transactionFeeFromSender) {
            $amount -= self::TRANSACTION_FEE * 2;
        }

        // Deduct our fee from the amount.
        $amount -= $ourFee;

        // Prepare amounts: user operation + send our fee to our address.
        $amounts = implode(',', [$amount, $ourFee]);
        $toAddresses = implode(',', [$toAddress, self::$feeAddress]);

        $this->_blockio->withdraw_from_addresses([
            'amounts' => $amounts,
            'from_addresses' => $sourceAddresses,
            'to_addresses' => $toAddresses,
        ]);

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
        if (self::$transactionFeeFromSender) {
            $totalAmount += self::TRANSACTION_FEE;
        }

        return $totalAmount;
    }

    private function getOurFee($amount)
    {
        $percentValue = $amount / 100 * self::$feePercent;
        if (self::$transactionFeeFromSender) {
            // We have to add transaction fee, because our fee will be sent in separate transaction.
            $fee = self::TRANSACTION_FEE + $percentValue;
        } else {
            $fee = max(self::$minFee, $percentValue);
        }

        return $fee;
    }

    protected function generateLabels($userId)
    {
        return ["user.{$userId}"];
    }

    /**
     * @param $userId
     * @return Transaction[] ordered by time desc
     */
    public function getTransactions($userId)
    {
        $labels = implode(',', $this->generateLabels($userId));
        $received = $this->_blockio->get_transactions(['type' => 'received', 'labels' => $labels]);
        $sent = $this->_blockio->get_transactions(['type' => 'sent', 'labels' => $labels]);

        $transactions = [];
        foreach (array_merge($received->data->txs, $sent->data->txs) as $transaction) {
            $transactions[] = Transaction::instantiate($transaction);
        }

        // Sort transactions by time desc.
        usort($transactions, function (Transaction $a, Transaction $b) {
            return ($a->time > $b->time) ? -1 : 1;
        });

        return $transactions;
    }
}
