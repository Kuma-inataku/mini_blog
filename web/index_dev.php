<?php

require '../bootstap.php';
require '../MiniBlogApplication.php';

$app = new MiniBlogApplication(true);
$app->run();