<?php

/**
 * @file
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link https://www.adback.co
 * @since 1.0.0
 *
 * @package AdBack
 * @subpackage AdBack/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="ab-settings">
    <div id="ab-full-app">
        <grid>
            <div col="5/6">

                <div id="ab-configuration-form"></div>
                <!--                <div class="ab-primary-setting">-->
                <section style="background-color:transparent;">
                    <h4 class="header-section"><?php echo t('Adback Account'); ?></h4>
                    <hr/>
                    <div class="section-content">
                        <button id="ab-logout" primary m-full><?php echo t('Log out'); ?></button>
                    </div>
                </section>
                <!--                </div>-->
            </div>
            <div col="1/6">
                <div id="adb-sidebar-standalone"
                     data-reviewlink="https://wordpress.org/support/plugin/adback-solution-to-adblock/reviews/"
                     data-supportlink="https://wordpress.org/support/plugin/adback-solution-to-adblock">
                </div>
            </div>
        </grid>
    </div>
</div>
