<?php

namespace MrWebappDeveloper\HiddifyApiPhp;

class User extends hiddifyApi
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
     * Create new user in the panel
     *
     * @param string $name
     * @param int $package_days
     * @param int $package_size
     * @param string|null $telegram_id
     * @param string|null $comment
     * @param string $resetMod
     * @return string|bool
     */
    public function create(
        string $name,
        int $package_days = 30,
        int $package_size = 30,
        string $telegram_id = null,
        string $comment = null,
        string $resetMod = 'no_reset'
    ): string | bool {
        $url = $this->hiddifyApi->urlAdmin . 'api/v1/user/';

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
        );

        $uuid = $this->generateRandomUUID();

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
            'uuid' => $uuid
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
     * Find and Get user data with uuid
     *
     * @param string $uuid
     * @return array
     */
    public function find(string $uuid): array
    {
        $url = $this->hiddifyApi->urlAdmin . 'api/v1/user/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($result, true);
        $userdata = $this->findElementByUuid($data, $uuid);
        $userdata['subData'] = $this->getDataFromSub($uuid);

        return $userdata;
    }
}