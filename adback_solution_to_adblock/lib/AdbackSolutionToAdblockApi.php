<?php

/**
 * @file
 * Manager of API adback.
 *
 * The class is used for getting & setting from/to API adback.
 */

require_once 'AdbackSolutionToAdblockConnector.php';

/**
 * Class AdbackSolutionToAdblockAPI.
 *
 * @class AdbackSolutionToAdblockApi
 */
class AdbackSolutionToAdblockApi {
  protected $connector;

  /**
   * AdbackSolutionToAdblockApi constructor.
   *
   * @param string $token
   *   The token.
   */
  public function __construct($token = NULL) {
    $this->connector = new AdbackSolutionToAdblockConnector($token);
  }

  /**
   * Get all domains.
   *
   * @return mixed
   *   An json stringify with analytics domain from api
   */
  public function getDomain() {
    $result = $this->connector->get("script/me", 'json');
    if (isset($result['analytics_domain'])) {
      $result = $result['analytics_domain'];
    }
    return $result;
  }

  /**
   * Get all scripts.
   *
   * @return mixed
   *   Get all scriptname and domain from api
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
   * Check if the module is connected.
   *
   * @return bool
   *   Return if the user is connected and authentificated.
   */
  public function isConnected() {
    $url = "test/normal";
    $result = $this->connector->get($url, 'json');
    return is_array($result) && array_key_exists("name", $result);
  }

  /**
   * Get all message.
   *
   * @return mixed
   *   Return all custom message from api
   */
  public function getMessages() {
    $url = "custom-message";
    $result = $this->connector->get($url, 'json');
    return $result;
  }

  /**
   * Send the settings of the custom message.
   *
   * @param array $message
   *   Custom message settings.
   * @param string $id
   *   The id of the custom message.
   *
   * @return mixed
   *   Return the code of the request
   */
  public function setMessage(array $message, $id) {
    $headers = array(
      "Content-Type: application/json",
    );

    $url = "custom-message/" . $id;

    return $this->connector->post($url, json_encode($message), $headers);
  }

  /**
   * Set a new token.
   *
   * @param string $token
   *   The token.
   */
  public function setToken($token) {
    $this->connector->setToken($token);
  }

}
