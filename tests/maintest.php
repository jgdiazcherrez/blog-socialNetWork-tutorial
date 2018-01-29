<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 30/01/2018
 * Time: 0:09
 */

require_once '../vendor/autoload.php';

$feed = new Social\Feed();
$facebook = new Social\Facebook();
$elPaisNews = $feed->getNews('http://ep00.epimg.net/rss/elpais/portada.xml');
$eldiarioNews = $feed->getNews('http://www.eldiario.es/rss/');
$elmundoNews = $feed->getNews('http://estaticos.elmundo.es/elmundo/rss/portada.xml');
$facebook->connect('your_app_id', 'your_app_secret');
$elpaisSharesCountFb = $facebook->getSharesCount($elPaisNews);
$eldiarioSharesCountFb = $facebook->getSharesCount($eldiarioNews);
$elmundoSharesCountFb = $facebook->getSharesCount($elmundoNews);

$data = [
    'elpais' => $elpaisSharesCountFb,
    'eldiario' => $eldiarioNews,
    'elmundo' => $elmundoSharesCountFb
];

var_dump($data);





