#!/usr/bin/env php
<?php

date_default_timezone_set('America/Sao_Paulo');

require __DIR__.'/vendor/autoload.php';

use Myjar\QueueTestApp\Tasks\QueueTask;

$task = new QueueTask();
$task->run();
