<?php

require_once 'helpers/Hash.helper.php';

$pwd = HashHelper::makeHash('test', 'test', 1000);
var_dump($pwd);
