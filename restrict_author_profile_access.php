<?php
/*
Plugin Name: Restrict Author Profile Access
Description: Restricts access to specified author profiles.
Version: 1.0
Author: soranoo (Freeman)
*/

function restrict_author_profile_access()
{
    // Check if it's an author page
    if (is_author()) {        
        // Get the current author ID
        $current_author_id = get_query_var('author');
        
        // Get the current logged in user ID
        $current_user_id = get_current_user_id();
        
        // Check if the current author is in the restricted list and the current user is not the author
        if (wprapa_is_author_restricted($current_author_id) && $current_user_id != $current_author_id) {
            // Return a 404 error
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            get_template_part(404); // You can create a custom 404 template if needed
            exit();
        }
    }
}
// Hook into the template_redirect action to check author page access
add_action('template_redirect', 'restrict_author_profile_access');



function restrict_author_profile_access_menu()
{
    add_options_page('Restrict Author Profile Access Settings', 'Restrict Author Profile Access', 'manage_options', 'restrict-author-profile-access-settings', 'restrict_author_profile_access_settings_page');
}
// Add settings page to the admin menu
add_action('admin_menu', 'restrict_author_profile_access_menu');



// handle the form submission
function restrict_author_profile_access_settings() {
    register_setting('restrict-author-profile-access-group', 'restricted_authors', function ($input) {
        // If the input is an array, convert it to a string
        if (is_array($input)) {
            // sort the array
            sort($input);

            // remove duplicate values
            $input = array_unique($input);

            return array_map('intval', $input);
        }
        return array();
    });
}
add_action('admin_init', 'restrict_author_profile_access_settings');



// Enqueue Select2
function enqueue_select2_script()
{
    wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');
    wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'), '4.0.13', true);
}
add_action('admin_enqueue_scripts', 'enqueue_select2_script');



// Add JavaScript for Select2 initialization
function initialize_select2()
{
?>
    <script>
        jQuery(document).ready(function($) {
            $('.multi-author-select').select2({
                width: '100%',
                placeholder: 'Select authors',
            });
        });
    </script>
<?php
}
add_action('admin_footer', 'initialize_select2');



function remove_author()
{
    // Check if the author ID is set
    if (isset($_POST['author_id'])) {
        // Get the current restricted authors
        $restricted_authors = get_option('restricted_authors', array());

        // Remove the author ID from the array
        $key = array_search($_POST['author_id'], $restricted_authors);
        if ($key !== false) {
            unset($restricted_authors[$key]);
        }

        // Update the restricted authors
        update_option('restricted_authors', $restricted_authors);

        // Return a success response
        wp_send_json_success();
    } else {
        // Return an error response
        wp_send_json_error();
    }
}
add_action('wp_ajax_remove_author', 'remove_author');



function clear_all_authors()
{
    // Clear all saved authors
    update_option('restricted_authors', array());

    // Return a success response
    wp_send_json_success();
}
// Add a new AJAX action for clearing all authors
add_action('wp_ajax_clear_all_authors', 'clear_all_authors');



// Create the settings page content
function restrict_author_profile_access_settings_page()
{
?>
    <div class="wrap">
        <h1>Restrict Author Access Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields('restrict-author-profile-access-group'); ?>
            <?php do_settings_sections('restrict-author-profile-access-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <h2>Add Restricted Authors</h2>
                    <td>
                        <?php
                        $restricted_authors = get_option('restricted_authors', array());
                        
                        // visible input to select the authors
                        echo '<select name="restricted_authors[]" multiple="multiple" class="multi-author-select">';
                        $users = get_users();
                        foreach ($users as $user) {
                            $display_name = esc_html($user->display_name) . ' (ID: ' . $user->ID . ')';
                            // Exclude the saved authors from the dropdown
                            if (!in_array($user->ID, $restricted_authors)) {
                                echo '<option author-id="1" value="' . esc_attr($user->ID) . '">' . $display_name . '</option>';
                            }
                        }
                        echo '</select>';
                        
                        // hidden input to save the selected authors
                        echo '<select name="restricted_authors[]" multiple="multiple" class="multi-author-select" id="saved-author-ids">';
                        $users = get_users();
                        foreach ($users as $user) {
                            $display_name = esc_html($user->display_name) . ' (ID: ' . $user->ID . ')';
                            // Only show the saved authors in the dropdown
                            if (in_array($user->ID, $restricted_authors)) {
                                echo '<option author-id="1" value="' . esc_attr($user->ID) . '" selected>' . $display_name . '</option>';
                            }
                        }
                        echo '</select>';
                        ?>
                        <p class="description">Select multiple authors to restrict access.</p>

                        <style>
                            /* apply display none to #saved-author-ids and the coming span */
                            #saved-author-ids,
                            #saved-author-ids + span {
                                display: none;
                            }

                        </style>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
            <p>
                Only the authors selected above will be able to access their author pages. All other users will be redirected to a 404 page.
            </p>
        </form>

        <div>
            <h2>Saved Authors</h2>
            <button id="clear-all-authors">Clear All Authors</button>
            <?php
            $saved_authors = get_option('restricted_authors', array());
            if (!empty($saved_authors)) {
                echo '<ul>';
                foreach ($saved_authors as $author_id) {
                    $author_data = get_userdata($author_id);
                    if ($author_data) {
                        echo '<li>';
                        echo esc_html($author_data->display_name);
                        echo ' (ID: ' . $author_id . ')';
                        echo ' <a href="#" class="remove-author" data-author-id="' . esc_attr($author_id) . '">Remove</a>';
                        echo '</li>';
                    }
                }
                echo '</ul>';
            } else {
                echo '<p>No authors saved.</p>';
            }
            ?>
        </div>

        <script>
            jQuery(document).ready(function($) {
                // Remove author from saved list
                $('.remove-author').on('click', function(e) {
                    e.preventDefault();

                    // Get the author ID from the data attribute
                    var author_id = $(this).data('author-id');

                    // Make an AJAX request to remove the author
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            action: 'remove_author',
                            author_id: author_id
                        },
                        success: function(response) {
                            // Log success message to the console
                            console.log(`Author (ID: ${author_id}) removed successfully: ${response}`);

                            location.reload();
                        },
                        error: function(error) {
                            // Log the error to the console
                            console.error('Error removing author:', error);

                            // Display an alert with the error message
                            alert('Error removing author. Please check the console for details.');
                        }
                    });
                });

                // Add an event listener for the clear all authors button
                $('#clear-all-authors').on('click', function(e) {
                    e.preventDefault();

                    // Confirm clearing all authors
                    var confirmClear = confirm('Are you sure you want to clear all authors?');
                    if (confirmClear) {
                        // Make an AJAX request to clear all authors from the database
                        $.post(ajaxurl, {
                            action: 'clear_all_authors'
                        }).done(function() {
                            console.log('All authors cleared successfully.');
                            location.reload();
                        });
                    }
                });
            });
        </script>
    </div>
<?php
}

function sanitize_callback($input)
{
    if (is_array($input)) {
        return array_map('intval', $input);
    }
    return '';
}

/**
 * Check if the author profile is restricted.
 * 
 * @param int $author_id The author ID.
 * 
 * @return bool True if the author is restricted, false if not.
 
 */
function wprapa_is_author_profile_restricted($author_id)
{
    // Get the restricted authors from the plugin settings
    $restricted_authors = get_option('restricted_authors', array());

    // Check if the author is in the restricted list
    if (in_array($author_id, $restricted_authors)) {
        return true;
    } else {
        return false;
    }
}
add_action('plugins_loaded', 'wprapa_is_author_profile_restricted');
?>