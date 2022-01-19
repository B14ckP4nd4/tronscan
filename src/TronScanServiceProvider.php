<?php


namespace BlackPanda\TronScan;


use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\App;

class TronScanServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind('TronScan', function () {
            return new TronScan; //Add the proper namespace at the top
        });
    }

}
