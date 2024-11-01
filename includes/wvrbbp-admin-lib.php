<?php

function wvrbbp_help_link($ref, $label)
{

    $t_dir = wvrbbp_plugins_url('/help/' . $ref, '');
    $icon = wvrbbp_plugins_url('/help/help.png', '');
    $pp_help = '<a href="' . $t_dir . '" target="_blank" title="' . $label . '">'
        . '<img class="entry-cat-img" src="' . $icon . '" style="position:relative; top:4px; padding-left:4px;" title="' .
        __('Click for help', 'weaver_for_bbpress') . '" alt="' . __('Click for help', 'weaver_for_bbpress') . '" /></a>';
    echo $pp_help;
}


function wvrbbp_save_msg($msg)
{
    echo '<div id="message" class="updated fade" style="width:70%;"><p><strong>' . $msg .
        '</strong></p></div>';
}

function wvrbbp_error_msg($msg)
{
    echo '<div id="message" class="updated fade" style="background:#F88;" style="width:70%;"><p><strong>' . $msg .
        '</strong></p></div>';
}

function wvrbbp_donate_button()
{

    if (!wvrbbp_getopt('hide_donate')) {
        $img = WP_CONTENT_URL . '/plugins/weaver-for-bbpress/images/donate-button.png';
        ?>
        <div style="float:right;padding-right:30px;display:inline-block;">
            <div style="font-size:14px;font-weight:bold;display:inline-block;vertical-align: top;"><?php _e('Like <em>Turnkey bbPress</em>? Please', 'weaver-for-bbpress' /*adm*/); ?></div>&nbsp;&nbsp;<a
                    href='//weavertheme.com/donate' target='_blank'><img src="<?php echo $img; ?>" alt="donate"
                                                                         style="max-height:28px;"/></a>
        </div>
        <br style="clear:both;"/>
    <?php }
}


// =======================================>>> Save/Restore <<<=================================
function wvrbbp_download_link($desc, $filebase, $ext, $time)
{

    $nonce = wp_create_nonce('wvrbbp_download');

    $downloader = plugins_url() . '/weaver-for-bbpress/includes/downloader.php';
    $download_img_path = plugins_url() . '/weaver-for-bbpress/images/download.png';

    $filename = "{$filebase}-{$time}.{$ext}";
    $href = $downloader . "?_wpnonce={$nonce}&_ext={$ext}&_file={$filename}";
    ?>
    <a style="margin-left:2em;text-decoration: none;" href="<?php echo esc_url($href); ?>">
    <span class="download-link"><img src="<?php echo esc_url($download_img_path); ?>" alt="download"/>
    <?php _e('Download', 'weaver-xtreme' /*adm*/);
    echo '</span></a> - ';
    echo $desc;
    echo ' &nbsp;';
    _e('Save as:', 'weaver_for_bbpress');
    echo ' ' . $filename . "<br /><br />\n";
}

function wvrbbp_save_restore()
{
    if (!(isset($_POST['uploadit']) && $_POST['uploadit'] == 'yes')) {
        return false;
    }

    // upload theme from users computer
    // they've supplied and uploaded a file

    // echo '<pre>'; print_r($_FILES); echo '</pre>';

    $ok = true;     // no errors so far
    $errors = array();

    if (isset($_FILES['post_uploaded']['name'])) {
        $filename = $_FILES['post_uploaded']['name'];
    } else {
        $filename = "";
    }

    if (isset($_FILES['post_uploaded']['tmp_name'])) {
        $openname = $_FILES['post_uploaded']['tmp_name'];
    } else {
        $openname = "";
    }

    //Check the file extension
    $check_file = strtolower($filename);
    $pat = '.';                // PHP version strict checking bug...
    $end = explode($pat, $check_file);

    if ($filename == "") {
        $errors[] = __("You didn't select a file to upload.", 'weaver_for_bbpress') . "<br />";
        $ok = false;
    }

    if (!$ok) {
        echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">' .
            __('ERROR', 'weaver_for_bbpress') . '</em></strong></p><p>';
        foreach ($errors as $error) {
            echo $error . '<br />';
        }
        echo '</p></div>';

        return false;
    } else {    // OK - read file and save to My Saved Theme
        // $handle has file handle to temp file.//
        $contents = file_get_contents($openname);

        if (!wvrbbp_set_to_serialized_values($contents)) {
            echo '<div id="message" class="updated fade"><p><strong><em style="color:red;">' .
                __('Sorry, there was a problem uploading your file. The file you picked was not a valid Turnkey bbPress settings file.', 'weaver_for_bbpress') .
                '</em></strong></p></div>';

            return false;
        } else {
            wvrbbp_save_msg(__('Turnkey bbPress Settings Restored.', 'weaver_for_bbpress'));
            echo '<script>location.reload(true);</script>';                // sweet way to reload settings
        }
    }

    return true;
}

