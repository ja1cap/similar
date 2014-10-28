<?php
require 'vendor/autoload.php';

$similar = new \Weasty\Similar\Similar(__DIR__ . '/data/main.txt');
$similar->groupSimilar();
