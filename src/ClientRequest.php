<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 05/02/2018
 * Time: 22:51
 */
namespace Social;
use GuzzleHttp\Client;

class ClientRequest {

    const STATUS_OK =  200;
    protected $_clientRequest;
    protected $_access_token;

    protected function __construct(Client $client)
    {
        $this->_clientRequest = $client;
    }
    protected function _assertConnection(){
        if(!$this->_access_token)
            throw new \Exception("You haven't connected to the Social Network");
    }

    protected function _removeQueryString(string $url) :string{
        $urlParsed = parse_url($url);
        return $urlParsed['scheme'].'://' . $urlParsed['host']  . $urlParsed['path'];
    }

    protected function _doRequest($method, $uri, $params = [], $toJson = true) {
        sleep(1);
        $response = $this->_clientRequest->request($method, $uri, $params);
        if($response->getStatusCode() != self::STATUS_OK)
            throw new \Exception('Invalid Status Code');
        return ($toJson) ? json_decode((string)$response->getBody(), true) :  (string)$response->getBody();
    }
}