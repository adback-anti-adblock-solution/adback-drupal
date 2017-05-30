<?php

/**
 * @file
 * Admin interfaces.
 *
 * Add admin interfaces.
 */

require_once 'lib/AdbackSolutionToAdblockGeneric.php';

/**
 * Form builder to show custom message settings.
 *
 * @param array $form
 *   It's the Form.
 *
 * @return array
 *   The form.
 */
function adback_solution_to_adblock_admin_settings(array $form) {

  adback_solution_to_adblock_connect();

  adback_solution_to_adblock_add_common_assets($form);
  adback_solution_to_adblock_display_incenteev();

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  if ($adback->isConnected()) {

    if ($adback->getDomain() == '') {
      $adback->askDomain();
    }
    $form['#attached']['js'][] = [
      'type' => 'external',
      'data' => 'https://' . $adback->getDomain() . '/lib/ab.min.js',
    ];

    $form['#attached']['js'][] = [
      'type' => 'inline',
      'data' => 'window.onload = function() {
        if(typeof adbackjs === \'object\') {
        adbackjs.init({
          token: \'' . $adback->getToken()->access_token . '\',
          url: \'https://' . $adback->getDomain() . '/api/\',
          language: \'' . $GLOBALS['language']->language . '\',
          version: 2
          });
        }
      }',
    ];
  }

  return $form;
}

/**
 * Form builder to show custom message settings.
 *
 * @param array $form
 *   It's the Form.
 *
 * @return array
 *   The form.
 */
function adback_solution_to_adblock_admin_message(array $form) {

  adback_solution_to_adblock_connect();

  adback_solution_to_adblock_add_common_assets($form);
  adback_solution_to_adblock_display_incenteev();

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  if ($adback->isConnected()) {

    if ($adback->getDomain() == '') {
      $adback->askDomain();
    }
    $form['#attached']['js'][] = [
      'type' => 'external',
      'data' => 'https://' . $adback->getDomain() . '/lib/ab.min.js',
    ];

    $form['#attached']['js'][] = [
      'type' => 'inline',
      'data' => 'window.onload = function() {
        if(typeof adbackjs === \'object\') {
        adbackjs.init({
          token: \'' . $adback->getToken()->access_token . '\',
          url: \'https://' . $adback->getDomain() . '/api/\',
          language: \'' . $GLOBALS['language']->language . '\',
          version: 2
          });
        }
      }',
    ];
  }

    return $form;
}

/**
 * Check if have access_token, and save it.
 */
function adback_solution_to_adblock_connect() {

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  if (isset($_GET['access_token'])) {
    $adback->saveToken([
      'access_token' => $_GET['access_token'],
      'refresh_token' => '',
    ]);

    drupal_goto('admin/config/adback/statistics');
  }
}

/**
 * Add common assets to form.
 *
 * @param array $form
 *   The Form.
 */
function adback_solution_to_adblock_add_common_assets(array &$form) {

  // Get the path to the module.
  $path = drupal_get_path('module', 'adback_solution_to_adblock');
  // Attach the CSS and JS to the form.
  $form['#attached'] = array(
    'css' => array(
      array(
        'type' => 'file',
        'data' => $path . '/admin/css/ab-admin.css',
      ),
    ),
    'js' => array(
      array(
        'type' => 'file',
        'data' => $path . '/admin/js/ab-admin.js',
      ),
    ),
  );
}

/**
 * Display incenteev message after plugin installation
 */
function adback_solution_to_adblock_display_incenteev() {
  $time = time();
  if ($time > variable_get('adback_solution_to_adblock_incenteev_display', $time) && false === variable_get('adback_solution_to_adblock_message_activation', false)) {
    drupal_set_message(t('It\'s time to get in touch with your adblock users, activate your adback custom message !'));
    variable_del('adback_solution_to_adblock_incenteev_display');
  }
}

/**
 * Form builder to show statistics.
 *
 * @param array $form
 *   The form.
 *
 * @return array
 *   The form.
 */
function adback_solution_to_adblock_admin_statistics(array $form) {

  adback_solution_to_adblock_connect();

  adback_solution_to_adblock_add_common_assets($form);
  adback_solution_to_adblock_display_incenteev();

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  if ($adback->isConnected()) {

    if ($adback->getDomain() == '') {
      $adback->askDomain();
    }
    $form['#attached']['js'][] = array(
      'type' => 'external',
      'data' => 'https://' . $adback->getDomain() . '/lib/ab.min.js',
    );

    $form['#attached']['js'][] = array(
      'type' => 'inline',
      'data' => 'window.onload = function() {
        if(typeof adbackjs === \'object\') {
            adbackjs.init({
                token: \'' . $adback->getToken()->access_token . '\',
                url: \'https://' . $adback->getDomain() . '/api/\',
                language: \'' . $GLOBALS['language']->language . '\',
                version: 2
            });
        }
    }',
    );
  }

  return $form;
}

/**
 * To log out adback account.
 */
function adback_solution_to_adblock_admin_logout() {

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  $adback->logout();

  drupal_json_output(array('done' => TRUE));
}
