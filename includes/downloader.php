<?php
// will down load current settings based on db setting
// __ added - 12/11/14

$wp_root = dirname(__FILE__) . '/../../../../';
if (file_exists($wp_root . 'wp-load.php')) {
    require_once($wp_root . "wp-load.php");
} elseif (file_exists($wp_root . 'wp-config.php')) {
    require_once($wp_root . "wp-config.php");
} else {
    exit;
}


@error_reporting(0);

$nonce = '';
$show_fn = '';
$ext = '';

if (isset($_GET['_wpnonce'])) {
    $nonce = $_GET['_wpnonce'];
}

if (isset($_GET['_file'])) {
    $show_fn = $_GET['_file'];
}

if (isset($_GET['_ext'])) {
    $ext = $_GET['_ext'];
}

if (!$nonce || !$show_fn || !$ext) {
    @header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
    wp_die(__('Sorry - invalid download', 'weaver-for-bbpress'));
}

if (!wp_verify_nonce($nonce, 'wvrbbp_download')) {
    @header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
    wp_die(__('Sorry - download must be initiated from admin panel.', 'weaver-for-bbpress') . ':' . $nonce);
}

if (headers_sent()) {
    @header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
    wp_die(__('Headers Sent: The headers have been sent by another plugin - there may be a plugin conflict.', 'weaver-for-bbpress'));
}

//@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
//wp_die("Ready to download: {$show_fn} - ext: {$ext}");

$show_opts = get_option('wvrbbp_settings', array());

if ($ext == 'wvrbbp') {
    $save = array();
    $save['wvrbbp'] = $show_opts;
} else {
    @header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
    wp_die(__("Error - trying to save invalid type of settings:", 'weaver-for-bbpress') . " {$ext}.");
}

$save_settings = serialize($save);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $show_fn);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . strlen($save_settings));
echo $save_settings;
exit;

