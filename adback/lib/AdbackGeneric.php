<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.adback.co
 * @since      1.0.0
 *
 * @package    AdBack
 * @subpackage AdBack
 */

require_once 'AdbackApi.php';

class AdBackGeneric
{
    private static $instance = null;

    protected $api;

    /**
     * AdBackGeneric constructor.
     */
    private function __construct()
    {
        $token = variable_get('adback_access_token', '');

        $this->api = new AdbackApi($token);
    }

    /**+
     * Use singleton pattern
     *
     * @return AdBack_Generic|null
     */
    static public function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new AdBackGeneric();
        }

        return self::$instance;
    }

    /**
     * Get info from adback site
     *
     * @return mixed|string
     */
    public function getMyInfo()
    {
        $myinfo = variable_get('adback_myinfo', '');
        $update_time = variable_get('adback_update_time', 0);

        if ($myinfo == "" || $update_time < (time() - 86400)) {
            $mysite = $this->api->getScripts();

            $this->saveDomain($mysite['analytics_domain']);

        } else if ($myinfo != "") {
            $mysite = json_decode($myinfo, true);
        }

        return $mysite;
    }

    /**
     * Update local storage with message
     */
    public function updateMessageLocal()
    {
        $message = $this->api->getMessages();

        variable_set('adback_message_header', $message['custom_messages'][0]['header_text']);
        variable_set('adback_message_text', $message['custom_messages'][0]['message']);
        variable_set('adback_message_close', $message['custom_messages'][0]['close_text']);
        $link = $message['custom_messages'][0]['links']['_self'];
        preg_match('/[0-9]+$/', $link, $elements);
        $id = $elements[0];
        variable_set('adback_message_id', $id);
    }

    /**
     * Update via adback api with message
     *
     * @param $text
     * @param $header
     * @param $close
     * @return mixed|string
     */
    public function updateMessageRemote($text, $header, $close)
    {
        $fields = array(
            "message" => $text,
            "header_text" => $header,
            "close_text" => $close,
        );

        $id = variable_get('adback_message_id', 0);
        return $this->api->setMessage($fields, $id);
    }

    /**
     * Check if the token is valid
     *
     * @param object $token
     * @return bool
     */
    public function isConnected($token = null)
    {
        if ($token == null) {
            $token = $this->getToken();
        }

        if (is_array($token)) {
            $token = (object)$token;
        }

        $this->api->setToken($token->access_token);

        return $this->api->isConnected();
    }

    /**
     * Return token object stored
     *
     * @return object
     */
    public function getToken()
    {
        return (object)['access_token' => variable_get('adback_access_token', ''), 'refresh_token'=> variable_get('adback_refresh_token', '')];
    }

    /**
     * Save tokens into db
     *
     * @param $token
     */
    public function saveToken($token)
    {
        if ($token == null || array_key_exists("error", $token)) {
            return;
        }

        variable_set('adback_access_token', $token["access_token"]);
        variable_set('adback_refresh_token', $token["refresh_token"]);

        $this->api->setToken($token["access_token"]);

        $this->api->pluginActivate();
    }

    /**
     * update the api domain
     */
    public function askDomain()
    {
        $domain = $this->api->getDomain();
        $this->saveDomain($domain);
    }

    /**
     * Get all scripts
     *
     * @return mixed|string
     */
    public function askScripts()
    {
        return $this->api->getScripts();
    }

    /**
     * Store the current domain
     *
     * @param $domain
     */
    public function saveDomain($domain)
    {
        variable_set('adback_domain', $domain);
        variable_set('adback_update_time', time());
    }

    /**
     * Get the stored domain
     *
     * @return mixed
     */
    public function getDomain()
    {
        return variable_get('adback_domain', '');
    }

    /**
     * Reset all token and domain
     */
    public function logout()
    {
        variable_del('adback_domain');

        variable_del('adback_access_token');
        variable_del('adback_refresh_token');
    }
}
