<?php

$config = [];

if (PHP_VERSION_ID >= 80400) {
	$config['parameters']['ignoreErrors'] = [['identifier' => 'parameter.implicitlyNullable']];
}

return $config;
