<?php

/**
 * @file
 */

require_once 'AdbackSolutionToAdblockConnector.php';

/**
 * @file AdbackSolutionToAdblockApi.
 *
 * @class AdbackSolutionToAdblockApi
 *
 * Class AdbackSolutionToAdblockApi.
 */
class AdbackSolutionToAdblockApi {
  protected $connector;

  /**
   * AdbackSolutionToAdblockApi constructor.
   *
   * @param string $token
   */
  public function __construct($token = NULL) {
    $this->connector = new AdbackSolutionToAdblockConnector($token);
  }

  /**
   * @return mixed
   */
  public function getDomain() {
    $result = $this->connector->get("script/me", 'json');
    if (isset($result['analytics_domain'])) {
      $result = $result['analytics_domain'];
    }
    return $result;
  }

  /**
   * @return mixed
   */
  public function getScripts() {
    $result = $this->connector->get("script/me", TRUE);

    return $result;
  }

  /**
   * Hit plugin activate.
   */
  public function pluginActivate() {
    $this->connector->get('plugin-activate/drupal');
  }

  /**
   * @return bool
   */
  public function isConnected() {
    $url = "test/normal";
    $result = $this->connector->get($url, 'json');
    return is_array($result) && array_key_exists("name", $result);
  }

  /**
   * @return mixed
   */
  public function getMessages() {
    $url = "custom-message";
    $result = $this->connector->get($url, 'json');
    return $result;
  }

  /**
   * @param $message
   * @param $id
   * @return mixed
   */
  public function setMessage($message, $id) {
    $headers = array(
      "Content-Type: application/json",
    );

    $url = "custom-message/" . $id;

    return $this->connector->post($url, json_encode($message), $headers);
  }

  /**
   * @param string $token
   */
  public function setToken($token) {
    $this->connector->setToken($token);
  }

}
