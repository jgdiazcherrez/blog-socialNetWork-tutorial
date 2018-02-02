<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 28/01/2018
 * Time: 17:04
 */

namespace Social;

use GuzzleHttp\Client;
/**
 * Class Facebook
 * @author Jonathan Díaz
 * @package Social
 */
class Facebook implements  INetWork {

    const LIMIT_IDS = 50;
    const ENDPOINT_GRAPH_API = 'https://graph.facebook.com/v2.11/';
    const ENDPOINT_OAUTH = 'https://graph.facebook.com/oauth/access_token';
    const STATUS_OK =  200;
    const DEFAULT_TIMEOUT = 200;

    private $_access_token;
    /**
     * @var Client
     */
    private $_clientRequest;

    /**
     * Facebook constructor.
     */
    public function __construct()
    {
        $this->_clientRequest = new Client();
    }

    /**
     * Oauth 2.0 authentification
     * @param String $app_id
     * @param String $app_secret
     * @throws \Exception
     * @return null
     */
    public function connect(String $app_id, String $app_secret){
        try{
            $response = $this->_doRequest('GET', $this->_getTokenUrl($app_id, $app_secret));
            $this->_access_token = $response['access_token'];
        }
        catch (\Exception $ex){
                throw new \Exception('Error Facebook Oauth request:' . $ex->getMessage());
        }
    }

    /**
     * Get Shares Count
     * @param array $urls
     * @return array
     * @throws \Exception
     */
    public function getSharesCount(array $urls) : array{
        if(!$this->_access_token)
            throw new \Exception("You haven't connected to the Social Network");
        $chunkData = array_chunk($urls, self::LIMIT_IDS);
        $dataShares = [];
        foreach($chunkData as $itemData){
            try{
                $dataRequest = $this->_doRequest('GET', self::ENDPOINT_GRAPH_API, $this->_getSharesParams($itemData));
                $dataShares = $this->_fillSharesData($dataRequest, $dataShares);
            }
            catch(\Exception $ex){
                throw new \Exception('Error obtaining shares count:' . $ex->getMessage());
            }
        }
        return $dataShares;
    }

    private function _fillSharesData($dataRequest, $dataShares) : array {
        foreach ($dataRequest as $itemGraph){
            if(isset($itemGraph['engagement'])){
                array_push($dataShares, [
                    'id' => $itemGraph['id'],
                    'engagement' => $itemGraph['engagement']
                ]);
            }
        }
        return $dataShares;
    }

    private function _doRequest($method, $uri, $params = []) : array {
        sleep(1);
        $response = $this->_clientRequest->request($method, $uri, $params);
        if($response->getStatusCode() != self::STATUS_OK)
            throw new \Exception('Invalid Status Code');
        return json_decode($response->getBody()->getContents(), true);
    }

    private function _getSharesParams($itemData) : array {
        return
            [
            'timeout' => self::DEFAULT_TIMEOUT,
            'query' =>
                [
                    'access_token' => $this->_access_token,
                    'ids' => $this->_retrieveListIds($itemData),
                    'fields' => "engagement"
                ]
            ];
    }

    private function _getTokenUrl($app_id, $app_secret): string
    {
        return self::ENDPOINT_OAUTH ."?client_id=$app_id&client_secret=$app_secret&grant_type=client_credentials";
    }

    private function _retrieveListIds($itemData) : string{
        $ids = "";
        foreach($itemData as $keyItem => $itemURL){
            if(!Validator::isUrl($itemURL))
                throw new \Exception('Invalid URL');
            if($keyItem == 0)
                $ids .=  $itemURL;
            else
                $ids .= ',' . $itemURL;
        }
        return $ids;
    }
}

