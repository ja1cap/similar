<?php

require 'vendor/autoload.php';

error_reporting(0);

$similar = new \Weasty\Similar\Similar();

$query = $argv[1];
echo json_encode($similar->search($query));