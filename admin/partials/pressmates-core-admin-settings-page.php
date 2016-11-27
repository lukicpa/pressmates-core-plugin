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
<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="#" class="nav-tab nav-tab-active">Custom Post Types Options</a>
        <a href="#" class="nav-tab">Instagram Feed Options</a>
    </h2>
    <?php
    printf( '<h2>%1$s</h2>', esc_html( get_admin_page_title() ) );

    require_once plugin_dir_path( __FILE__ ) . 'pressmates-core-admin-settings-cpt.php';
    require_once plugin_dir_path( __FILE__ ) . 'pressmates-core-admin-settings-instagram.php';
    ?>
</div>