<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 29/01/2018
 * Time: 23:59
 */

require_once '../vendor/autoload.php';
$feed =  Social\Feed::getInstance();
var_dump($feed->getNews('http://ep00.epimg.net/rss/elpais/portada.xml'));
die;
