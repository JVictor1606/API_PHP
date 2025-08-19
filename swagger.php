<?php
require("vendor/autoload.php");
$openapi = \OpenApi\Generator::scan(['./Controllers']);
header('Content-Type: application/x-yaml');
echo $openapi->toYaml();