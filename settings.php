<?php
/*
 *  Injects content into page at different places
 */

// Prevent direct access to file
defined('ABSPATH') or die("Cheatin' uh?");

class AdPushup_Settings {

    function __construct() {
        add_action('admin_menu', function () {
            add_options_page(
                    'AdPushUp Settings', 'AdPushUp Settings', 'manage_options', 'adpushup_settings_page', array($this, 'settings_page')
            );
        });
        add_action('admin_notices', array($this, 'action_admin_notices'));
    }

    function action_admin_notices() {
        global $pagenow;

        $adpush_code = get_option('adpushup_site_id', '');
        $url = admin_url() . 'options-general.php?page=adpushup_settings_page';

        // Show error message everywhere except its setting page
        if (
                !('options-general.php' == $pagenow && isset($_GET['page']) && 'adpushup_settings_page' == $_GET['page']) &&
                empty($adpush_code)
        ) {
            echo "<script type='text/javascript'>
                    // Popup window code
                    function newPopup(url) {
                        popupWindow = window.open(
                            url,'popUpWindow','height=350,width=500,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no,status=yes'
                        )
                    }
                </script>
                
                <div class='error'><p>Please <b><a href='JavaScript:newPopup(\"$url\");'>install AdPushup site ID</a><b> to use it.</p></div>";
        }
    }

    function settings_page() {
        if (isset($_REQUEST['adpushup_site_id'])) {
            $adpushup_site_id = (int) $_REQUEST['adpushup_site_id'];

            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], '_adpushup_site_id')) {
                update_option('adpushup_site_id', $adpushup_site_id);
            }
        } else {
            $adpushup_site_id = get_option('adpushup_site_id', '');
        }
        ?>
        <h3>Please enter AdPushup code</h3>
        <form method="POST" name="adpushup_submission_form">
            <?php wp_nonce_field('_adpushup_site_id') ?>
            <input type="number" name="adpushup_site_id" required size="50" value="<?php echo esc_attr($adpushup_site_id) ?>" />
            <?php submit_button('Update code'); ?>
        </form>
        <?php
    }

}

new AdPushup_Settings();
