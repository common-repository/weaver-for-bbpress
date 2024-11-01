<?php
/*

This code is Copyright 2011-2016 by Bruce E. Wampler, all rights reserved.
This code is licensed under the terms of the accompanying license file: license.txt.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

require_once(dirname(__FILE__) . '/wvrbbp-admin-lib.php'); // NOW - load the admin stuff

function wvrbbp_admin_page()
{
    wvrbbp_submits();        // process submit settings

    $name = wvrbbp_NAME . ' (' . __('Version', 'weaver_for_bbpress') . ' ' . wvrbbp_VERSION . ')';
    ?>
    <br/>
    <div class="atw-wrap">
    <div style="font-weight:bold;font-size:180%;margin-top:1em;display:inline;"><?php echo $name; ?></div>
    <?php wvrbbp_donate_button(); ?>
    <hr/>

    <div id="tabwrap_plus" style="padding-left:5px;">
    <div id="tab-container-plus" class='yetii'>
        <ul id="tab-container-plus-nav" class='yetii'>

            <li><a href="#tab-css" title="Style"><?php _e('Themes &amp; CSS', 'weaver_for_bbpress'); ?></a></li>

            <li><a href="#tab-layouts"
                   title="Layout"><?php _e('Forums &amp; Topics Layout', 'weaver_for_bbpress'); ?></a></li>

            <li><a href="#tab-members" title="Members"><?php _e('Members', 'weaver_for_bbpress'); ?></a></li>

            <li><a href="#tab-admin" title="Admin"><?php _e('Admin', 'weaver_for_bbpress'); ?></a></li>

            <li><a href="#tab-restore" title="Save"><?php _e('Save/Restore', 'weaver_for_bbpress'); ?></a></li>


            <li><a href="#tab-help" title="Help"><?php _e('Help', 'weaver_for_bbpress'); ?></a></li>

        </ul>
        <hr/>
        <?php /* IMPORTANT - in spite of the id's, these MUST be in the correct order - the same as the above list... */
        ?>
        <!-- ******* -->

        <div id="tab-css" class="tab_plus"> <!-- Themes & CSS -->

            <?php
            require_once(dirname(__FILE__) . '/wvrbbp-style-admin.php'); // NOW - load the admin stuff
            wvrbbp_style_admin();
            ?>
        </div>

        <!-- ******* -->

        <div id="tab-layouts" class="tab_plus"> <!-- Forums & Topics Layout -->
            <?php
            require_once(dirname(__FILE__) . '/wvrbbp-layout-admin.php'); // NOW - load the admin stuff
            wvrbbp_layout_admin();

            ?>
        </div>

        <!-- ******* -->

        <div id="tab-members" class="tab_plus"> <!-- Members -->
            <?php
            require_once(dirname(__FILE__) . '/wvrbbp-members-admin.php'); // NOW - load the admin stuff
            wvrbbp_members_admin();
            ?>
        </div>


        <!-- ******* -->

        <div id="tab-admin-users" class="tab_plus"> <!-- Admin & Users -->
            <?php
            require_once(dirname(__FILE__) . '/wvrbbp-admin-admin.php'); // NOW - load the admin stuff
            wvrbbp_admin_users_admin();
            ?>
        </div>


        <!-- ******* -->

        <div id="tab-restore" class="tab_plus"> <!-- Themes & CSS -->

            <?php
            require_once(dirname(__FILE__) . '/wvrbbp-save-restore-admin.php'); // NOW - load the admin stuff
            wvrbbp_save_restore_admin();
            ?>
        </div>

        <!-- ******* -->

        <div id="tab-help" class="tab_plus"> <!-- Help -->

            <?php
            require_once(dirname(__FILE__) . '/wvrbbp-help-admin.php'); // NOW - load the admin stuff
            wvrbbp_help_admin();
            ?>

        </div>
    </div> <!-- #tabwrap_plus -->


    <script type="text/javascript">
        var tabber2 = new Yetiiw4bb({
            id: 'tab-container-plus',
            tabclass: 'tab_plus',
            persist: true
        });
    </script>


    <?php
} // end wvrbbp_admin

// ========================================= FORM DISPLAY ===============================

function wvrbbp_t_($s)
{
    return $s;
}


