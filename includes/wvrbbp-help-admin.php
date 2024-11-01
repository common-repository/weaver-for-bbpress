<?php
// ========================================= >>> wvrbbp_select_filter <<< ===============================
function wvrbbp_help_admin()
{
    // admin for help

    ?>
    <h2 style="color:blue;font-weight:bold;"><?php _e('Quick Start Help', 'weaver-for-bbpress'); ?></h2>

    <h3>Turnkey bbPress Help</h3>
    Documentation for Turnkey bbPress can be found on the <a href="//guide.weavertheme.com/category/weaver-for-bbpress/"
                                                             target="_blank">Turnkey bbPress Guide </a>page.

    <p>Help is provided at the <a href="//forum.weavertheme.com" target="_blank">Weaver Support Forum</a>.</p>

    <h3>Recommended Plugins</h3>

    <p>To help build a user friendly, active forum, we also recommend the following plugins. <strong>Turnkey
            bbPress</strong> also supports these plugins with integrated subTheme styling.
        <br/>
    <ol>
        <li><a href="//wordpress.org/plugins/bbpress-pencil-unread/" target="_blank"><strong>bbPress Pencil
                    Unread</strong></a> - What, a forum without an indicator for Unread posts? Bad idea. This plugin
            will clearly indicate unread Topics, and Weaver
            for bbPress styles the marker to match the theme. The "Mark all as Read" link is styled as a button that
            appears at the top right of forum Topic list.
        </li>
        <br/>

        <li><a href="//wordpress.org/plugins/search/bbpress+moderation+tools/" target="_blank"><strong>bbPress
                    Moderation Tools</strong></a> - If you need to add Topic/Reply moderation tools, this is a very well
            written and simple plugin. Extends standard
            WP Comment moderation to bbPress.
        </li>
        <br/>

        <li><a href="//wordpress.org/plugins/bbp-move-topics/" target="_blank"><strong>bbp Move Topics</strong></a> -
            Move topics from one forum to another, convert post/comments into topic/replies in the same site. Essential
            moderation tool.
        </li>
        <br/>

        <li><a href="//wordpress.org/plugins/wp-edit/" target="_blank"><strong>WP Edit</strong></a> - Adds new features
            to the visual editor. Most usefully, it allows you to customize the formatting buttons on the visual editor
            button bar. We recommend
            using <em>Turnkey bbPress's</em> option to enable the visual editor rather than WP Edit's option, however.
            You still get all the great WP Edit features.
        </li>
        <br/>

        <li><a href="//wordpress.org/plugins/classic-editor/" target="_blank"><strong>Classic Editor</strong></a> - With
            the advent of the new Block Editor (Gutenberg) in WP 5, bbPress will default to the new editor, which is not
            really suitable for
            simple visitor post creation. We recommend using the WP Classic Editor plugin. After you install it, from
            the admin account open the dashboard Settings : Writing menu and check 'Classic Editor' as the default
            editor, and 'No' to allow users to
            switch editors.
        </li>

    </ol>
    <br/>
    <h3>Great Plugins You Probably Don't Need Now</h3>
    <p>The following plugins are great, but most of their major functionality is now included with <strong>Turnkey
            bbPress</strong>.</p>
    <ol>
        <li><strong>bbp style pack, bbPress forum utility pack, bbPress WP Tweaks</strong> - these are among several
            other similar plugins that provide different kinds of tweaking for bbPress. We've integrated the most useful
            tweaks into Weaver for
            bbPress, and you most likely won't need these plugins any more.
        </li>
        <br/>
        <li><strong>bbPress Private Replies</strong> - its features are enhanced and integrated into Turnkey bbPress,
            including styling.
        </li>
        <br/>
        <li><strong>bbPress Simple View Counts</strong> - its features are enhanced and integrated into Turnkey bbPress.
            In fact, Turnkey bbPress will detect and use the count data from this plugin, so you won't even lose your
            data by switching.
        </li>
        <br/>
        <li><strong>bbResoutions</strong> - most of its features have been integrated into Turnkey bbPress. The Turnkey
            bbPress
            version is compatible with the resolution settings by bbResolutions so your resolution status will be
            unchanged.
    </ol>
    <div>
        <?php wvrbbp_ts_more_help(); ?>
    </div>
    </div>

    </div> <!-- xxxx -->
    <?php

}


