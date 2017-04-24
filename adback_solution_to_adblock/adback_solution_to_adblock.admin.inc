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

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  adback_solution_to_adblock_add_common_assets($form);

  if (!$adback->isConnected()) {
    return $form;
  }

  $adback->updateMessageLocal();

  $form['title'] = array(
    '#markup' => t('AdBack Account'),
    '#prefix' => '<h3>',
    '#suffix' => '</h3>',
  );

  $form['container']['logout'] = array(
    '#markup' => '<button id="ab-logout" class="button button-primary">' . t('Log out') . '</button>',
  );

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

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  adback_solution_to_adblock_add_common_assets($form);

  if (!$adback->isConnected()) {
    return $form;
  }

  $adback->updateMessageLocal();

    $form['container'] = array(
        '#type' => 'container',
        '#attributes' => array(
            'class' => array('ab-discover'),
        ),
    );

  $form['container']['title'] = array(
    '#markup' => t('1. Activate your message'),
    '#prefix' => '<h3>',
    '#suffix' => '</h3>',
  );

  $form['container']['settings']['activation'] = array(
    '#type' => 'checkbox',
    '#title' => t('Activate your message'),
    '#default_value' => variable_get('adback_solution_to_adblock_message_activation', FALSE),
  );

  $form['container']['settings']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#submit' => array('adback_solution_to_adblock_admin_message_submit'),
  );

  $form['container']['second_title'] = array(
    '#markup' => t('2. Customize your message'),
    '#prefix' => '<h3>',
    '#suffix' => '</h3>',
  );

  $form['container']['text'] = array(
    '#markup' => '<p>' . t('Click below to personalize the text and colors of your message, select a template, a theme, add your logo and choose which alternative solutions you want to propose to your adblocker users: deactivation, video, survey…') . '</p>',
  );

  $form['container']['more'] = array(
    '#type' => 'link',
    '#title' => t('Customize my message on AdBack.co'),
    '#href' => 'https://www.adback.co/en/monitoring/custom',
    '#prefix' => '<span>',
    '#suffix' => '</span>',
    '#attributes' => array(
      'class' => array(
        'button',
        'button-primary',
        'button-ab',
      ),
    ),
  );

  $form['container']['last'] = array(
    '#markup' => '<div><a href="https://www.adback.co/en/monitoring/custom" target="_blank">' . theme(
      'image',
      array(
        'path' => $GLOBALS['base_url'] . '/' . drupal_get_path('module', 'adback_solution_to_adblock') . '/admin/images/custom_message.png',
        'attributes' => array(
          'style' => 'max-width: 710px;width: 100%;',
        )
      )
    ) . '</a></div>',
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
 * @param array $form
 *   The Form.
 * @param array $form_state
 *   The Form state.
 */
function adback_solution_to_adblock_admin_message_submit(array $form, array &$form_state) {

  variable_set(
    'adback_solution_to_adblock_message_activation',
    $form_state['values']['activation']
  );

  $adback = AdbackSolutionToAdblockGeneric::getInstance();

  $adback->updateMessageDisplay((bool) $form_state['values']['activation']);

  drupal_set_message(t('Your configuration has been saved.'));
}
