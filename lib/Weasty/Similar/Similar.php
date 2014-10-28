<?php
namespace Weasty\Similar;
use Weasty\Similar\Finder\SimilarFinder;

/**
 * Class Similar
 * @package Weasty\Similar
 */
class Similar {

    /**
     * @var string
     */
    protected $haystackFilePath;

    function __construct($haystackFilePath)
    {
        $this->haystackFilePath = $haystackFilePath;
    }

    public function groupSimilar(){

        $haystackFileContent = @file_get_contents($this->haystackFilePath);
        $haystack = $haystack = preg_split('/\r\n|\r|\n/', $haystackFileContent);

        $finder = new SimilarFinder($haystack);

        $i = 0;
        foreach($haystack as $value){

            $i++;

            $similarities = $finder->find($value);

            echo $value . PHP_EOL;
            var_dump($similarities);
            echo '----------' . PHP_EOL;

            if($i == 3){
                break;
            }

        }

    }

} 