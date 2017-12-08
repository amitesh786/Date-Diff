<?php

// Generated by './vendor/bin/php-generate-autoload' 'src/autoload.php'

\spl_autoload_register(function ($class) {
  static $map = array (
  'Subito\\Interfaces\\SubitoDateInterface' => 'Subito/Interfaces/SubitoDateInterface.php',
  'Subito\\Models\\SubitoDate' => 'Subito/Models/SubitoDate.php',
  'Subito\\Models\\SubitoDateModel' => 'Subito/Models/SubitoDateModel.php',
  'Subito\\Models\\SubitoMonths' => 'Subito/Models/SubitoMonths.php',
  'Subito\\Tests\\Models\\SubitoDateModelTest' => 'Subito/Tests/Models/SubitoDateModelTest.php',
);

  if (isset($map[$class])) {
    require_once __DIR__ . '/' . $map[$class];
  }
}, true, false);


