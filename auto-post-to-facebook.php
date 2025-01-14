<?php
/**
 * Plugin Name: Auto Post to Facebook Page
 * Description: Automatically shares WordPress posts to your Facebook page.
 * Version: 2.0.0
 * Author: Shahir Islam
 */

// Function to handle redirection after activation
function shahir_redirect_after_activation() {
    if (get_option('shahir_plugin_activated') != 'yes') {
        update_option('shahir_plugin_activated', 'yes');
        wp_safe_redirect(admin_url('admin.php?page=shahir_auto_post_settings'));
        exit; // Exit to prevent any further output
    }
}

register_activation_hook(__FILE__, 'shahir_plugin_activation');

function shahir_plugin_activation() {
    add_option('shahir_plugin_activated', 'yes');
}

// Function to handle the admin_init hook for redirection
function shahir_admin_redirect() {
    if (get_option('shahir_plugin_activated') == 'yes') {
        delete_option('shahir_plugin_activated');
        wp_safe_redirect(admin_url('admin.php?page=shahir_auto_post_settings'));
        exit;
    }
}
add_action('admin_init', 'shahir_admin_redirect');

// Function to create settings page

function shahir_auto_post_settings_page() {
    $saved_page_id = get_option('shahir_facebook_page_id');
    $saved_page_access_token = get_option('shahir_facebook_access_token');
    ?>
    <div class="wrap">
        <h1>Auto Post to Facebook</h1>
        <p>Click the button below to connect your Facebook account and select a page.</p>
        <button id="connect-facebook-page" class="button button-primary">Connect Facebook Page</button>
        <div id="page-list" style="margin-top: 20px;">
            <?php if ($saved_page_id && $saved_page_access_token) : ?>
                <p>Saved Page ID: <?php echo esc_html($saved_page_id); ?></p>
                <p>Saved Access Token: <?php echo esc_html($saved_page_access_token); ?></p>
                <button id="remove-page" class="button">Remove Page</button>
            <?php endif; ?>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('connect-facebook-page').addEventListener('click', function() {
                FB.login(function(response) {
                    if (response.authResponse) {
                        FB.api('/me/accounts', function(pages) {
                            let pageList = '<select id="page-select">';
                            pages.data.forEach(page => {
                                pageList += `<option value="${page.id}|${page.access_token}">${page.name}</option>`;
                            });
                            pageList += '</select>';
                            document.getElementById('page-list').innerHTML = pageList + '<button id="save-page" class="button">Save Page</button>';

                            document.getElementById('save-page').addEventListener('click', function() {
                                const selected = document.getElementById('page-select').value.split('|');
                                console.log('Saving page with data:', selected);
                                jQuery.post(shahir_ajax.ajax_url, {
                                    action: 'shahir_save_page',
                                    page_id: selected[0],
                                    access_token: selected[1],
                                    security: shahir_ajax.nonce // Add nonce for security
                                }, function(response) {
                                    console.log(response);
                                    if (response.success) {
                                        alert(response.data);
                                        location.reload(); // Reload to display saved data
                                    } else {
                                        alert('Error: ' + response.data);
                                    }
                                }).fail(function(xhr, status, error) {
                                    console.log(xhr.responseText);
                                    alert('Error: ' + error);
                                });
                            });
                        });
                    }
                }, {scope: 'pages_manage_posts,pages_read_engagement,pages_show_list'});
            });

            document.getElementById('remove-page').addEventListener('click', function() {
                if (confirm('Are you sure you want to remove the saved page?')) {
                    jQuery.post(shahir_ajax.ajax_url, {
                        action: 'shahir_remove_page',
                        security: shahir_ajax.nonce // Add nonce for security
                    }, function(response) {
                        if (response.success) {
                            alert(response.data);
                            location.reload(); // Reload to update the settings page
                        } else {
                            alert('Error: ' + response.data);
                        }
                    }).fail(function(xhr, status, error) {
                        alert('Error: ' + error);
                    });
                }
            });
        });
    </script>
    <?php
}

add_action('admin_menu', function() {
    add_menu_page(
        'Auto Post to Facebook',
        'Facebook Auto Post',
        'manage_options',
        'shahir_auto_post_settings',
        'shahir_auto_post_settings_page'
    );
});

function shahir_enqueue_facebook_sdk() {
    // Check if we are on the plugin settings page
    if (isset($_GET['page']) && $_GET['page'] === 'shahir_auto_post_settings') {
        ?>
        <script async defer crossorigin="anonymous" 
            src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v15.0&appId={APP_ID}&autoLogAppEvents=1">
        </script>
        <?php
    }
}
add_action('admin_enqueue_scripts', 'shahir_enqueue_facebook_sdk');

function enqueue_admin_scripts() {
    wp_enqueue_script('jquery'); // Make sure jQuery is loaded
    wp_enqueue_script('admin-js', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), null, true);
    wp_localize_script('admin-js', 'shahir_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('shahir_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

function shahir_save_page() {
    // Verify the nonce for security
    if (!check_ajax_referer('shahir_nonce', 'security', false)) {
        wp_send_json_error('Invalid nonce.');
        wp_die();
    }

    if (isset($_POST['page_id']) && isset($_POST['access_token'])) {
        update_option('shahir_facebook_page_id', sanitize_text_field($_POST['page_id']));
        update_option('shahir_facebook_access_token', sanitize_text_field($_POST['access_token']));
        wp_send_json_success('Page saved successfully!');
    } else {
        wp_send_json_error('Error saving the page. Please try again.');
    }
    wp_die();
}
add_action('wp_ajax_shahir_save_page', 'shahir_save_page');

function shahir_remove_page() {
    // Verify the nonce for security
    if (!check_ajax_referer('shahir_nonce', 'security', false)) {
        wp_send_json_error('Invalid nonce.');
        wp_die();
    }

    delete_option('shahir_facebook_page_id');
    delete_option('shahir_facebook_access_token');

    wp_send_json_success('Page removed successfully.');
    wp_die();
}
add_action('wp_ajax_shahir_remove_page', 'shahir_remove_page');

add_action('publish_post', 'auto_post_to_facebook', 10, 2);

function auto_post_to_facebook($ID, $post) {
    $access_token = get_option('shahir_facebook_access_token');
    $page_id = get_option('shahir_facebook_page_id');
    $message = get_the_title($ID) . "\n" . get_permalink($ID);
    $response = wp_remote_post("https://graph.facebook.com/$page_id/feed", [
        'body' => [
            'message' => $message,
            'access_token' => $access_token,
        ],
    ]);

    if (is_wp_error($response)) {
        error_log('Error posting to Facebook: ' . $response->get_error_message());
    } else {
        error_log('Successfully posted to Facebook.');
    }
}
?>
