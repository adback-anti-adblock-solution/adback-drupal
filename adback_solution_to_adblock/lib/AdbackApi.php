<?php

require_once('AdbackConnector.php');

class AdbackApi
{
    protected $connector;

    public function __construct($token = null)
    {
        $this->connector = new AdbackConnector($token);
    }

    public function getDomain()
    {
        $result = $this->connector->get("script/me", 'json');
        if (isset($result['analytics_domain'])) {
            $result = $result['analytics_domain'];
        }
        return $result;
    }

    public function getScripts()
    {
        $result = $this->connector->get("script/me", true);

        return $result;
    }

    public function pluginActivate()
    {
        $this->connector->get('plugin-activate/wordpress');
    }

    public function isConnected()
    {
        $url = "test/normal";
        $result = $this->connector->get($url, 'json');
        return is_array($result) && array_key_exists("name", $result);
    }

    public function getMessages()
    {
        $url = "custom-message";
        $result = $this->connector->get($url, 'json');
        return $result;
    }

    public function setMessage($message, $id)
    {
        $headers = array(
            "Content-Type: application/json"
        );

        $url = "custom-message/".$id;

        return $this->connector->post($url, json_encode($message), $headers);
    }

    /**
     * @param null $token
     */
    public function setToken($token)
    {
        $this->connector->setToken($token);
    }
}
