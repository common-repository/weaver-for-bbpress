<?php
function wvrbbp_alert($msg)
{
    echo "<script> alert('" . esc_html($msg) . "'); </script>";
}

// ===============================  options =============================
$wvrbbp_opts_cache = false;

function wvrbbp_getopt($opt, $default = false)
{
    global $wvrbbp_opts_cache;
    if (!$wvrbbp_opts_cache) {
        $wvrbbp_opts_cache = get_option('wvrbbp_settings', array());
    }

    if (!isset($wvrbbp_opts_cache[$opt]) || !$wvrbbp_opts_cache[$opt])    // handles changes to data structure
    {
        return $default;
    }

    return $wvrbbp_opts_cache[$opt];
}

function wvrbbp_setopt($opt, $val, $save = true)
{
    global $wvrbbp_opts_cache;
    if (!$wvrbbp_opts_cache) {
        $wvrbbp_opts_cache = get_option('wvrbbp_settings', array());
    }

    $wvrbbp_opts_cache[$opt] = $val;
    if ($save) {
        wvrbbp_wpupdate_option('wvrbbp_settings', $wvrbbp_opts_cache);
    }
}


//----

function wvrbbp_save_all_options()
{
    global $wvrbbp_opts_cache;

    if ($wvrbbp_opts_cache) // don't save anything if we have nothing to save yet.
    {
        wvrbbp_wpupdate_option('wvrbbp_settings', $wvrbbp_opts_cache);
    }
}

function wvrbbp_delete_all_options()
{
    global $wvrbbp_opts_cache;
    $wvrbbp_opts_cache = false;
    if (current_user_can('manage_options')) {
        delete_option('wvrbbp_settings');
    }
}

function wvrbbp_wpupdate_option($name, $opts)
{
    if (current_user_can('manage_options')) {
        update_option($name, $opts);
    }
}

