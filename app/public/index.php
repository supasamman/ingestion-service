<?php

use App\Kernel;

require_once dirname(path: __DIR__).'/vendor/autoload_runtime.php';

return fn(array $context) => new Kernel(environment: $context['APP_ENV'], debug: (bool) $context['APP_DEBUG']);
