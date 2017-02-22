<?php


/**
 * Form builder to show account settings and website setting
 *
 * @ingroup forms
 * @see tagcommander_admin_settings_submit()
 */
function adback_admin_settings($form)
{

}


function connectAdback ($form, $other)
{
    require_once('class-ad-back.php');

    $adback = new AdBack_Generic();

    if (isset($_GET['access_token'])) {
        var_dump($form, $other);
        $adback->saveToken([
            'access_token' => $_GET['access_token'],
            'refresh_token' => '',
        ]);

        drupal_goto('admin/config/adback/statistics');
    }
}

function adback_admin_statistics($form, $other)
{
    // Administrative role option.

//    $form['submit'] = array('#type' => 'button', '#value' => t('Login'), '#submit' => array('adback_admin_settings_submit'));

    connectAdback($form, $other);
    // Get the path to the module
    $path = drupal_get_path('module', 'adback');
    // Attach the CSS and JS to the form
    $form['#attached'] = array
    (
        'css' => array(
            array
            (
                'type' => 'file',
                'data' => $path . '/admin/css/ab-admin.css',
            ),
            array
            (
                'type' => 'file',
                'data' => $path . '/admin/css/sweetalert2.min.css',
            ),
        ),
        'js' => array(
            array
            (
                'type' => 'file',
                'data' => $path . '/admin/js/ab-admin.js',
            ),
            array
            (
                'type' => 'file',
                'data' => $path . '/admin/js/sweetalert2.min.js',
            ),
        ),
    );

    require_once('class-ad-back.php');

    $adback = new AdBack_Generic();

    if ($adback->isConnected()) {

        if($adback->getDomain() == '') {
            $adback->askDomain();
        }
        $form['#attached']['js'][] = array(
            'type' => 'external',
            'data' => 'https://'. $adback->getDomain() .'/lib/ab.min.js'
        );


        $form['#attached']['js'][] = array(
            'type' => 'inline',
            'data' => 'window.onload = function() {
        if(typeof adbackjs === \'object\') {
            adbackjs.init({
                token: \''.$adback->getToken()->access_token.'\',
                url: \'https://'.$adback->getDomain(). '/api/\',
                language: \'fr-FR\'
            });
        }
    }'
        );

//        $form['access_token'] = $adback->getToken()->access_token;
//        $form['domain'] = $adback->getDomain();
    }

/*
    try{
        if (TagCommander::getInstance()->checkAccount()) {

            $form['website'] = array(
                '#type' => 'container',
                '#title' => t('Website'),
                '#attributes' => array('class' => array('admin-panel'))
            );

            $form['website']['title'] = array(
                '#markup' => t('Website'),
                '#prefix' => '<h3>',
                '#suffix' => '</h3><hr/>'
            );

            $options = array();
            $sites = TagCommander::getInstance()->getSites();
            foreach ($sites as $site) {
                $options[$site['id_site']] = utf8_encode($site['name']);
            }
            $form['website']['website_select'] = array(
                '#type' => 'select',
                '#title' => t('Website'),
                '#default_value' => variable_get('tagcommander_website_id', 000),
                '#options' => $options,
                '#maxlength' => 180,
                '#empty_option' => t('Select a website')
            );

            $form['website']['submit'] = array('#type' => 'submit', '#value' => t('Save'), '#submit' => array('tagcommander_admin_settings_submit'));
        }
    }catch(Exception $e)
    {
        drupal_set_message(t('Login or Password are invalid'), 'warning');
    }*/

//var_dump(theme('adback_login'));die;

//return theme('adback_login');

    return $form;
}

function adback_admin_logout()
{
    require_once('class-ad-back.php');

    $adback = new AdBack_Generic();

    $adback->logout();

    drupal_json_output(array('done' => true));
}