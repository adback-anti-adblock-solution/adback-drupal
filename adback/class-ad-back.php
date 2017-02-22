<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.adback.co
 * @since      1.0.0
 *
 * @package    AdBack
 * @subpackage AdBBack
 */
class AdBack_Generic
{
    public function getContents($url)
    {
        if (function_exists('curl_version')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($curl);
            curl_close($curl);
            return $data;
        } else {
            return @file_get_contents($url);
        }
    }

    public function postContents($url, $fields, $header = array())
    {
        $fields_string = '';
        $header[] = 'Content-Type: application/x-www-form-urlencoded';

        if (function_exists('curl_version')) {
            if (is_array($fields)) {
                //url-ify the data for the POST
                foreach ($fields as $key => $value) {
                    $fields_string .= $key . '=' . urlencode($value) . '&';
                }
                rtrim($fields_string, '&');
            }

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            if (is_array($fields)) {
                curl_setopt($ch, CURLOPT_POST, count($fields));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);
        } else {
            $options = array(
                'http' => array(
                    'header' => implode("\r\n", $header),
                    'method' => 'POST',
                    'content' => is_array($fields) ? http_build_query($fields) : $fields
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
        }

        return $result;
    }

    public function getMyInfo()
    {
        //global $wpdb; // this is how you get access to the database

        //$table_name = $wpdb->prefix . 'adback_myinfo';
        //$myinfo = $wpdb->get_row("SELECT * FROM " . $table_name . " WHERE id = 1");
        $myinfo = variable_get('adback_myinfo', '');
        $update_time = variable_get('adback_update_time', 0);

        if ($myinfo == "" || strtotime($update_time) < (time() - 86400)) {
            $mysite = $this->askScripts();

//            $wpdb->update(
//                $table_name,
//                array(
//                    'myinfo' => json_encode($mysite),
//                    'update_time' => current_time('mysql', 1)
//                ),
//                array("id" => 1)
//            );

        } else if ($myinfo != "") {
            $mysite = json_decode($myinfo, true);
        }

        return $mysite;
    }

    public function getCacheMessages()
    {
        //global $wpdb; // this is how you get access to the database

        //$table_name = $wpdb->prefix . 'adback_message';
        //return stripslashes_deep($wpdb->get_row("SELECT * FROM " . $table_name . " WHERE id = 1"));

        //return variable_get('adback_message', '');
        return '';
    }

    public function saveCacheMessage($display, $message, $header_text, $close_text, $link = "")
    {
        //global $wpdb; // this is how you get access to the database

        $fields = array(
            "message" => $message,
            "header_text" => $header_text,
            "close_text" => $close_text,
            "display" => filter_var($display, FILTER_VALIDATE_BOOLEAN),
            "update_time" => current_time('mysql', 1)
        );

        if ($link != "") {
            $fields['link'] = $link;
        }

        //variable_set('adback_message', )

        /*$table_name = $wpdb->prefix . 'adback_message';
        $wpdb->update(
            $table_name,
            $fields,
            array("id" => 1)
        );*/
    }

    public function getMessages()
    {
        $token = $this->getToken();
        $url = "https://www.adback.co/api/custom-message?access_token=" . $token->access_token;

        $message = $this->getCacheMessages();

        $result = $this->getContents($url);
        $result = json_decode($result, true);
        $result['display'] = $message->display;

        $this->saveCacheMessage($message->display, $result['custom_messages'][0]['message'], $result['custom_messages'][0]['header_text'], $result['custom_messages'][0]['close_text'], $result['custom_messages'][0]['links']['_self']);

        return stripslashes_deep($result);
    }

    public function saveMessage($display, $message, $header_text, $close_text)
    {
        $mess = $this->getCacheMessages();

        $token = $this->getToken();
        $url = $mess->link . "?access_token=" . $token->access_token;

        $fields = array(
            "message" => $message,
            "header_text" => $header_text,
            "close_text" => $close_text,
        );

        $headers = array(
            "Content-Type: application/json"
        );

        $this->postContents($url, json_encode($fields), $headers);

        $this->saveCacheMessage($display, $message, $header_text, $close_text);

        return true;
    }

    public function isConnected($token = null)
    {
        if ($token == null) {
            $token = $this->getToken();
        }

        if (is_array($token)) {
            $token = (object)$token;
        }

        if (isset($token->access_token)) {
            $url = "https://www.adback.co/api/test/normal?access_token=" . $token->access_token;
            $result = json_decode($this->getContents($url), true);
            return is_array($result) && array_key_exists("name", $result);
        } else {
            return false;
        }
    }

    public function getToken()
    {
        //global $wpdb; // this is how you get access to the database

        //$table_name = $wpdb->prefix . 'adback_token';
        //$token = $wpdb->get_row("SELECT * FROM " . $table_name . " WHERE id = 1");

        return (object)['access_token' => variable_get('adback_access_token', ''), 'refresh_token'=> variable_get('adback_refresh_token', '')];
    }

    public function askToken()
    {
        //global $wpdb; // this is how you get access to the database

        $url = "https://www.adback.co/oauth/access_token?grant_type=bearer";
        $fields = array();
        $headers = array();

        //$table_name = $wpdb->prefix . 'adback_account';

        //$auth = $wpdb->get_row("SELECT * FROM " . $table_name . " WHERE id = 1");

        if ($auth->key != "" && $auth->secret != "") {
            $headers[] = "Authorization: Basic " . base64_encode($auth->key . ":" . $auth->secret);

            $result = json_decode($this->postContents($url, $fields, $headers), true);

            $this->saveToken($result);

            return $result;
        }
        return null;
    }

    public function saveToken($token)
    {
        //global $wpdb; // this is how you get access to the database

        if ($token == null || array_key_exists("error", $token)) {
            return;
        }

        variable_set('adback_access_token', $token["access_token"]);
        variable_set('adback_refresh_token', $token["refresh_token"]);

        //$table_name = $wpdb->prefix . 'adback_token';
        /*$wpdb->update(
            $table_name,
            array(
                "access_token" => $token["access_token"],
                "refresh_token" => $token["refresh_token"]
            ),
            array("id" => 1)
        );*/

        $this->notifyInstallation($token["access_token"]);
    }

    public function notifyInstallation($accessToken)
    {
        $notifyUrl = 'https://www.adback.co/api/plugin-activate/wordpress?access_token=' . $accessToken;

        $this->getContents($notifyUrl);
    }

    public function askDomain()
    {
        $jsonDomain = $this->getContents("https://www.adback.co/api/script/me?access_token=" . $this->getToken()->access_token);
        $result = json_decode($jsonDomain, true);
        if (isset($result['analytics_domain'])) {
            $this->saveDomain($result['analytics_domain']);
        }
    }

    public function askScripts()
    {
        $jsonScripts = $this->getContents("https://www.adback.co/api/script/me?access_token=" . $this->getToken()->access_token);
        $result = json_decode($jsonScripts, true);

        return $result;
    }

    public function saveDomain($domain)
    {
        //global $wpdb; // this is how you get access to the database

        /*$table_name = $wpdb->prefix . 'adback_myinfo';
        $wpdb->update(
            $table_name,
            array(
                'domain' => $domain,
                'update_time' => current_time('mysql', 1)
            ),
            array("id" => 1)
        );*/

        variable_set('adback_domain', $domain);
    }

    public function getDomain()
    {
        /*global $wpdb; // this is how you get access to the database

        $table_name = $wpdb->prefix . 'adback_myinfo';
        $myinfo = $wpdb->get_row("SELECT domain FROM " . $table_name . " WHERE id = 1");*/
        return variable_get('adback_domain', '');
//        return $myinfo->domain;
    }

    public function logout()
    {
        variable_del('adback_domain');

        variable_del('adback_access_token');
        variable_del('adback_refresh_token');

    }
}