function wvrbbp_submits()
{
    // process settings for plugin parts


    // for each option section, define a save filter for the save button. Add the name here, then call the handler.
    $actions = array(
        'wvrbbp_save_style_opts',
        'wvrbbp_save_layout_opts',
        'wvrbbp_save_member_opts',
        'wvrbbp_save_user_options',
        'wvrbbp_save_restore',
    );


    foreach ($actions as $functionName) {
        if (isset($_POST[$functionName])) {
            if (wvrbbp_submitted($functionName) && function_exists($functionName)) {
                if ($functionName()) {
                    break;
                }
            }
        }
    }
}

// ======================== options handlers ==========================

// ========================================= >>> wvrbbp_save_layout_opts <<< ===============================
function wvrbbp_save_layout_opts()
{

    wvrbbp_save_form_options(
    /* $check_opts */
        array(
            'enable_new_topic_link',
            'add_forum_description',
            'hide_contains_descriptions',
            'hide_account_ability',
            'clear_after_breadcrumbs',
            'forum_list_columns',
            'forum_list_columns',
            'hide_voices',
            'hide_counts',
            'enable_view_count',
            'view_count_only_logged',
            'hide_fav_sub',
        ),
        /* text_opts */
        array()
    );
    wvrbbp_save_msg(__('Layout Options Saved', 'weaver-for-bbpress'));
}

// ========================================= >>> wvrbbp_save_member_opts <<< ===============================
function wvrbbp_save_member_opts()
{

    wvrbbp_save_form_options(
    /* $check_opts */
        array(
            'enable_mentions',
            'enable_profile_options',
            'enable_private_reply',
            'enable_best_answer',
        ),
        /* text_opts */
        array('email_subject', 'email_body', 'mention_notify', 'profile_visibility')
    );
    wvrbbp_save_msg(__('Member Options Saved', 'weaver-for-bbpress'));
}

// ========================================= >>> wvrbbp_save_user_options <<< ===============================
function wvrbbp_save_user_options()
{

    wvrbbp_save_form_options(
    /* $check_opts */
        array(
            'disable_moderation',
            'enable_visual_editor',
            'enable_email_link',
            'enable_auto_block',
            'show_post_status',
            'enable_resolution',
            'use_resolve_icons',
            'hide_donate',
            'logout_show_time',
        ),
        /* text_opts */
        array(
            'email_name',
            'email_address',
            'hide_wp_admin_bar',
            'logged_in_message',
            'logout_widget_msg',
            'new_topic_msg',
            'register_page',
        )
    );


    wvrbbp_save_msg(__('Admin &amp; User Options Saved', 'weaver-for-bbpress'));
}

// ========================================= >>> wvrbbp_save_form_opts <<< ===============================
// *******

function wvrbbp_save_form_options($check_opts = array(), $text_opts = array())
{

    //echo '<pre>';print_r($_POST); print_r($text_opts); print_r($check_opts); echo '</pre>';

    // **** text fields and selects

    foreach ($text_opts as $opt) {
        $val = wp_kses_post(wvrbbp_get_POST($opt));
        wvrbbp_setopt($opt, $val);
    }

    foreach ($check_opts as $opt) {
        if (wvrbbp_get_POST($opt) != '') {
            wvrbbp_setopt($opt, true);
        } else {
            wvrbbp_setopt($opt, false);
        }
    }

    wvrbbp_save_all_options();    // and save them to db
}

// ========================================= >>> wvrbbp_save_style_opts <<< ===============================

function wvrbbp_save_style_opts()
{

    wvrbbp_save_form_options(
    /* $check_opts */
        array(
            'round_avatars',
            'hide_small_avatars',
            'no_even_odd',
            'author_shadow',
        ),
        /* text_opts */
        array(
            'theme',
            'base_font_size',
            'tiny_author_avatar_size',
            'show_freshness_avatar',
            'show_started_by_avatar',
            'alt_style_file',
        )
    );


    $css = wp_check_invalid_utf8(trim(wvrbbp_get_POST('wvrbbp_custom_css')));
    wvrbbp_setopt('custom_css', $css);

    wvrbbp_save_all_options();    // and save them to db
    wvrbbp_save_msg(__('Theme and Custom CSS Settings saved.', 'weaver_for_bbpress'));
}

