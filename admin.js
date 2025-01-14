jQuery(document).ready(function($) {
    console.log('jQuery and admin.js loaded successfully');
    console.log('shahir_ajax object:', shahir_ajax); // Log the shahir_ajax object to check if it's defined

    $('#connect-facebook-page').on('click', function() {
        FB.login(function(response) {
            if (response.authResponse) {
                FB.api('/me/accounts', function(pages) {
                    let pageList = '<select id="page-select">';
                    pages.data.forEach(function(page) {
                        pageList += `<option value="${page.id}|${page.access_token}">${page.name}</option>`;
                    });
                    pageList += '</select>';
                    $('#page-list').html(pageList + '<button id="save-page" class="button">Save Page</button>');

                    $('#save-page').on('click', function() {
                        const selected = $('#page-select').val().split('|');
                        console.log('Saving page with data:', selected);
                        $.post(shahir_ajax.ajax_url, {
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

                    // Add the remove page button handler here
                    $('#remove-page').on('click', function() {
                        if (confirm('Are you sure you want to remove the saved page?')) {
                            console.log('Remove page button clicked');
                            $.post(shahir_ajax.ajax_url, {
                                action: 'shahir_remove_page',
                                security: shahir_ajax.nonce // Add nonce for security
                            }, function(response) {
                                console.log(response);
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
            }
        }, {scope: 'pages_manage_posts,pages_read_engagement,pages_show_list'});
    });
});

