<?php

namespace Tests\Traits;

use Dotenv\Dotenv;

trait HasEnv
{
    /**
     * Load env in setup function
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadDotEnv();
    }

    /**
     * Load env
     *
     * @return void
     */
    public function loadDotEnv(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(dirname(__DIR__)));

        $dotenv->load();
    }
}