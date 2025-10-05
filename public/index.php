<?php

use App\Kernel;

$_ENV['HTTP_X_FORWARDED_PROTO'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$_SERVER['APP_RUNTIME_OPTIONS'] = ['disable_dotenv' => false];

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return fn (array $context) => new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
