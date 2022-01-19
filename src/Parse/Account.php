<?php


namespace BlackPanda\TronScan\Parse;


use BlackPanda\TronScan\Contracts\Parser;

class Account implements Parser
{

    public static function parse($givenData)
    {
        /*
         * outputs
         */
        $output = new \stdClass();
        $balance = [];

        // TRC20 Balance
        foreach ($givenData->tokens as $token){
            $balance[] = TokenBalance::parse($token);
        }


        // Set parsed data
        $output->address = $givenData->address;
        $output->balance = $balance;
        $output->transaction_in = $givenData->transactions_in;
        $output->transaction_out = $givenData->totalTransactionCount -  $givenData->transactions_in;
        $output->total_transactions = $givenData->totalTransactionCount;


        return $output;
    }

}
