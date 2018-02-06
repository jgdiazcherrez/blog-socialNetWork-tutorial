<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 30/01/2018
 * Time: 0:09
 */

require_once '../vendor/autoload.php';

$feed = Social\Feed::getInstance();
$elPaisNews = $feed->getNews('http://ep00.epimg.net/rss/elpais/portada.xml');
$eldiarioNews = $feed->getNews('http://www.eldiario.es/rss/');
$elmundoNews = $feed->getNews('http://estaticos.elmundo.es/elmundo/rss/portada.xml');
$totalNews = count($elPaisNews) + count($eldiarioNews) + count($elmundoNews);
printf("Se van a procesar un total de %s noticias \n", $totalNews);
$facebook =  Social\Facebook::getInstance();
$facebook->connect('your_app_id', 'your_app_secret');
$elpaisSharesCountFb = $facebook->getSharesCount($elPaisNews);
$eldiarioSharesCountFb = $facebook->getSharesCount($eldiarioNews);
$elmundoSharesCountFb = $facebook->getSharesCount($elmundoNews);
$twitter = Social\Twitter::getInstance();
$twitter->connect('your_consumer_key', 'your_consumer_secret');
$elpaisSharesCountTw = $twitter->getSharesCount($elPaisNews);
$eldiarioSharesCountTw = $twitter->getSharesCount($eldiarioNews);
$elmundoSharesCountTw = $twitter->getSharesCount($elmundoNews);
$data = [
    'facebook' => [
            'elpais' => $elpaisSharesCountFb,
            'eldiario' => $eldiarioSharesCountFb,
            'elmundo' => $elmundoSharesCountFb
    ],
    'twitter' => [
            'elpais' => $elpaisSharesCountTw,
            'eldiario' => $eldiarioSharesCountTw,
            'elmundo' => $elmundoSharesCountTw
    ]
];
var_dump($data);
file_put_contents('data/test.json', json_encode($data));
die;





