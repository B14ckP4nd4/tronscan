<?php


namespace BlackPanda\TronScan\Parse;


use BlackPanda\TronScan\Contracts\Parser;
use BlackPanda\TronScan\utils\Math;

class TokenBalance implements Parser
{

    public static function parse($givenData)
    {
        // parse TronBalance
        if($givenData->tokenId == '_'){
            return self::parseTron($givenData);
        }

        if($givenData->tokenType == 'trc10'){
            return self::parseTRC10Token($givenData);
        }

        if($givenData->tokenType == 'trc20'){
            return self::parseTRC20Token($givenData);
        }

        throw new \Exception('Wrong data for parsing');

    }

    public static function parseTRC20Token($givenData){

        $result = new \stdClass();

        $result->token_id = $givenData->tokenId;
        $result->balance = Math::toDecimal($givenData->balance , $givenData->tokenDecimal);
        $result->smart_contract = $givenData->tokenId;
        $result->name = $givenData->tokenName;
        $result->symbol = $givenData->tokenAbbr;
        $result->decimal = $givenData->tokenDecimal;
        $result->type = $givenData->tokenType;
        $result->logo_url = $givenData->tokenLogo;
        $result->vip = $givenData->vip;
        $result->price_in_trx = $givenData->tokenPriceInTrx ?? 0;
        $result->amount_in_trx = $givenData->amount ?? 0;
        $result->holders = $givenData->nrOfTokenHolders;
        $result->transfers = $givenData->transferCount;


        return $result;
    }

    public static function parseTRC10Token($givenData){
        $result = new \stdClass();

        $result->token_id = $givenData->tokenId;
        $result->balance = Math::toDecimal($givenData->balance , $givenData->tokenDecimal);
        $result->name = $givenData->tokenName;
        $result->symbol = $givenData->tokenAbbr;
        $result->decimal = $givenData->tokenDecimal;
        $result->type = $givenData->tokenType;
        $result->logo_url = $givenData->tokenLogo;
        $result->vip = $givenData->vip;
        $result->price_in_trx = $givenData->tokenPriceInTrx ?? 0;
        $result->amount_in_trx = $givenData->amount ?? 0;
        $result->holders = $givenData->nrOfTokenHolders;
        $result->transfers = $givenData->transferCount;


        return $result;
    }

    public static function parseTron($givenData){
        $result = new \stdClass();

        $result->token_id = $givenData->tokenId;
        $result->balance = Math::toDecimal($givenData->balance , $givenData->tokenDecimal);
        $result->name = $givenData->tokenName;
        $result->symbol = $givenData->tokenAbbr;
        $result->decimal = $givenData->tokenDecimal;
        $result->type = $givenData->tokenType;
        $result->logo_url = $givenData->tokenLogo;
        $result->vip = $givenData->vip;
        $result->price_in_trx = 1;
        $result->amount_in_trx = Math::toDecimal($givenData->balance , $givenData->tokenDecimal);


        return $result;
    }
}
