<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 06/02/2018
 * Time: 22:16
 */

require_once '../vendor/autoload.php';
$twitter = Social\Twitter::getInstance();
$twitter->connect('your_consumer_key', 'your_consumer_secret');
//meter noticias actualizadas
$data = $twitter->getSharesCount(['https://www.elconfidencial.com/mercados/2018-02-06/bolsa-ibex35-cotizacion-wall-street-desplome_1517626/','http://google.es', 'https://elpais.com/economia/2018/02/06/actualidad/1517917303_830180.html']);
var_dump($data);
die;