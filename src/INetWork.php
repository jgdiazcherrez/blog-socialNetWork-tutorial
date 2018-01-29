<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 28/01/2018
 * Time: 17:05
 */
namespace Social;

interface INetWork {
    /**
     * @param $app_id
     * @param $app_secret
     * @return string
     */
    public function connect(String $app_id, String $app_secret);



    /**
     * @param $urls
     * @return mixed
     */
    public function getSharesCount(array $urls) : array;
}