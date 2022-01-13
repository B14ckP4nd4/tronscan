<?php


namespace BlackPanda\TronScan;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use function PHPUnit\Framework\isJson;

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

    /**
     * get status of tronscan api
     */
    public function systemStatus()
    {
        return $this->request('GET', 'system/status');
    }

    /**
     * details and balance of specific account
     * @param string $address
     * @return mixed|string
     * @throws GuzzleException
     */
    public function getAccount(string $address)
    {
        $params = ['address' => $address];

        return $this->request('GET', 'account', $params);
    }


    /**
     * show all transaction for specific address
     * @param string $address
     * @param int|null $start_timestamp
     * @param int|null $end_timestamp
     * @param int $limit
     * @param int $start
     * @param string $sort
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     * @throws GuzzleException
     */
    public function getAccountTransactions(string $address, int $start_timestamp = null, int $end_timestamp = null, int $limit = 20, int $start = 0, string $sort = '-timestamp')
    {
        /*
         * set params
         */
        $params = [
            'address' => $address,
            'sort' => $sort,
            'limit' => $limit,
            'start' => $start,
            'count' => true,
            'start_timestamp' => $start_timestamp ?? null,
            'end_timestamp' => $end_timestamp ?? null,
        ];

        return $this->request('GET', 'transaction', $params);
    }


    public function getTransaction(string $hash)
    {
        $params = [
            'hash' => $hash
        ];

        return $this->request('GET', 'transaction-info', $params);
    }


    /**
     * get Transfers of Specific Address
     * @param string $address
     * @param string $token
     * @param int|null $start_timestamp
     * @param int|null $end_timestamp
     * @param int $limit
     * @param int $start
     * @param string $sort
     * @return mixed|string
     * @throws GuzzleException
     */
    public function getAccountTransfers(string $address, string $token = '_', int $start_timestamp = null, int $end_timestamp = null, int $limit = 20, int $start = 0, string $sort = '-timestamp')
    {
        /*
         * set params
         */
        $params = [
            'address' => $address,
            'sort' => $sort,
            'limit' => $limit,
            'start' => $start,
            'count' => true,
            'start_timestamp' => $start_timestamp ?? null,
            'end_timestamp' => $end_timestamp ?? null,
        ];

        return $this->request('GET', 'transfer', $params);
    }

    public function getContract(string $contract_address)
    {
        $params = ['contract' => $contract_address];
        return $this->request('GET', 'contract', $params);
    }

    /**
     * handle Requests
     * @param string $method
     * @param string $endpoint
     * @param array $queries
     * @return mixed|string
     * @throws GuzzleException
     */
    private function request(string $method, string $endpoint, array $queries = [])
    {
        $request = $this->api->request($method, $endpoint, [
            'query' => $queries,
        ]);

        $result = $request->getBody()->getContents();

        return (isJson($result)) ? json_decode($result) : $result;
    }
}
