<?php
/**
 * Plugin Name: Auto Post to Facebook Page
 * Description: Automatically shares WordPress posts to your Facebook page.
 * Version: 1.0
 * Author: Shahir Islam
 * GitHub: https://github.com/shahirislam/wp-fb-auto-poster/
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add action to publish_post hook
add_action('publish_post', 'auto_post_to_facebook', 10, 2);

function auto_post_to_facebook($ID, $post) {
    // Facebook Page Access Token
    $access_token = 'YOUR_ACCESS_TOKEN';

    // Facebook Page ID
    $page_id = 'YOUR_PAGE_ID';

    // Post details
    $post_title = get_the_title($ID);
    $post_url = get_permalink($ID);
    $message = "$post_title \nRead more: $post_url";

    // Facebook Graph API URL
    $url = "https://graph.facebook.com/{$page_id}/feed";

    // Prepare data
    $data = [
        'message' => $message,
        'access_token' => $access_token
    ];

    // Send request to Facebook
    $response = wp_remote_post($url, [
        'body' => $data
    ]);

    // Log errors if needed
    if (is_wp_error($response)) {
        error_log('Error posting to Facebook: ' . $response->get_error_message());
    }
}
