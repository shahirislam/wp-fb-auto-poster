<?php
/**
 * Plugin Name: Auto Post to Facebook Page
 * Description: Automatically shares WordPress posts to your Facebook page.
 * Version: 3.0.0
 * Author: Shahir Islam
 * Author URI: https://github.com/shahirislam
 * License: GPL2
 */

// Function to handle redirection after activation
function shahir_redirect_after_activation() {
    if (get_option('shahir_plugin_activated') != 'yes') {
        update_option('shahir_plugin_activated', 'yes');
        wp_safe_redirect(admin_url('admin.php?page=shahir_auto_post_setup'));
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
        wp_safe_redirect(admin_url('admin.php?page=shahir_auto_post_setup'));
        exit;
    }
}
add_action('admin_init', 'shahir_admin_redirect');

// Add a Settings link to the plugin action links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');

function my_plugin_action_links($links) {
    $settings_link = '<a href="admin.php?page=shahir_auto_post_settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

// Part 2

// Function to create setup page
function shahir_auto_post_setup_page() {
    ?>
    <div class="wrap">
        <h1>Facebook Auto Post Setup</h1>
        <h2>Step 1: Enter Facebook App Details</h2>
        <form method="post" action="options.php">
            <?php settings_fields('shahir_facebook_config'); ?>
            <?php do_settings_sections('shahir_facebook_config'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Facebook App ID</th>
                    <td><input type="text" name="shahir_facebook_app_id" value="<?php echo esc_attr(get_option('shahir_facebook_app_id')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook App Secret</th>
                    <td><input type="text" name="shahir_facebook_app_secret" value="<?php echo esc_attr(get_option('shahir_facebook_app_secret')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Long-Lived Access Token</th>
                    <td><input type="text" name="shahir_facebook_long_lived_access_token" value="<?php echo esc_attr(get_option('shahir_facebook_long_lived_access_token')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Domain Name</th>
                    <td><input type="text" name="shahir_facebook_domain_name" value="<?php echo esc_attr(get_option('shahir_facebook_domain_name')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button('Save & Continue'); ?>
        </form>

        <h2>Step 2: Connect Facebook Page</h2>
        <button id="connect-facebook-page" class="button button-primary" disabled>Connect Facebook Page</button>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const appId = "<?php echo esc_js(get_option('shahir_facebook_app_id')); ?>";
                const appSecret = "<?php echo esc_js(get_option('shahir_facebook_app_secret')); ?>";
                const accessToken = "<?php echo esc_js(get_option('shahir_facebook_long_lived_access_token')); ?>";
                const domain = "<?php echo esc_js(get_option('shahir_facebook_domain_name')); ?>";

                if (appId && appSecret && accessToken && domain) {
                    document.getElementById('connect-facebook-page').disabled = false;
                }

                document.getElementById('connect-facebook-page').addEventListener('click', function() {
                    // Add your Facebook page connection logic here
                });
            });
        </script>
    </div>
    <?php
}

// Register settings for the configuration
add_action('admin_init', function() {
    register_setting('shahir_facebook_config', 'shahir_facebook_app_id');
    register_setting('shahir_facebook_config', 'shahir_facebook_app_secret');
    register_setting('shahir_facebook_config', 'shahir_facebook_long_lived_access_token');
    register_setting('shahir_facebook_config', 'shahir_facebook_domain_name');
});

// Add Overview and Setup submenu pages
add_action('admin_menu', function() {
    add_menu_page(
        'Auto Post to Facebook',
        'Facebook Auto Post',
        'manage_options',
        'shahir_auto_post_settings',
        'shahir_auto_post_settings_page'
    );
    
    // Add Overview submenu
    add_submenu_page(
        'shahir_auto_post_settings',
        'Overview',
        'Overview',
        'manage_options',
        'shahir_auto_post_overview',
        'shahir_auto_post_overview_page'
    );
    
    // Add Setup submenu
    add_submenu_page(
        'shahir_auto_post_settings',
        'Setup',
        'Setup',
        'manage_options',
        'shahir_auto_post_setup',
        'shahir_auto_post_setup_page'
    );
});

