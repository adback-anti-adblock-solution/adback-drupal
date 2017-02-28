<?php

/**
 * @file AdbackSolutionToAdblockConnector.php
 *
 * @class AdbackSolutionToAdblockConnector
 * Class AdbackSolutionToAdblockConnector
 *
 * @package AdbackSolutionToAdblock
 * @subpackage AdbackSolutionToAdblock
 */

/**
 *
 */
class AdbackSolutionToAdblockConnector {
  const ADBACK_BASE = 'https://www.adback.co/api/';

  protected $token;

  /**
   * AdbackSolutionToAdblockConnector constructor.
   *
   * @param string $token
   */
  public function __construct($token = NULL) {
    $this->token = $token;
  }

  /**
   * @param $endpoint
   * @return string
   */
  protected function getUrl($endpoint) {
    return self::ADBACK_BASE . $endpoint . '?access_token=' . $this->token;
  }

  /**
   * @param $endpoint
   * @param string $format
   * @return mixed
   */
  public function get($endpoint, $format = NULL) {
    if ($this->token === NULL) {
      return FALSE;
    }

    $url = $this->getUrl($endpoint);
    if (function_exists('curl_version')) {
      $curl = curl_init($url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
      $data = curl_exec($curl);
      curl_close($curl);
    }
    else {
      $data = @file_get_contents($url);
    }

    if ($format == 'json') {
      try {
        $data = json_decode($data, TRUE);
      }
      catch (\Exception $exception) {
      }
    }

    return $data;
  }

  /**
   * @param $endpoint
   * @param $fields
   * @param array $header
   * @return bool|mixed|string
   */
  public function post($endpoint, $fields, $header = array()) {
    if ($this->token === NULL) {
      return FALSE;
    }

    $fields_string = '';
    $header[] = 'Content-Type: application/x-www-form-urlencoded';
    $url = $this->getUrl($endpoint);
    if (function_exists('curl_version')) {
      if (is_array($fields)) {
        // url-ify the data for the POST.
        foreach ($fields as $key => $value) {
          $fields_string .= $key . '=' . urlencode($value) . '&';
        }
        rtrim($fields_string, '&');
      }

      // Open connection.
      $ch = curl_init();

      // Set the url, number of POST vars, POST data.
      curl_setopt($ch, CURLOPT_URL, $url);
      if (is_array($fields)) {
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      }
      else {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      }
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

      // Execute post.
      $result = curl_exec($ch);

      // Close connection.
      curl_close($ch);
    }
    else {
      $options = array(
        'http' => array(
          'header' => implode("\r\n", $header),
          'method' => 'POST',
          'content' => is_array($fields) ? http_build_query($fields) : $fields,
        ),
      );
      $context = stream_context_create($options);
      $result = file_get_contents($url, FALSE, $context);
    }

    return $result;
  }

  /**
   * @param null $token
   */
  public function setToken($token) {
    $this->token = $token;
  }

}
