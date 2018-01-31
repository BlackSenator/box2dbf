#!/usr/bin/env php
<?php

namespace BlackSenator;

use Symfony\Component\Console\Application;

require_once('vendor/autoload.php');

$app = new Application('Fritz!Box to Fritz!adr converter');

$app->addCommands(array(
	new RunCommand()
));

$app->run();