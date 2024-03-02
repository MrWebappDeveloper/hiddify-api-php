<?php

namespace Tests\Traits;

use Faker\Factory;
use Faker\Generator;

trait WithFaker
{
    /**
     * Faker generator instance
     *
     * @var Generator
     */
    public Generator $faker;

    /**
     * Load faker in setup test
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFaker();
    }

    /**
     * Load faker factory
     *
     * @return void
     */
    public function loadFaker():void
    {
        $this->faker = Factory::create();
    }
}