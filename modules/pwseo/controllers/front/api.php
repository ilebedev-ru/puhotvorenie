<?php

require(dirname(__FILE__).'/../../../../config/config.inc.php');
require(dirname(__FILE__).'/ApiController.php');

$controller = new ApiController();
echo $controller->run();