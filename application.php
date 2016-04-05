#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Myjar\QueueTestApp\Tasks\QueueTask;

$task = new QueueTask();
$task->run();
