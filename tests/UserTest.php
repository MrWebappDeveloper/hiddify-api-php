<?php

namespace Tests;

use Intech\Tool\Helper;
use MrWebappDeveloper\HiddifyApiPhp\HiddifyApi;

class UserTest extends TestCase
{
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
    public function test_delete_user()
    {
        $api = $this->buildHiddifyApi();

        $res = $api->user()->create(
            '14165465',
            30,
            30,
        );

        Helper::debugging()->dd($res);
    }
}