function wvrbbp_set_to_serialized_values($contents)
{

    $restore = unserialize($contents);

    if (!isset($restore['wvrbbp'])) {
        return false;
    }

    $current_settings = $restore['wvrbbp'];

    wvrbbp_wpupdate_option('wvrbbp_settings', $current_settings);

    return true;
}

/*
	================= nonce helpers =====================
*/
function wvrbbp_submitted($submit_name)
{
    // do a nonce check for each form submit button
    // pairs 1:1 with aspen_nonce_field
    $nonce_act = $submit_name . '_act';
    $nonce_name = $submit_name . '_nonce';

    if (isset($_POST[$submit_name])) {
        if (isset($_POST[$nonce_name]) && wp_verify_nonce($_POST[$nonce_name], $nonce_act)) {
            return true;
        } else {
            die(sprintf(__("WARNING: invalid form submit detected (%s). Probably caused by session time-out, or, rarely, a failed security check.", 'weaver_for_bbpress'), $submit_name));
        }
    } else {
        return false;
    }
}

function wvrbbp_nonce_field($submit_name, $echo = true)
{
    // pairs 1:1 with submitted
    // will be one for each form submit button

    return wp_nonce_field($submit_name . '_act', $submit_name . '_nonce', $echo);
}

/*
	================= form helpers =====================
*/

function wvrbbp_get_POST($id)
{
    return isset($_POST[$id]) ? stripslashes($_POST[$id]) : '';
}

// general values - wvrbbp_getopt

function wvrbbp_form_checkbox($id, $desc, $br = '<br />') {
    ?>
    <div style="display:inline;padding-left:2.5em;text-indent:-1.7em;"><label><input type="checkbox"
                                                                                     name="<?php echo $id ?>"
                                                                                     id="<?php echo $id; ?>"
        <?php checked(wvrbbp_getopt($id)); ?> >&nbsp;
    <?php echo $desc . '</label></div>' . $br . "\n";
}

// filter values - wvrbbp_getopts

function wvrbbp_checkbox($id, $desc, $br = '<br />') {
    ?>
    <div style="display:inline;padding-left:2.5em;text-indent:-1.7em;"><label><input type="checkbox"
                                                                                     name="<?php echo $id; ?>"
                                                                                     id="<?php echo $id; ?>"
        <?php checked(wvrbbp_getopt($id)); ?> >&nbsp;
    <?php echo $desc . '</label></div>' . $br . "\n";
}

function wvrbbp_textarea($id, $desc, $br = '<br />', $cols = 40, $rows = 1, $default = '') {
    ?>
    <div style="margin-top:5px;display:inline-block;padding-left:4em;text-indent:-1.7em;"><label>
    <?php
    if ($cols <= 12 && $rows == 1) {    // use a simple text
        ?>
        <input class="regular-text" type="text" style="width:50px;height:22px;" name="<?php echo $id; ?>"
               value="<?php echo sanitize_text_field(wvrbbp_getopt($id)); ?>"/>
        <?php
    } else {
        ?>
        <textarea style="margin-bottom:-8px;" cols="<?php echo $cols; ?>" rows="<?php echo $rows; ?>" maxlength=300
                  name="<?php echo $id; ?>"><?php echo esc_html(wvrbbp_getopt($id, $default)); ?></textarea>
    <?php } ?>

    &nbsp;
    <?php echo $desc . '</label></div>' . $br . "\n";
}

function wvrbbp_val($id, $desc, $br = '<br />')
{
    ?>
    <div style="margin-top:5px;display:inline-block;padding-left:2.5em;text-indent:-1.7em;"><label>
    <input class="regular-text" type="text" style="width:50px;height:22px;" name="<?php echo $id; ?>"
           value="<?php echo sanitize_text_field(wvrbbp_getopt($id)); ?>"/>
    &nbsp;
    <?php echo $desc . '</label></div>' . $br . "\n";
}

