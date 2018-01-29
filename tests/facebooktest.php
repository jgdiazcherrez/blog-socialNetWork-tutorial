<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 28/01/2018
 * Time: 17:13
 */

require_once '../vendor/autoload.php';
$facebook = new Social\Facebook();
$facebook->connect('your_app_id', 'your_app_secret');
$data = $facebook->getSharesCount(['https://google.es', 'https://marca.com', 'https://elconfidencial.com']);
var_dump($data);
die;