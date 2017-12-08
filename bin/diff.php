<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Subito\Models\SubitoDateModel;

$totalArguments = count($argv);

if (count($argv) === 3) {
    $subitoDate = new SubitoDateModel($argv[1], $argv[2]);
    var_dump($subitoDate->diff());
} else {
    echo "----------\n";
    echo "WRONG USAGE:\n";
    echo "Use Example: php bin/diff.php YYYY/MM/DD YYYY/MM/DD\n";
    echo "----------\n";
}
