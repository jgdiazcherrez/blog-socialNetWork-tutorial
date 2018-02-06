<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 05/02/2018
 * Time: 21:49
 */

namespace Social;
use GuzzleHttp\Client;


/**
 * Class Twitter
 * @package Social
 * @author Jonathan Díaz
 */
class Twitter extends ClientRequest  implements  INetWork
{
    const DEFAULT_TIMEOUT = 600;
    const CODE_UNAUTHORIZED = 401;
    const ENDPOINT_OAUTH2_TOKEN = 'https://api.twitter.com/oauth2/token/';
    const ENDPOINT_SEARCH_API = 'https://api.twitter.com/1.1/search/tweets.json';
    const ENDPOINT_LIMIT_REMAIN_API = 'https://api.twitter.com/1.1/application/rate_limit_status.json';

    /**
     * Singleton Instance
     * @return Twitter
     */
    public static function getInstance()
    {
        static $i;
        if(!$i){
            $i = new Twitter(new Client());
        }
        return $i;
    }
    /**
     * Oauth2.0 Authentification
     * @param String $consumer_key
     * @param String $consumer_secret
     * @throws \Exception
     * @return void
     */
    public function connect(String $consumer_key, String $consumer_secret)
    {
        try{
            $dataTokens = $this->_doRequest('POST',
                self::ENDPOINT_OAUTH2_TOKEN,
                [
                "query" =>
                    [
                        "grant_type" => "client_credentials"
                    ],
                'headers' =>
                    [
                        "Content-Type" => "application/x-www-form-urlencoded",
                        "Authorization" => "Basic " .$this->_getEncodedBarer($consumer_key, $consumer_secret)
                    ]
                ]);
            $this->_access_token = $dataTokens["access_token"];
        }
        catch (\Exception $e){
            throw new \Exception('Error Twitter Oauth2.0 request:' . $e->getMessage());
        }
    }

    /**
     * Retrieve Shares count
     * @param array $urls
     * @return array
     * @throws \Exception
     */
    public function getSharesCount(array $urls) : array
    {
        $dataShares = [];
        $limitRemainRequests  = $this->_getLimitApi();
        foreach($urls as $key => $url){
            try{
                if($limitRemainRequests  <= 0)
                    throw new \Exception('You have exceeded the limit', self::CODE_UNAUTHORIZED);
                $response = $this->_doRequest('GET', self::ENDPOINT_SEARCH_API, $this->_getSearchQueryParams($url));
                $dataShares = $this->_calcAndGetTwitterData($response, $dataShares, $url, $key);
            }
            catch(\Exception $e){
                    if($e->getCode() == self::CODE_UNAUTHORIZED){
                        break;
                    }
                    throw new \Exception('Error obtainig twitter shares count: ' . $e->getMessage(), $e->getCode());
            }
            $limitRemainRequests--;
        }
        return $dataShares;
    }

    private function _calcAndGetTwitterData($response, $dataShares, $url, $key):array
    {
        //defect values
        $favoriteCount  = 0;
        $retweetCount = 0;
        $dataShares = $this->_fillTwitterData($dataShares, $url, $favoriteCount, $retweetCount, $key);
        if(count($response['statuses']) > 0){
            foreach($response['statuses'] as $tweet){
                $favoriteCount = $favoriteCount + $tweet['favorite_count'];
                $retweetCount = $retweetCount + $tweet['retweet_count'];
            }
            $dataShares = $this->_fillTwitterData($dataShares, $url, $favoriteCount, $retweetCount, $key);
        }
        return $dataShares;
    }

    private function _fillTwitterData($dataShares, $url, $favoriteCount, $retweetCount, $key){
        $dataShares[$key] = [
            'id' => $url,
            'favorite_count' => $favoriteCount,
            'retweet_count' => $retweetCount
        ];
        return $dataShares;
    }

    private function _getSearchQueryParams($url) : array
    {   $q = $url . " AND exclude:replies  AND exclude:retweets AND -filter:retweets";
        $queryString = ['q' => $q, 'count'=> '100', 'result_type' => 'mixed'];
        return
            [
                'timeout' => self::DEFAULT_TIMEOUT,
                "curl" =>
                    [
                        CURLOPT_TIMEOUT => self::DEFAULT_TIMEOUT,
                        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1
                    ],
                "headers" =>
                    [
                        "Authorization" => "Bearer ". $this->_access_token,
                        "Content-Type" => "application/json"
                    ],
                "query" => $queryString
            ];
    }

    private function _getLimitApi(){
        try{
            $this->_assertConnection();
            $response = $this->_doRequest('GET', self::ENDPOINT_LIMIT_REMAIN_API,[
                "query" => ['resources' => 'search,statuses'],
                "headers" => ["Authorization" => "Bearer ". $this->_access_token, "Content-Type" => "application/json"]]);
            $limitRemain =  $response['resources']['search']['/search/tweets']['remaining'];
        }
        catch(\Exception $e){
            $limitRemain = 0;
        }
        return (int)$limitRemain;
    }



    /**
     * Encoded Bearer
     * @param $consumer_key
     * @param $consumer_secret
     * @return string
     */
    private function _getEncodedBarer($consumer_key, $consumer_secret) : string{
        return base64_encode(urlencode($consumer_key) . ":" . urlencode ($consumer_secret));
    }

}