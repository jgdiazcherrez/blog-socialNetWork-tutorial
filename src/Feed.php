<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 28/01/2018
 * Time: 17:24
 */

namespace Social;
use GuzzleHttp\Client;

/**
 * Class Feed
 * @author Jonathan Díaz
 * @package Social
 */
class Feed extends ClientRequest  {


    const DEFAULT_TIMEOUT = 100;

    public static function getInstance()
    {
        static $i;
        if(!$i){
            $i = new Feed(new Client());
        }
        return $i;
    }
    /**
     * @param String $feedUrl
     * @return array
     * @throws \Exception
     */
    public function getNews(string $feedUrl):array{

        try{
            if(!Validator::isUrl($feedUrl))
                throw new \Exception('Invalid URL');
            $news = $this->_fillData($this->_doRequest('GET',
                $feedUrl,
                [   'timeout' => self::DEFAULT_TIMEOUT,
                    'headers' => ['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)']
                ],
            false));
        }
        catch(\Exception $e){
            throw new \Exception('Error obtaining Feed Data');
        }
        return $news;
    }
    private function _fillData($xmlRequest) : array{
        $dataXML = simplexml_load_string($xmlRequest, null, LIBXML_NOCDATA);
        date_default_timezone_set('Europe/Madrid');
        if(!$dataXML || !isset($dataXML->channel->item)){
           throw new \Exception('Invalid Feed, check structure');
        }
        $news = [];
        foreach($dataXML->channel->item as $itemXML){
            $url = $this->_removeQueryString((string) $itemXML->link);
            array_push($news, $url);
        }
        return $news;
    }
}