<?php

namespace MrWebappDeveloper\HiddifyApiPhp;

if (version_compare(phpversion(), "8.0.0", "<=")) {
    die('Use PHP 8 or later :) Stay Updated');
}

class HiddifyApi
{
    /**
     * Singleton design pattern
     *
     * @var array
     */
    private static array $singleton;

    public $urlUser, $urlAdmin;

    /**
     * Constructor
     *
     * @param string $mainUrl
     * @param string $path
     * @param string $adminSecret
     */
    function __construct(
        public string $mainUrl,
        public string $path,
        public string $adminSecret
    )
    {
        $this->urlUser = $mainUrl . '/' . $path . '/';
        $this->urlAdmin = $mainUrl . '/' . $path . '/' . $adminSecret . '/';
    }

    /**
     * Returns User abstraction
     *
     * @return User
     */
    public function user():User
    {
        if(!isset(self::$singleton[User::class]))
            self::$singleton[User::class] = new User($this);

        return self::$singleton[User::class];
    }

    /**
     * Check connection establish
     *
     * @return bool
     */
    public function is_connected(): bool
    {
        $url = $this->urlAdmin . 'admin/get_data/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);
        $retVal = (is_array($response)) ? true : false;
        return $retVal;
    }

    /**
     * Extracts panel statuses
     *
     * @return array
     */
    public function getSystemStats(): array
    {
        $url = $this->urlAdmin . 'admin/get_data/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);
        return $response['stats'];
    }

    /**
     * Helper function for generate uuid
     *
     * @return string
     */
    protected function generateRandomUUID(): string
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    /* 
    protected function getcrftoken(string $path): string
    {
        // Load the HTML content into a DOMDocument object
        $url = $this->urlAdmin . $path; // 

        $html = file_get_contents($url);
        $doc = new DOMDocument();
        $doc->loadHTML($html);

        // Create a DOMXPath object and use it to query the document for the csrf_token input field
        $xpath = new DOMXPath($doc);
        $input = $xpath->query('//input[@name="csrf_token"]')->item(0);

        // Get the value of the csrf_token input field
        $csrfToken = $input->getAttribute('value');

        // Output the value of the csrf_token input field
        return $csrfToken;
    }
    */
}


