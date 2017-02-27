<?php

class AdbackConnector
{
    const ADBACK_BASE = 'https://www.adback.co/api/';

    protected $token;

    public function __construct($token = null)
    {
        $this->token = $token;
    }

    protected function getUrl($endpoint)
    {
        return self::ADBACK_BASE . $endpoint . '?access_token='.$this->token;
    }

    public function get($endpoint, $format = null)
    {
        if ($this->token === null) {
            return false;
        }

        $url = $this->getUrl($endpoint);
        if (function_exists('curl_version')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($curl);
            curl_close($curl);
        } else {
            $data = @file_get_contents($url);
        }

        if ($format == 'json') {
            try{
                $data = json_decode($data, true);
            } catch (\Exception $exception){}
        }

        return $data;
    }

    public function post($endpoint, $fields, $header = array())
    {
        if ($this->token === null) {
            return false;
        }

        $fields_string = '';
        $header[] = 'Content-Type: application/x-www-form-urlencoded';
        $url = $this->getUrl($endpoint);
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

    /**
     * @param null $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}