function wvrbbp_ts_more_help()
{
    ?>
    <hr/>
    <h3><?php _e('Your System and Configuration Info', 'weaver-for-bbpress' /*adm*/); ?></h3>
    <?php
    $sys = wvrbbp_ts_get_sysinfo();
    ?>
    <div style="float:left;max-width:60%;"><textarea id="wvrx-sysinfo" readonly class="wvrx-sysinfo no-autosize"
                                                     style="font-family:monospace;" rows="12"
                                                     cols="50"><?php echo $sys; ?></textarea></div>
    <div style="margin-left:20px;max-width:40%;float:left;"><?php _e('<p>This information can be used to help us diagnose issues you might be having with Weaver Xtreme.
If you are asked by a moderator on the <a href="//forum.weavertheme.com" target="_blank">Weaver Support Forum</a>, please select all the info, then copy, then Paste the Sysinfo report directly into a Forum post.</p>
<p>Please note that there is no personally identifying data in this report except your site\'s URL. Having your site URL is important to help us
diagnose the problem, but you can delete it from your forum post right after you paste if you need to.</p>', 'wvrbbp-theme-support'); ?></div>
    <div style="clear:both;margin-bottom:20px;"></div>

    <div><strong>Please select all the text in the above box, then copy it so you can paste to the forum.</strong></div>
    <?php
    //if (wvrbbp_DEV_MODE && isset($GLOBALS['POST_COPY']) && $GLOBALS['POST_COPY'] != false ) {
    //	echo '<pre>$_POST:'; var_dump($GLOBALS['POST_COPY']); echo '</pre>';
    //}
}


function wvrbbp_ts_get_sysinfo()
{

    global $wpdb;

    $theme = wp_get_theme()->Name . ' (' . wp_get_theme()->Version . ')';
    $frontpage = get_option('page_on_front');
    $frontpost = get_option('page_for_posts');
    $fr_page = $frontpage ? get_the_title($frontpage) . ' (ID# ' . $frontpage . ')' . '' : 'n/a';
    $fr_post = $frontpage ? get_the_title($frontpost) . ' (ID# ' . $frontpost . ')' . '' : 'n/a';
    $jquchk = wp_script_is('jquery', 'registered') ? $GLOBALS['wp_scripts']->registered['jquery']->ver : 'n/a';

    $return = '### Weaver System Info ###' . "\n\n";

    // Basic site info
    $return .= '        -- WordPress Configuration --' . "\n\n";
    $return .= 'Site URL:                 ' . site_url() . "\n";
    $return .= 'Home URL:                 ' . home_url() . "\n";
    $return .= 'Multisite:                ' . (is_multisite() ? 'Yes' : 'No') . "\n";
    $return .= 'Version:                  ' . get_bloginfo('version') . "\n";
    $return .= 'Language:                 ' . get_locale() . "\n";
    //$return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . "\n";
    $return .= 'WP_DEBUG:                 ' . (defined('WP_DEBUG') ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set') . "\n";
    $return .= 'WP Memory Limit:          ' . WP_MEMORY_LIMIT . "\n";
    $return .= 'Permalink:                ' . get_option('permalink_structure') . "\n";
    $return .= 'Show On Front:            ' . get_option('show_on_front') . "\n";
    $return .= 'Page On Front:            ' . $fr_page . "\n";
    $return .= 'Page For Posts:           ' . $fr_post . "\n";
    $return .= 'Current Theme:            ' . $theme . "\n";
    $return .= 'Post Types:               ' . implode(', ', get_post_types('', 'names')) . "\n";

    // Plugin Configuration
    $return .= "\n" . '        -- Turnkey bbPress Configuration --' . "\n\n";
    $return .= 'Turnkey bbPress:    ' . wvrbbp_VERSION . "\n";


    // Server Configuration
    $return .= "\n" . '        -- Server Configuration --' . "\n\n";
    $return .= 'Operating System:         ' . php_uname('s') . "\n";
    $return .= 'PHP Version:              ' . PHP_VERSION . "\n";
    $return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
    $return .= 'jQuery Version:           ' . $jquchk . "\n";

    $return .= 'Server Software:          ' . $_SERVER['SERVER_SOFTWARE'] . "\n";

    // PHP configs... now we're getting to the important stuff
    $return .= "\n" . '        -- PHP Configuration --' . "\n\n";
    //$return .= 'Safe Mode:                ' . ( ini_get( 'safe_mode' ) ? 'Enabled' : 'Disabled' . "\n" );
    $return .= 'Local Memory Limit:       ' . ini_get('memory_limit') . "\n";
    $return .= 'Server Memory Limit:      ' . get_cfg_var('memory_limit') . "\n";
    $return .= 'Post Max Size:            ' . ini_get('post_max_size') . "\n";
    $return .= 'Upload Max Filesize:      ' . ini_get('upload_max_filesize') . "\n";
    $return .= 'Time Limit:               ' . ini_get('max_execution_time') . "\n";
    $return .= 'Max Input Vars:           ' . ini_get('max_input_vars') . "\n";
    $return .= 'Display Errors:           ' . (ini_get('display_errors') ? 'On (' . ini_get('display_errors') . ')' : 'N/A') . "\n";

    // WordPress active plugins
    $return .= "\n" . '        -- WordPress Active Plugins --' . "\n\n";
    $plugins = get_plugins();
    $active_plugins = get_option('active_plugins', array());
    foreach ($plugins as $plugin_path => $plugin) {
        if (!in_array($plugin_path, $active_plugins)) {
            continue;
        }
        $return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
    }

    // WordPress inactive plugins
    $return .= "\n" . '        -- WordPress Inactive Plugins --' . "\n\n";
    foreach ($plugins as $plugin_path => $plugin) {
        if (in_array($plugin_path, $active_plugins)) {
            continue;
        }
        $return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
    }

    if (is_multisite()) {
        // WordPress Multisite active plugins
        $return .= "\n" . '        -- Network Active Plugins --' . "\n\n";
        $plugins = wp_get_active_network_plugins();
        $active_plugins = get_site_option('active_sitewide_plugins', array());
        foreach ($plugins as $plugin_path) {
            $plugin_base = plugin_basename($plugin_path);
            if (!array_key_exists($plugin_base, $active_plugins)) {
                continue;
            }
            $plugin = get_plugin_data($plugin_path);
            $return .= $plugin['Name'] . ': ' . $plugin['Version'] . "\n";
        }
    }

    $return .= "\n" . '### End System Info ###' . "\n";

    return $return;
}


?>
