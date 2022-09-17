<?php


namespace BlackPanda\TronScan\Parse;


use BlackPanda\TronScan\Contracts\Parser;
use BlackPanda\TronScan\TronScan;
use BlackPanda\TronScan\utils\Math;

class TransactionsList implements Parser
{

    public static function parse($givenData)
    {
        // rate Limited TronScan
        $tronScan = new TronScan(20);

        if(property_exists($givenData, 'message'))
            throw new \Exception($givenData->message);

        if (!property_exists($givenData, 'data')) {
            $givenData->transactionsCount = $givenData->rangeTotal;
            $givenData->transactions = [];
            return $givenData;
        }


        $transactionsCount = count($givenData->data);

        $parsedTransactions = [];
        /*
         * parse Data
         */
        foreach ($givenData->data as $key => $transaction) {

            $transactionData = [];

            /*
             * TRC 20 Transaction
             */
            if ($transaction->contractType == 31) {
                $transactionData = (new TransactionsList)->ParseTRC20Transaction($transaction,$tronScan);
            }

            /*
             * TRC10 Transactions
             */
            if (in_array($transaction->contractType, [1, 2])) {
                $transactionData = (new TransactionsList)->ParseTRC10Transaction($transaction);
            }


            if ($transactionData)
                $parsedTransactions[] = $transactionData;

        }

        /*
         * set new data's
         */
        $givenData->transactionsCount = $transactionsCount;
        $givenData->transactions = $parsedTransactions;

        // unset unnecessary data's
        unset($givenData->data);


        return $givenData;
    }

    public static function ParseTRC20Transaction($transaction,TronScan $tronScan = null)
    {
        /*
         * get complete information of transaction
         */
        if(is_null($tronScan)){
            $transactionInfo = \TronScan::getTransaction($transaction->hash);
        }
        else{
            $tronScan->getTransaction($transaction->hash)
        }

        /*
         * ignore shielded transactions
         */
        if(isset($transactionInfo->triggerContractType) && $transactionInfo->triggerContractType == 502)
            return false;

        /*
         * ignore if the transaction isn't transfer
         */
        if(!isset($transactionInfo->tokenTransferInfo))
            return false;

        $output = new \stdClass();
        $output->hash = $transactionInfo->hash;
        $output->timestamp = $transactionInfo->timestamp;
        $output->from = $transactionInfo->ownerAddress;

        $output->to = $transactionInfo->tokenTransferInfo->to_address;
        $output->coin = $transactionInfo->tokenTransferInfo->name;
        $output->symbol = $transactionInfo->tokenTransferInfo->symbol;
        $output->contract_address = $transactionInfo->tokenTransferInfo->contract_address;
        $output->amount = Math::toDecimal($transactionInfo->tokenTransferInfo->amount_str, $transactionInfo->tokenTransferInfo->decimals);
        $output->note = null;

        $output->tag = null;

        $output->tokenType = 'trc20';
        $output->status = $transactionInfo->contractRet;
        $output->confirmed = $transactionInfo->confirmed;
        $output->confirms = $transactionInfo->confirmations;

        return $output;
    }


    public function ParseTRC10Transaction($transaction)
    {
        $output = new \stdClass();

        $output->hash = $transaction->hash;
        $output->timestamp = $transaction->timestamp;
        $output->from = $transaction->ownerAddress;
        $output->to = $transaction->toAddress;
        $output->tag = null;
        $output->note = null;
        $output->coin = $transaction->tokenInfo->tokenName;
        $output->symbol = $transaction->tokenInfo->tokenAbbr;
        $output->tokenType = $transaction->tokenType;
        $output->tokenId = $transaction->tokenInfo->tokenId;
        $output->amount = Math::toDecimal($transaction->amount, $transaction->tokenInfo->tokenDecimal);
        $output->status = $transaction->result;
        $output->confirmed = $transaction->confirmed;
        $output->confirms = null;

        return $output;
    }
}
