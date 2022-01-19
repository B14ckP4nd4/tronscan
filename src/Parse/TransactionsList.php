<?php


namespace BlackPanda\TronScan\Parse;


use BlackPanda\TronScan\Contracts\Parser;
use BlackPanda\TronScan\utils\Math;

class TransactionsList implements Parser
{

    public static function parse($givenData)
    {
        $transactionsCount = count($givenData->data);

        $parsedTransactions = [];
        /*
         * parse Data
         */
        foreach ($givenData->data as $key => $transaction){

            $transactionData = [];

            /*
             * TRC 20 Transaction
             */
            if($transaction->contractType == 31)
            {
                $transactionData = self::ParseTRC20Transaction($transaction);
            }

            /*
             * TRC10 Transactions
             */
            if($transaction->contractType == 1)
            {
                $transactionData = self::ParseTRC10Transaction($transaction);
            }

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

    public static function ParseTRC20Transaction($transaction)
    {
        /*
         * get complete information of transaction
         */
        $transactionInfo = \TronScan::getTransaction($transaction->hash);

        $output = new \stdClass();
        $output->hash = $transactionInfo->hash;
        $output->timestamp = $transactionInfo->timestamp;
        $output->from = $transactionInfo->tokenTransferInfo->from_address;
        $output->to = $transactionInfo->tokenTransferInfo->to_address;
        $output->tag = null;
        $output->note = null;
        $output->coin = $transactionInfo->tokenTransferInfo->name;
        $output->symbol = $transactionInfo->tokenTransferInfo->symbol;
        $output->amount = Math::toDecimal($transactionInfo->tokenTransferInfo->amount_str , $transactionInfo->tokenTransferInfo->decimals);
        $output->status = $transactionInfo->contractRet;
        $output->confirmed = $transactionInfo->confirmed;
        $output->confirms = $transactionInfo->confirmations;

        return $output;
    }


    public function ParseTRC10Transaction($transaction)
    {
        $output = new \stdClass();

        $output->hash = $transaction->hash ;
        $output->timestamp = $transaction->ownerAddress ;
        $output->from = $transaction->ownerAddress ;
        $output->to = $transaction->toAddress ;
        $output->tag = null;
        $output->note = null;
        $output->coin = $transaction->tokenInfo->tokenName ;
        $output->symbol = $transaction->tokenInfo->tokenAbbr ;
        $output->amount = Math::toDecimal($transaction->amount , $transaction->tokenInfo->tokenDecimal) ;
        $output->status = $transaction->result ;
        $output->confirmed = $transaction->confirmed ;
        $output->confirms = null ;

        return $output;
    }
}
