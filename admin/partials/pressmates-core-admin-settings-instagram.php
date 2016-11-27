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
<h1>Instagram</h1>
<a href="https://instagram.com/oauth/authorize/?client_id=45aea79da8c14f1a98eab7b90ced34fa&scope=basic+public_content&redirect_uri=https://smashballoon.com/instagram-feed/instagr am-token-plugin/?return_uri=<?php echo admin_url('admin.php?page=sb-instagram-feed'); ?>&response_type=token" class="sbi_admin_btn"><?php _e('Log in and get my Access Token and User ID', 'instagram-feed'); ?></a>