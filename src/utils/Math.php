<?php


namespace BlackPanda\TronScan\utils;


class Math
{

    public static function divideFloat(float $numOne , float $numTwo)
    {

    }

    public static function toDecimal($number , int $decimals)
    {
        $number = floatval($number);
        $pow = pow(10 , $decimals);
        $div =  fdiv($number , $pow );
        return sprintf('%f',self::floor($div , $decimals));
    }

    public static function floor($val, $precision)
    {
        $mult = pow(10, $precision);
        return floor($val * $mult) / $mult;
    }

}
