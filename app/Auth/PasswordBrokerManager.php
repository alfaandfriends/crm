<?php

namespace App\Auth;

use Illuminate\Support\Str;
use Illuminate\Auth\Passwords\PasswordBrokerManager as PasswordBrokerManagerBase;

class PasswordBrokerManager extends PasswordBrokerManagerBase
{
    protected function createTokenRepository(array $config)
    {
        $key = $this->app['config']['app.key'];
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        $connection = $config['connection'] ?? null;

        // Import or use fully qualified namespace for DatabaseTokenRepository
        // use App\Auth\DatabaseTokenRepository;
        // or
        // return new \App\Auth\DatabaseTokenRepository(
        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire']
        );
    }
}