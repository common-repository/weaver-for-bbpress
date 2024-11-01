<?php
/*
Plugin Name: Turnkey bbPress by WeaverTheme
Plugin URI: https://weavertheme.com/weaver-for-bbpress/
Description: Turnkey bbPress: easy bbPress styling that blends beautifully with your current WP Theme. Plus ESSENTIAL layout and user/admin usability options.
Author: wpweaver
Author URI: http://weavertheme.com/about/
Version: 1.5
Text Domain: weaver-for-bbpress
Domain Path: /languages
*/

/*

License: GPL V2 or later

Turnkey bbPress by WeaverTheme Copyright (C) 2017-2021 Bruce E. Wampler - weaver@weavertheme.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/* CORE FUNCTIONS
*/

// gotta have bbPress loaded
// bbPress seems to load after this plugin so have to check if it is active

include_once(ABSPATH . 'wp-admin/includes/plugin.php');    // need this for is_plugin_active
$dependency = is_plugin_active('bbpress/bbpress.php');

if ($dependency) :        // only load if bbPress is active

    define('wvrbbp_VERSION', '1.5');
    define('wvrbbp_MINIFY', '.min');        // '' for dev, '.min' for production
    define('wvrbbp_DIR_PATH', plugin_dir_path(__FILE__));
    define('wvrbbp_NAME', 'Turnkey bbPress by WeaverTheme.com');

// ===============================>>> REGISTER ACTIONS <<<===============================

    add_action('plugins_loaded', 'wvrbbp_plugins_loaded');

    function wvrbbp_plugins_loaded()
    {

        function wvrbbp_installed()
        {
            return true;
        }

        add_action('admin_menu', 'wvrbbp_admin_menu', 9);

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wvrbbp_bottom_links'); //bottom links

        if (!load_plugin_textdomain('weaver-for-bbpress', false, dirname(plugin_basename(__FILE__)) . '/languages')) // use ours first
        {
            load_plugin_textdomain('weaver-for-bbpress');
        }    // try the wp-content/languages directories

    }

// ===============================>>> DEFINE ACTIONS <<<===============================

    function wvrbbp_admin()
    {
        require_once(dirname(__FILE__) . '/includes/wvrbbp-admin-top.php'); // NOW - load the admin stuff
        wvrbbp_admin_page();
    }

    function wvrbbp_admin_menu()
    {

        $menu = 'Turnkey bbPress';
        $full = 'Turnkey bbPress Plugin by WeaverTheme.com';

        $page = add_submenu_page(
        //'options-general.php',		// Settings menu parent
            'themes.php',
            'Turnkey bbPress',            // page title
            'Turnkey bbPress',            // menu title
            'manage_options',            // change 'switch_themes' capability to something else for placement of dashboard
            'wvrbbp_page',                // slug
            'wvrbbp_admin');


        /* using registered $page handle to hook stylesheet loading for this admin page */
        add_action('admin_print_styles-' . $page, 'wvrbbp_admin_scripts');
    }


    function wvrbbp_admin_scripts()
    {
        /* called only on the admin page, enqueue our special style sheet here (for tabbed pages) */
        wp_enqueue_style('wvrbpp_admin_style', wvrbbp_plugins_url('/wvrbbp-admin-style', wvrbbp_MINIFY . '.css'), array(), wvrbbp_VERSION);

        wp_enqueue_script('wvrbbp_Yetii', wvrbbp_plugins_url('/js/yetii/yetii', wvrbbp_MINIFY . '.js'), array(), wvrbbp_VERSION);

    }

    function wvrbbp_plugins_url($file, $ext = '')
    {
        return plugins_url($file, __FILE__) . $ext;
    }

// ############

    add_action('wp_enqueue_scripts', 'wvrbbp_enqueue_scripts');

    function wvrbbp_enqueue_scripts()
    {    // enqueue runtime scripts

        wp_enqueue_style('dashicons');

        // add plugin CSS here, too.
    }

    function wvrbbp_bottom_links($links)
    {

        if (current_user_can('manage_options')) {
            $settings_page_url = add_query_arg(
                array(
                    'page' => 'wvrbbp_page',
                ),
                get_admin_url(null, 'themes.php')
            );
            $links[] = sprintf('<a href="%s">%s</a>', esc_url($settings_page_url), __('Settings', 'weaver-for-bbpress'));
        }

        return $links;
    }


// ############

    /* setup any shortcodes - possible future enhancement
        if ( false ) :
            function wvrbbp_setup_shortcodes() {
                add_shortcode( 'wvrbbp_shortcode', 'wvrbbp_sc' );
            }

            function wvrbbp_sc( $args = '' ) {
                require_once( dirname( __FILE__ ) . '/includes/wvrbbp-shortcodes.php' );    // load only when have to,

                return wvrbbp_shortcode( $args );
            }
        endif;    // no shortcodes
    */


// ############

    require_once(dirname(__FILE__) . '/includes/wvrbbp-runtime-lib.php'); // NOW - load the basic library
    require_once(dirname(__FILE__) . '/includes/wvrbbp-runtime-actions.php'); // NOW - load actions and filters required
//require_once(dirname( __FILE__ ) . '/includes/wvrbbp-widgets.php'); // NOW - load the widgets ( possible future features )


endif; // end of dependency check
