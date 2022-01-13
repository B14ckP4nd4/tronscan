<?php


namespace BlackPanda\TronScan;
class TronScan
{
    /*
     * url of the tronScan API
     */
    private $api_url = 'https://apilist.tronscan.org/api/';

    private $api;

    public function __construct()
    {
        $this->api = new Client(['base_uri' => $this->api_url]);
    }

}
