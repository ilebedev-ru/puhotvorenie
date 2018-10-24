<?php

require(dirname(__FILE__).'/../../../../config/config.inc.php');
require(dirname(__FILE__).'/FormController.php');

$controller = new FormController();
echo $controller->run();