// Function for the Overview page
function shahir_auto_post_overview_page() {
    ?>
    <div class="wrap">
        <h1>Auto Post to Facebook - Overview</h1>
        <p>Welcome to the Auto Post to Facebook plugin! This plugin allows you to automatically share your WordPress posts to your Facebook page.</p>
        <h2>Author Information</h2>
        <p>Developed by: <a href="http://yourwebsite.com" target="_blank">Shahir Islam</a></p>
        <p>Version: 1.0</p>
    </div>
    <?php
}

// Part 3

// Function to create the settings page
function shahir_auto_post_settings_page() {
    $saved_page_id = get_option('shahir_facebook_page_id');
    $saved_page_access_token = get_option('shahir_facebook_access_token');
    ?>
    <div class="wrap">
        <h1>Auto Post to Facebook - Settings</h1>
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
            console.log('Document ready');
            document.getElementById('connect-facebook-page').addEventListener('click', function() {
                console.log('Connect Facebook Page button clicked');
                FB.login(function(response) {
                    console.log('FB.login response:', response);
                    if (response.authResponse) {
                        FB.api('/me/accounts', function(pages) {
                            console.log('FB.api response:', pages);
                            let pageList = '<select id="page-select">';
                            pages.data.forEach(page => {
                                pageList += `<option value="${page.id}|${page.access_token}">${page.name}</option>`;
                            });
                            pageList += '</select>';
                            document.getElementById('page-list').innerHTML = pageList + '<button id="toggle-save-delete" class="button button-primary">Save Page</button>';

                            let isPageSaved = false;

                            document.getElementById('toggle-save-delete').addEventListener('click', function() {
                                const selected = document.getElementById('page-select').value.split('|');
                                if (!isPageSaved) {
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
                                            document.getElementById('toggle-save-delete').textContent = 'Delete Page';
                                            isPageSaved = true;
                                            location.reload(); // Reload to display saved data
                                        } else {
                                            alert('Error: ' + response.data);
                                        }
                                    }).fail(function(xhr, status, error) {
                                        console.log(xhr.responseText);
                                        alert('Error: ' + error);
                                    });
                                } else {
                                    if (confirm('Are you sure you want to remove the saved page?')) {
                                        console.log('Removing page');
                                        jQuery.post(shahir_ajax.ajax_url, {
                                            action: 'shahir_remove_page',
                                            security: shahir_ajax.nonce // Add nonce for security
                                        }, function(response) {
                                            console.log(response);
                                            if (response.success) {
                                                alert(response.data);
                                                document.getElementById('toggle-save-delete').textContent = 'Save Page';
                                                isPageSaved = false;
                                                location.reload(); // Reload to update the settings page
                                            } else {
                                                alert('Error: ' + response.data);
                                            }
                                        }).fail(function(xhr, status, error) {
                                            alert('Error: ' + error);
                                        });
                                    }
                                }
                            });
                        });
                    }
                }, {scope: 'pages_manage_posts,pages_read_engagement,pages_show_list'});
            });

            const removePageButton = document.getElementById('remove-page');
            if (removePageButton) {
                removePageButton.addEventListener('click', function() {
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
            }
        });
    </script>
    <?php
}

// Function to enqueue Facebook SDK
function shahir_enqueue_facebook_sdk() {
    // Check if we are on the plugin settings page
    if (isset($_GET['page']) && $_GET['page'] === 'shahir_auto_post_settings') {
        ?>
        <script async defer crossorigin="anonymous" 
            src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v15.0&appId=1265059821236647&autoLogAppEvents=1">
        </script>
        <?php
    }
}
add_action('admin_enqueue_scripts', 'shahir_enqueue_facebook_sdk');

// Function to enqueue admin scripts
function enqueue_admin_scripts() {
    wp_enqueue_script('jquery'); // Make sure jQuery is loaded
    wp_enqueue_script('admin-js', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), null, true);
    wp_localize_script('admin-js', 'shahir_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('shahir_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

// Function to save the selected Facebook page
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

// Function to remove the saved Facebook page
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

// Function to auto-post to Facebook when a post is published
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
