<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.pressmates.net
 * @since      1.0.0
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/admin/partials
 */
?>

<form action="options.php" method="post">
    <?php
    settings_fields( $this->plugin_name );
    do_settings_sections( $this->plugin_name );
    submit_button();
    ?>
</form>