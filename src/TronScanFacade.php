<?php


namespace BlackPanda\TronScan;


use Illuminate\Support\Facades\Facade;

class TronScanFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TronScan';
    }

}
