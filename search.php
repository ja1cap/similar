<?php

require 'vendor/autoload.php';

$similar = new \Weasty\Similar\Similar();
$query = $argv[1];
echo json_encode($similar->search($query));