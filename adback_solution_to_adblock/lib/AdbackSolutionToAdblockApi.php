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
    $result = $this->connector->get("api/script/me", 'json');
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
    $result = $this->connector->get("api/script/me", TRUE);

    return $result;
  }

  /**
   * Hit plugin activate.
   */
  public function pluginActivate() {
    $this->connector->get('api/plugin-activate/drupal');
  }

  /**
   * Check if the module is connected.
   *
   * @return bool
   *   Return if the user is connected and authentificated.
   */
  public function isConnected() {
    $url = "api/test/normal";
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
    $url = "api/custom-message";
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
    $url = "api/custom-message/" . $id;

    return $this->connector->postWithToken($url, $message);
  }

  /**
   * Send the settings of the custom message.
   *
   * @param array $message
   *   Custom message settings.
   *
   * @return mixed
   *   Return the code of the request
   */
  public function setMessageDisplay(array $message) {
    $url = "api/custom-message/update-status";

    return $this->connector->postWithToken($url, $message);
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

  public function registerUser($email, $website) {
    $fields = [
        'email' => $email,
        'website' => $website,
    ];

    $response = $this->connector->post('tokenoauth/register/en', $fields);
    $data = json_decode($response, true);

    $refreshToken = '';
    if (array_key_exists('refresh_token', $data)) {
        $refreshToken = $data['refresh_token'];
    }
    if (array_key_exists('access_token', $data)) {
      $accessToken = $data['access_token'];
      $adback = AdbackSolutionToAdblockGeneric::getInstance();

      $adback->saveToken([
          'access_token' => $accessToken,
          'refresh_token' => $refreshToken,
      ]);
      $this->connector->setToken($accessToken);
    }

    if ('' == variable_get('adback_solution_to_adblock_access_token', '')) {
      drupal_set_message(t('An error occurred during your AdBack plugin activation. Please go to the <a href="/admin/config/adback/settings">configuration</a> page to complete your installation'), 'error');
    }
  }
}
