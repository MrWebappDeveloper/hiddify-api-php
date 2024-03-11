<?php

namespace MrWebappDeveloper\HiddifyApiPhp;

use Intech\Tool\Helper;
use MrWebappDeveloper\HtmlQuerySelector\QuerySelector;

class User
{
    public function __construct(
        private HiddifyApi $hiddifyApi
    ){}

    /**
     * Returns list of registered users in the panel
     *
     * @return array
     */
    public function list(): array
    {
        $url = $this->hiddifyApi->urlAdmin . 'api/v1/user/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);
        return $data;
    }

    /**
     * Uses for send update and create user request
     *
     * @param string $name
     * @param int $package_days
     * @param int $package_size
     * @param string|null $uuid
     * @param string|null $telegram_id
     * @param string|null $comment
     * @param string $resetMod
     * @return string|bool
     */
    private function updateOrCreate(
        string $name,
        int $package_days = 30,
        int $package_size = 30,
        ?string $uuid = null,
        string $telegram_id = null,
        string $comment = null,
        string $resetMod = 'no_reset'
    ): string | bool
    {
        $url = $this->hiddifyApi->urlAdmin . 'api/v1/user/';

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );

        $finalUuid = $uuid ?? $this->hiddifyApi->generateRandomUUID();

        $data = array(
            'added_by_uuid' => $this->hiddifyApi->adminSecret,
            'comment' => $comment,
            'current_usage_GB' => 0,
            'last_online' => null,
            'last_reset_time' => null,
            'mode' => $resetMod,
            'name' => $name,
            'package_days' => $package_days,
            'start_date' => date('Y-m-d'),
            'telegram_id' => $telegram_id,
            'usage_limit_GB' => $package_size,
            'uuid' => $finalUuid
        );

        $data_string = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if ($result != null) {
            return $uuid;
        } else {
            return false;
        }
    }

    /**
     * Create new user in the panel
     *
     * @param string $name
     * @param int $package_days
     * @param int $package_size
     * @param string|null $uuid
     * @param string|null $telegram_id
     * @param string|null $comment
     * @param string $resetMod
     * @return string|bool
     */
    public function create(
        string $name,
        int $package_days = 30,
        int $package_size = 30,
        ?string $uuid = null,
        string $telegram_id = null,
        string $comment = null,
        string $resetMod = 'no_reset'
    ): string | bool {
        return $this->updateOrCreate($name, $package_days, $package_size, $uuid, $telegram_id, $comment, $resetMod);
    }

    /**
     * Update user in the panel
     *
     * @param string $name
     * @param int $package_days
     * @param int $package_size
     * @param string|null $uuid
     * @param string|null $telegram_id
     * @param string|null $comment
     * @param string $resetMod
     * @return string|bool
     */
    public function update(
        string $name,
        int $package_days = 30,
        int $package_size = 30,
        string $uuid = null,
        string $telegram_id = null,
        string $comment = null,
        string $resetMod = 'no_reset'
    ): string | bool {
        return $this->updateOrCreate($name, $package_days, $package_size, $uuid, $telegram_id, $comment, $resetMod);
    }

    /**
     * Find user with uuid in passed array $data argument
     *
     * @param array $data
     * @param string $uuid
     * @return array|null
     */
    private function findElementByUuid(array $data, string $uuid):array|null
    {
        foreach ($data as $value)
            if ($value['uuid'] == $uuid)
                return $value;
        return null;
    }

    /**
     * Finds data from subscription
     *
     * @param string $uuid
     * @return array
     */
    private function getDataFromSub(string $uuid): array
    {
        $url = $this->hiddifyApi->urlUser . $uuid;

        // Extract days and GB remaining
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url . '/sub/');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        $raw_data = curl_exec($ch);
        curl_close($ch);

        // Import vless & vemss & trojan servers to array
        $servers = [];
        $lines = explode("\n", $raw_data);

        foreach ($lines as $line) {
            if (strpos($line, 'vless://') === 0 || strpos($line, 'trojan://') === 0 || strpos($line, 'vemss://') === 0) {
                $servers[] = $line;
            }
        }

        return $servers;
    }

    /**
     * Find user in panel id through its uuid
     *
     * @param string $uuid
     * @return int|null
     */
    private function extractId(string $uuid):int|null
    {
        $count = null;

        $page = 1;

        while (1){
            $url = $this->hiddifyApi->urlAdmin . 'admin/user/' . ($page > 1 ? "?page=" . ($page - 1) : '');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);

            $selector = new QuerySelector($result);

            $tag = $selector->tag('tr')
                ->contains($uuid)
                ->tag('a')
                ->attribute('title', 'Edit Record')
                ->select()->item(0);

            if(!$tag){
                if (!$count)
                    $count = $this->count();

                if($count < 51 || ($page * 50) > $count)
                    return null;

                $page += 1;

                continue;
            }

            $href = $tag->getAttribute('href');

            break;
        }

        if(!$href)
            return null;

        preg_match('/\?id=(\d+)/', $href, $matches);

        return $matches[1] ?? null;
    }

    /**
     * Find and Get user data with uuid
     *
     * @param string $uuid
     * @return array|null
     */
    public function find(string $uuid): array | null
    {
        $url = $this->hiddifyApi->urlAdmin . 'api/v1/user/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);

        $userdata = $this->findElementByUuid($data, $uuid);

        if(!isset($userdata['added_by_uuid']))
            return null;

        $userdata['subData'] = $this->getDataFromSub($uuid);

        return $userdata;
    }

    /**
     * Returns count of registered users
     *
     * @return int|null
     */
    public function count():int|null
    {
        $url = $this->hiddifyApi->urlAdmin . 'admin/user/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        if(!$result)
            return null;

        $selector = new QuerySelector($result);

        $tag = $selector->tag('div')
            ->attribute('class', 'card-header')
            ->tag('span')
            ->select();

        if(!$tag)
            return null;

        $content = $tag->item(0)->textContent;

        $explode = explode(':', $content);

        return $explode[1] ?? null;
    }

    /**
     * Delete user from panel
     *
     * @param string $uuid
     * @return bool
     */
    public function delete(string $uuid):bool
    {
        $url = $this->hiddifyApi->urlAdmin . 'admin/user/delete/';

        if(!$id = $this->extractId($uuid))
            return false;

        $data = http_build_query([
            'id' => $id,
            'url' => $this->hiddifyApi->path . "/" . $this->hiddifyApi->adminSecret . "/admin/user/"
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return (bool)$httpStatusCode == 302;
    }
}