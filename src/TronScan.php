<?php


namespace BlackPanda\TronScan;


use BlackPanda\TronScan\Parse\Account;
use BlackPanda\TronScan\Parse\TokenBalance;
use BlackPanda\TronScan\Parse\TransactionsList;
use BlackPanda\TronScan\Parse\TRC20Balance;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;
use function PHPUnit\Framework\isJson;

class TronScan
{
    /*
     * url of the tronScan API
     */
    private $api_url = 'https://apilist.tronscan.org/api/';

    private $api;

    public function __construct(int $rateLimit = 20)
    {
        $myApi = HandlerStack::create();
        $myApi->push(RateLimiterMiddleware::perMinute($rateLimit));
        $this->api = new Client(
            [
                'base_uri' => $this->api_url,
                'handler' => $myApi,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Connection' => 'keep-alive',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'User-Agent' => 'Mozilla/5.0 (X11; U; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/103.0.5154.180 Chrome/103.0.5154.180 Safari/537.36',
                ],
            ]
        );
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

        /**
         * get content
         */
        $content = $this->request('GET', 'account', $params);

        return Account::parse($content);
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

        return  TransactionsList::parse($this->request('GET', 'transaction', $params));
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

    /**
     * get tokens list
     * @param int $start
     * @param string $order
     * @param int|null $start_timestamp
     * @param int|null $end_timestamp
     * @param string $sort
     * @param string $filter
     * @return mixed|string
     * @throws GuzzleException
     */
    public function getTokensList(int $start= 0,string $order = 'desc', int $start_timestamp = null, int $end_timestamp = null,string $sort = 'volume24hInTrx', string $filter='all')
    {
        /*
         * set params
         */
        $params = [
            'start' => $start,
            'order' => $order,
            'start_timestamp' => $start_timestamp,
            'end_timestamp' => $end_timestamp,
            'sort' => $sort,
            'filter' => $filter,
        ];

        return $this->request('GET', 'tokens/overview', $params);
    }

    /**
     * get TRC10 tokens list
     * @param int $start
     * @param string $order
     * @param int|null $start_timestamp
     * @param int|null $end_timestamp
     * @param string $sort
     * @param string $filter
     * @return mixed|string
     * @throws GuzzleException
     */
    public function getTRC10Tokens(int $start= 0,string $order = 'desc', int $start_timestamp = null, int $end_timestamp = null,string $sort = 'volume24hInTrx', string $filter='trc10')
    {
        return $this->getTokensList($start , $order, $start_timestamp, $end_timestamp , $sort , $filter);
    }

    /**
     * get TRC20 Tokens list
     * @param int $start
     * @param string $order
     * @param int|null $start_timestamp
     * @param int|null $end_timestamp
     * @param string $sort
     * @param string $filter
     * @return mixed|string
     * @throws GuzzleException
     */
    public function getTRC20Tokens(int $start= 0,string $order = 'desc', int $start_timestamp = null, int $end_timestamp = null,string $sort = 'volume24hInTrx', string $filter='trc20')
    {
        return $this->getTokensList($start , $order, $start_timestamp, $end_timestamp , $sort , $filter);
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
//        $request = $this->api->request($method, $endpoint, [
//            'query' => $queries,
//        ]);
//
//        $result = $request->getBody()->getContents();
//
//        return (isJson($result)) ? \json_decode($result) : $result;

        $ch = curl_init();

        $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'Connection: keep-alive',
                'Accept-Encoding: gzip, deflate, br',
                'User-Agent: Mozilla/5.0 (X11; U; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/103.0.5154.180 Chrome/103.0.5154.180 Safari/537.36',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_URL, $this->api_url . http_build_query($queries));
        // Time OUT
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        // Turn off the server and peer verification (TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
        // UserAgent
        curl_setopt($ch, CURLOPT_USERAGENT, 'TronScan API');
        // Cookies
        if (!empty($cookies)) {
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);
            curl_setopt($ch, CURLOPT_COOKIE, http_build_query($cookies, '', '; '));
        }
        // Params
        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Get Response
        $response = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code != 200) {
            throw new \Exception("There is a problem to get results. curl Status code {$http_code}");
        }
        curl_close($ch);

        return (isJson($response)) ? \json_decode($response) : $response;
    }

    private function json_decode($json){
        $result = json_decode($json);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        return  $json;
    }

}
