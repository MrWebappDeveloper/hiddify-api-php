<?php

namespace Tests;

use Intech\Tool\Helper;
use MrWebappDeveloper\HiddifyApiPhp\HiddifyApi;
use Tests\Traits\WithFaker;

class UserTest extends TestCase
{
    use WithFaker;

    /**
     * Hiddify api service method factory
     *
     * @return HiddifyApi
     */
    public function buildHiddifyApi():HiddifyApi
    {
        return new HiddifyApi(
            $_ENV['HOST'],
            $_ENV['PANEL_PATH'],
            $_ENV['PANEL_SECRET']
        );
    }

    /**
     * @return void
     */
    public function test_create_user()
    {
        $api = $this->buildHiddifyApi();

        $user = (object)[
            'name' => $this->faker->name,
            'days' => rand(1, 365),
            'size' => rand(1, 100),
            'uuid' => $this->faker->uuid,
        ];

        $res = $api->user()->create(
            $user->name,
            $user->days,
            $user->size,
            uuid: $user->uuid
        );

        $this->assertSame($res, $user->uuid);
    }
}