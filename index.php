<?php
require 'vendor/autoload.php';

$similar = new \Weasty\Similar\Similar();

$haystackFilePath = __DIR__ . '/data/main.txt';
$similar->buildSimilar($haystackFilePath);

$haystackFileContent = @file_get_contents($haystackFilePath);
$haystack = preg_split('/\r\n|\r|\n/', $haystackFileContent);

$count = 1;
foreach($haystack as $query){

    $similarities = $similar->search($query);
    if($similarities){
        $count++;
        echo sprintf('%s - %s - %s', $query, count($similarities), $count) . PHP_EOL;
    }

}