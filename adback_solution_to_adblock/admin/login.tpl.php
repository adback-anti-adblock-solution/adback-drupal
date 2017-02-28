<?php

/**
 * @file Login.tpl.php
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link https://www.adback.co
 * @since 1.0.0
 *
 * @package Ad_Back
 * @subpackage Ad_Back/admin/partials
 */
?>
<h1><?php echo t('AdBack'); ?></h1>

<div id="ab-login">
    <div class="ab-col-md-12">
        <div class="ab-login-box">
            <h2><?php echo t('Register with AdBack Account'); ?></h2>
            <center><a href="https://www.adback.co" target="_blank"><div class="ab-login-logo" style="background-image:url('<?php echo $GLOBALS['base_url'] . '/' . drupal_get_path('module', 'adback_solution_to_adblock'); ?>/admin/images/_dback.png');"></div></a></center>
            <span class="ab-login-envato-desc"><?php echo t('If you are a subscriber of AdBack.co, The usage of the plugins is free, click below to identify yourself:'); ?></span>
            <center>
                <button class="button button-primary" id="ab-login-adback" style="width:100%;margin-top: 30px;"><?php echo t('Activate my AdBack account'); ?></button>
            </center>
            <center>
                <button
                    class="button button-primary"
                    id="ab-register-adback"
                    style="width:100%;margin-top: 30px;"
                    data-site-url="<?php echo $GLOBALS['base_url'] ?>"
                    data-email="<?php echo check_plain(variable_get('site_mail', ini_get('sendmail_from')));?>"
                >
                    <?php echo t('Create my AdBack account'); ?>
                </button>
            </center>
        </div>
    </div>
</div>
