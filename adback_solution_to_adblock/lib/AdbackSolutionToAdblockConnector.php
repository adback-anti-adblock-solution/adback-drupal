<?php

/**
 * @file
 * Connector for adback api.
 *
 * Class AdbackSolutionToAdblockConnector connector for adback api.
 *
 * @package AdbackSolutionToAdblock
 * @subpackage AdbackSolutionToAdblock
 */

/**
 * Class AdbackSolutionToAdblockConnector.
 *
 * @class AdbackSolutionToAdblockConnector
 */
class AdbackSolutionToAdblockConnector {
  const ADBACK_BASE = 'https://www.adback.co/api/';

  protected $token;

  /**
   * AdbackSolutionToAdblockConnector constructor.
   *
   * @param string $token
   *   The token.
   */
  public function __construct($token = NULL) {
    $this->token = $token;
  }

  /**
   * Get the url.
   *
   * @param string $endpoint
   *   Api endpoint.
   *
   * @return string
   *   The url
   */
  protected function getUrl($endpoint) {
    return self::ADBACK_BASE . $endpoint . '?access_token=' . $this->token;
  }

  /**
   * Get the api's response from the endpoint and parse the response.
   *
   * @param string $endpoint
   *   The api endpoint.
   * @param string $format
   *   Return format.
   *
   * @return mixed
   *   Datas.
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
   * Post to the api endpoint all fields.
   *
   * @param string $endpoint
   *   API endpoint.
   * @param array $fields
   *   Fields of the custom message.
   * @param array $header
   *   Specify header.
   *
   * @return mixed
   *   The response of request
   */
  public function post($endpoint, array $fields, array $header = array()) {
    if ($this->token === NULL) {
      return FALSE;
    }

    $header[] = 'Content-Type: application/json';
    $url = $this->getUrl($endpoint);
    if (function_exists('curl_version')) {

      // Open connection.
      $ch = curl_init();

      // Set the url, number of POST vars, POST data.
      curl_setopt($ch, CURLOPT_URL, $url);
      if (is_array($fields)) {
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
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
          'content' => is_array($fields) ? json_encode($fields) : $fields,
        ),
      );
      $context = stream_context_create($options);
      $result = file_get_contents($url, FALSE, $context);
    }

    return $result;
  }

  /**
   * Set a new token.
   *
   * @param string $token
   *   The new token.
   */
  public function setToken($token) {
    $this->token = $token;
  }

}
