<?php

namespace Config;

class Sentry
{
    public $dsn = '';
    public $environment = 'development';
    public $tracesSampleRate = 1.0;
    public $enableStacktrace = true;

    public function __construct()
    {
        $this->dsn = getenv('SENTRY_DSN');
        $this->environment = getenv('SENTRY_ENVIRONMENT') ?? 'development';
    }
}