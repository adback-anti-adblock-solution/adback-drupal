<?php

/**
 * @file
 */

require_once 'lib/AdbackSolutionToAdblockGeneric.php';

/**
 * Form builder to show custom message settings.
 *
 * @ingroup forms
 * @see adback_solution_to_adblock_admin_settings_submit()
 *
 * @param array $form
 *
 * @return array
 */
function adback_solution_to_adblock_admin_settings($form) {

  adback_solution_to_adblock_connect();

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  adback_solution_to_adblock_addCommonAssets($form);

  if (!$adback->isConnected()) {
    return $form;
  }

  $adback->updateMessageLocal();

  $form['title'] = array(
    '#markup' => t('AdBack Settings'),
    '#prefix' => '<h3>',
    '#suffix' => '</h3>',
  );

  $form['settings'] = array(
    '#type' => 'fieldset',
  );

  $form['settings']['activation'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show warning message for user with ad blocker'),
    '#default_value' => variable_get('adback_solution_to_adblock_message_activation', FALSE),
  );

  $form['settings']['header'] = array(
    '#type' => 'textfield',
    '#title' => t('Header text'),
    '#default_value' => variable_get('adback_solution_to_adblock_message_header', ''),
    '#maxlength' => 180,
  );

  $form['settings']['message'] = array(
    '#type' => 'textarea',
    '#title' => t('Text message'),
    '#default_value' => variable_get('adback_solution_to_adblock_message_text', ''),
    '#cols' => 50,
    '#rows' => 10,
  );

  $form['settings']['close'] = array(
    '#type' => 'textfield',
    '#title' => t('Text close button'),
    '#default_value' => variable_get('adback_solution_to_adblock_message_close', ''),
    '#maxlength' => 180,
  );

  $form['settings']['submit'] = array('#type' => 'submit', '#value' => t('Save'), '#submit' => array('adback_solution_to_adblock_admin_settings_submit'));

  $form['container'] = array(
    '#type' => 'container',
    '#attributes' => array(
      'class' => array('ab-discover ab-grey'),
    ),
  );

  $form['container']['text'] = array(
    '#markup' => '<p>' . t('Note that you can access more customization options on your AdBack dashboard (color, logo, template, theme, etc.)') . '</p>',
  );

  $form['container']['more'] = array(
    '#type' => 'link',
    '#title' => t('Discover more features'),
    '#href' => 'https://www.adback.co/',
    '#attributes' => array(
      'class' => array(
        'button',
        'button-primary',
        'button-ab',
      ),
    ),
  );

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
 * @param $form
 */
function adback_solution_to_adblock_addCommonAssets(&$form) {

  // Get the path to the module.
  $path = drupal_get_path('module', 'adback_solution_to_adblock');
  // Attach the CSS and JS to the form.
  $form['#attached'] = array(
    'css' => array(
      array(
        'type' => 'file',
        'data' => $path . '/admin/css/ab-admin.css',
      ),
      array(
        'type' => 'file',
        'data' => $path . '/admin/css/sweetalert2.min.css',
      ),
    ),
    'js' => array(
      array(
        'type' => 'file',
        'data' => $path . '/admin/js/ab-admin.js',
      ),
      array(
        'type' => 'file',
        'data' => $path . '/admin/js/sweetalert2.min.js',
      ),
    ),
  );
}

/**
 * Form builder to show statistics.
 *
 * @ingroup forms
 *
 * @param array $form
 *
 * @return array
 */
function adback_solution_to_adblock_admin_statistics($form) {

  adback_solution_to_adblock_connect();

  adback_solution_to_adblock_addCommonAssets($form);

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
                language: \'' . $GLOBALS['language']->language . '\'
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

/**
 * Submit handler for save fields.
 *
 * @param $form
 * @param $form_state
 */
function adback_solution_to_adblock_admin_settings_submit($form, &$form_state) {

  variable_set('adback_solution_to_adblock_message_activation', $form_state['values']['activation']);
  variable_set('adback_solution_to_adblock_message_header', $form_state['values']['header']);
  variable_set('adback_solution_to_adblock_message_text', $form_state['values']['message']);
  variable_set('adback_solution_to_adblock_message_close', $form_state['values']['close']);

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  $adback->updateMessageRemote($form_state['values']['message'], $form_state['values']['header'], $form_state['values']['close']);

  drupal_set_message(t('Your configuration has been saved.'));
}
