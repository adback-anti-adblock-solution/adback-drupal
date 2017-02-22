<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.adback.co
 * @since      1.0.0
 *
 * @package    AdBack
 * @subpackage AdBack/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1 class="ab-lefty"><?php echo t( 'AdBack : The stats of your AdBlock audience'); ?></h1>
<button id="ab-logout" class="button button-primary"><?php echo t('Log out'); ?></button>
<hr class="clear">

<h2><?php echo t( 'Blocked page view and percent'); ?></h2>
<h4><?php echo t( 'Blocked page view and percent - Sub'); ?></h4>
<div data-ab-graph data-ab-type="page-view-adblocker-percent" style="width: 95%; height: 400px; margin-bottom: 50px;">
</div>
<hr>


<h2><?php echo t( 'New - former adblock users'); ?></h2>
<h4><?php echo t( 'New - former adblock users - Sub'); ?></h4>
<div data-ab-graph data-ab-type="adblocker-new-old" style="width: 95%; height: 400px; margin-bottom: 50px;">
</div>
<hr>

<h2><?php echo t( 'Bounce'); ?></h2>
<div data-ab-graph data-ab-type="bounce" style="width: 95%; height: 400px; margin-bottom: 50px;">
</div>
<hr>

<h2><?php echo t( 'Browser'); ?></h2>
<div data-ab-graph data-ab-type="browser" style="width: 95%; height: 400px; margin-bottom: 50px;">
</div>
<hr>

<div class="ab-discover">
    <a href="https://www.adback.co" target="_blank" class="button button-primary button-ab"><?php echo t('Discover'); ?></a>
</div>
<?php var_dump($GLOBALS['language']);?>

