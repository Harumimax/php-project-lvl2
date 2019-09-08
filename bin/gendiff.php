#!/usr/bin/env php

<?php

require_once __DIR__ . '/../vendor/autoload.php';
require('../src/docopt.php');

$handler = new \Docopt\Handler(array(
    'help' => true,
    'optionsFirst' => false,
));
$handler->handle($doc, $argv);
