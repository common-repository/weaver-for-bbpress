<?php
// ############ Add actions and filters here - they really have to be loaded all the time...

// basic setup

function wvrx_bbp_editor_manage_users()
{

    if (get_option('wvrbbp_add_cap_editor_once') != 'done') {

        // let editor manage users

        $edit_editor = get_role('bbp_moderator'); // Get the user role
        if (!empty($edit_editor)) {
            $edit_editor->add_cap('edit_users');
            $edit_editor->add_cap('list_users');
            //$edit_editor->add_cap('promote_users');
            //$edit_editor->add_cap('create_users');
            //$edit_editor->add_cap('add_users');
            //$edit_editor->add_cap('delete_users');
        }

        update_option('wvrbbp_add_cap_editor_once', 'done');
    }

}

add_action('bbp_init', 'wvrx_bbp_editor_manage_users');

// ================== New bbPress 2.6 moderation handling ==================================

if (wvrbbp_getopt('disable_moderation')) {
    add_filter('bbp_bypass_check_for_moderation', '__return_true');
}

//    Handle REPLY moderation notices

add_action('bbp_new_reply_post_extras', 'wvrbbp_reply_post_extras');

function wvrbbp_reply_post_extras($reply_id)
{

    if (bbp_is_reply_pending($reply_id)) {
        $name = 'wvrbbp_reply_message_' . bbp_get_user_id(0, false, true);
        set_transient($name, __('Your reply has been marked as needing moderation. It probably has too many links or disallowed words. Your reply will not be displayed until it has been approved by a Moderator.', 'weaver-for-bbpress'), 180);
    }
}

add_action('bbp_template_after_replies_loop', 'wvrbbp_after_replies_loop');

function wvrbbp_after_replies_loop()
{

    $name = 'wvrbbp_reply_message_' . bbp_get_user_id(0, false, true);

    $reply_message = get_transient($name);
    if ($reply_message)
        echo '<div class="bbp-template-notice important">' . $reply_message . '</div>';
    delete_transient($name);
}


// ================== Change the default wordpress@ email address =========================


if (wvrbbp_getopt('email_name')) {
    add_filter('wp_mail_from_name', 'wvrbbp_new_mail_from_name');
}
if (wvrbbp_getopt('email_address')) {
    add_filter('wp_mail_from', 'wvrbbp_new_mail_from');
}

function wvrbbp_new_mail_from_name($old)
{
    $new = wvrbbp_getopt('email_name');
    if ($new) {
        return $new;
    }

    return $old;
}

function wvrbbp_new_mail_from($old)
{
    $new = wvrbbp_getopt('email_address');
    if ($new) {
        return $new;
    }

    return $old;
}


//'own_and_moderator'
// ================================= Per User Private Profile ====================================


if (wvrbbp_getopt('enable_profile_options')) :

    /** add field to bbp edit **/

    add_action('bbp_user_edit_after_contact', 'wvrbbp_prof_peditField');
    function wvrbbp_prof_peditField()
    {
        ?>
        <div>
            <label for=""><?php echo __('Profile Visibility', 'weaver-for-bbpress'); ?></label>
            <label>
                <input type="checkbox" name="wvrbbp_profile_owner_only"
                       style="width: auto;" <?php checked(wvrbbp_hide_profile(bbp_get_displayed_user_field('ID'))); ?> /> <?php _e('Hide my Profile from other forum users (except Moderators)', 'weaver-for-bbpress'); ?>
            </label>
        </div>
        <?php
    }

    function wvrbbp_hide_profile($user_id)
    {
        $allow = ((bool)get_user_meta($user_id, 'wvrbbp_hide_user_profile', 1));

        return $allow;
    }


    add_action('personal_options_update', 'wvrbbp_updateHideProfile');
    add_action('edit_user_profile_update', 'wvrbbp_updateHideProfile');
    function wvrbbp_updateHideProfile($user_id)
    {
        // exclude profile.php/user-edit update
        if (is_admin()) {
            return '';
        }
        // update preference
        delete_user_meta($user_id, "wvrbbp_hide_user_profile");    // assume false, will also clean up
        if (isset($_POST['wvrbbp_profile_owner_only'])) {
            return update_user_meta($user_id, "wvrbbp_hide_user_profile", 1);
        }
        return '';
    }

endif;


// ================================= Mentions ====================================

if (wvrbbp_getopt('enable_mentions')) :

    require_once(dirname(__FILE__) . '/wvrbbp-mentions.php');

endif;    // 'enable_mentions'


// =========================================   View Counter ===================================


if (wvrbbp_getopt('enable_view_count')) :
//FILTERS NEEDED TO SHOW THE VIEWS IN THE FRONT END


    add_action('bbp_theme_after_topic_started_by', 'wvrbbp_show_views_forum_page', 60);

    function wvrbbp_show_views_forum_page()
    {

        //FORUM PAGE
        //ie the forum pages lists all the posts in the forum, let's add a view count

        $post_id = get_the_ID();

        $svc_count = get_post_meta($post_id, 'bbp_svc_viewcounts', true);
        $old_count = get_post_meta($post_id, '_wvrx_bbpe_viewcounts', true);    // compatibility with svc_viewcounts

        $count = get_post_meta($post_id, '_wvrbbp_viewcounts', true);        // get current counts

        if ($svc_count > $count) {
            $count = $svc_count;
        }
        if ($old_count > $count) {
            $count = $old_count;
        }

        if (!empty($count)) {
            echo '<br /><span class="wvrbbp - forum - list- views">' . sprintf(__("Views: %s", 'weaver-for-bbpress'), $count) . '</span>';
        }

        return;
    }


    add_filter('bbp_get_reply_post_date', 'wvrbbp_show_views_topic_page', 100, 6);

    function wvrbbp_show_views_topic_page($content, $reply_id, $humanize, $gmt, $date, $time)
    {

        $post_id = get_the_ID();

        if ($post_id != $reply_id)    // bump only for Topics, not replies
        {
            return $content;
        }

        // HITS
        $svc_count = get_post_meta($post_id, 'bbp_svc_viewcounts', true);    // compatibility with svc_viewcounts
        $count = get_post_meta($post_id, '_wvrbbp_viewcounts', true);    // get previous count
        if ($svc_count > $count) {
            $count = $svc_count;
        }

        if (!wvrbbp_getopt('view_count_only_logged') || is_user_logged_in()) {    // only count views from logged in users?
            $count++;    // bump
            update_post_meta($post_id, '_wvrbbp_viewcounts', $count);
        }

        if (!empty($count)) {
            $text = sprintf(__(" - Views: %s", 'weaver-for-bbpress'), $count);

            //SHOW THE COUNTS on topic, but not replies

            return $content . '<span class="wvrbbp - topic - list- views">' . $text . '</span>';

        }
        return $content;
    }

endif; // 'enable_view_count'

/* actions for adding ACCEPT ANSWER
 *
 * <?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>

		<?php bbp_reply_admin_links(); ?>

		<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
*/


// =================================== Add New Topic link ===================================


if (wvrbbp_getopt('enable_new_topic_link')) :    // put "Create New Topic" link at top of Forum Lists

    add_action('bbp_template_before_single_forum', 'wvrbbp_create_new_topic_link');
    function wvrbbp_create_new_topic_link()
    {
        $text = __('Create New Topic', 'weaver-for-bbpress');
        if (bbp_current_user_can_access_create_topic_form() && !bbp_is_forum_category()) {
            echo '<div class="wvrbbp-new-topic">  <a href ="#new-post">' . $text . '</a></div>';
        }
    }

endif; // 'enable_new_topic_link'


// =============== show the Trash, Spam, Private status above author icon ====================


if (wvrbbp_getopt('show_post_status')) :

    add_action('bbp_theme_before_reply_author_details', 'wvrbbp_bbp_theme_before_reply_author_details');
    function wvrbbp_bbp_theme_before_reply_author_details()
    {

        if (wvrbbp_is_private()) {
            echo '<div class="bbp-status-notification bbp-private-reply-notification"><span>' . __('Private Reply', 'weaver-for-bbpress') . '</span></div>';
        }


        if (bbp_is_reply_spam() && current_user_can('moderate')) {
            echo '<div class="bbp-status-notification bbp-spam-notification"><span>' . __('Spam', 'weaver-for-bbpress') . '</span></div>';
        }


        if (current_user_can('view_trash') && bbp_is_reply_trash()) {
            echo '<div class="bbp-status-notification bbp-trash-notification"><span>' . __('In Trash', 'weaver-for-bbpress') . '</span></div>';
        }

        // Best Answer

        if (wvrbbp_getopt('enable_best_answer')) {

            $reply_id = bbp_get_reply_id();
            $topic_id = bbp_get_topic_id();

            if ($topic_id == $reply_id) {    // really is the thread topic post
                if (wvrbbp_BestAnswer::topic_has_answer($topic_id)) {
                    echo '<div class="bbp-status-notification bbp-answer-notification"><span>' . __('Answered', 'weaver-for-bbpress') . '</span></div>';
                }
            } elseif (wvrbbp_BestAnswer::is_reply_best($reply_id)) {
                echo '<div class="bbp-status-notification bbp-answer-notification"><span>' . __('Best Answer', 'weaver-for-bbpress') . '</span></div>';
            }

        }

    }

endif;


// =================================== Add New Topic link- AVATARS ===================================

$tiny_avatar_size = wvrbbp_getopt('tiny_author_avatar_size', 14);
if (wvrbbp_getopt('show_started_by_avatar') == 'hide' ||
    wvrbbp_getopt('show_freshness_avatar') == 'hide' ||
    ($tiny_avatar_size != 14 && $tiny_avatar_size >= 10)) :    //	default tiny avatar size is 14. Might want it bigger

    add_filter('bbp_before_get_author_link_parse_args', 'wvrbbp_bbp_freshness_link_parse_args');        // the filter for the author link
    add_filter('bbp_before_get_topic_author_link_parse_args', 'wvrbbp_bbp_topic_link_parse_args');

    function wvrbbp_bbp_freshness_link_parse_args($r)
    {
        if ($r['size'] == 14)    // only fix the tiny ones
        {
            $r['size'] = wvrbbp_getopt('tiny_author_avatar_size', 14);
        }
        if (wvrbbp_getopt('show_freshness_avatar') == 'hide') {
            $r['type'] = 'name';
        }        // only the name

        return $r;
    }

    add_filter('bbp_before_get_topic_author_link_parse_args', 'wvrbbp_bbp_topic_link_parse_args');
    function wvrbbp_bbp_topic_link_parse_args($r)
    {
        if (!isset($r['size']) || $r['size'] == 14)    // only fix the tiny ones
        {
            $r['size'] = wvrbbp_getopt('tiny_author_avatar_size', 14);
        }
        if (wvrbbp_getopt('show_started_by_avatar') == 'hide') {
            $r['type'] = 'name';
        }        // only the name

        return $r;
    }

endif; // $tiny_avatar_size


// ================================ Private Reply ======================================


if (wvrbbp_getopt('enable_private_reply')) :

    require_once(dirname(__FILE__) . '/wvrbbp-private-reply.php');

endif;    // enable_private_reply


// ================================ Best Answer ======================================


if (wvrbbp_getopt('enable_best_answer')) :

    require_once(dirname(__FILE__) . '/class-weaver-best-answer.php');

// Get the class instance
    add_action('plugins_loaded', array('wvrbbp_BestAnswer', 'get_instance'));

    add_action('bbp_theme_before_topic_title', 'wvrbbp_answer_sticker');


    function wvrbbp_answer_sticker($topic_id = 0)
    {
        $topic_id = bbp_get_topic_id();
        if (wvrbbp_BestAnswer::topic_has_answer($topic_id)) {
            $label = __('[Answered]', 'weaver-for-bbpress');        // add this to be translated for possible future choice of label or icon.
            echo '<span class="dashicons dashicons-lightbulb" style="vertical-align:text-top;font-size:120%;"></span>';
        }
    }

endif;    // enable_best_answer

// =================================== Layout Options ===================================


// best options from bbp Style Pack

if (wvrbbp_getopt('forum_list_columns')) :        // ================ forum list vertical subforums =================
    function wvrbbp_sub_forum_list($args)
    {
        $args['separator'] = '<br>';

        return $args;
    }

    add_filter('bbp_before_list_forums_parse_args', 'wvrbbp_sub_forum_list');
    add_filter('bbp_before_bsp_list_forums_parse_args', 'wvrbbp_sub_forum_list');

endif;    // forum_list_columns


if (wvrbbp_getopt('hide_counts')) :                // ================ hide counts in forum list =================

    function wvrbbp_remove_counts($args)
    {
        $args['show_topic_count'] = false;
        $args['show_reply_count'] = false;
        $args['count_sep'] = '';

        return $args;
    }


    add_filter('bbp_before_list_forums_parse_args', 'wvrbbp_remove_counts');
    add_filter('bbp_before_bsp_list_forums_parse_args', 'wvrbbp_remove_counts');

endif; // hide_counts


if (wvrbbp_getopt('add_forum_description')) :    // ================ Add forum description   =================

    function wvrbbp_add_display_forum_description()
    {
        echo '<div class="wvrbbp-forum-description">';
        bbp_forum_content();
        echo '</div>';
    }

    add_action('bbp_template_before_single_forum', 'wvrbbp_add_display_forum_description', 99);
// put it after subscription, add new topic, etc.

endif;        // add_forum_description


if (wvrbbp_getopt('clear_after_breadcrumbs')) :    // ================ Add clear after breadcrumbs    =================

    add_filter('bbp_get_breadcrumb', 'wvrbbp_bbp_get_breadcrumb', 9, 3);
    function wvrbbp_bbp_get_breadcrumb($trail, $crumbs, $r)
    {
        return $trail . '<div style="clear:both;"></div>';
    }
endif;    // clear_after_breadcrumbs


// =================================== Hook in our template directory ===================================

if (true) :            // always hook these - need for user profile and style sheets
    /**
     * Set the template path for the bbPress templates.
     *
     * @since 1.0.0
     */
    function wvrbbp_get_template_path()
    {
        return wvrbbp_DIR_PATH . 'templates';
    }

    /**
     * Register the new template stack with bbPress.
     * This is really cool stuff.
     *
     * @since 1.0.0
     */
    function wvrbbp_register_theme_packages()
    {
        bbp_register_template_stack('wvrbbp_get_template_path', 8);

        if (wvrbbp_getopt('alt_style_file')) {
            bbp_register_template_stack('wvrbbp_get_css_template_path', 8);
        }
    }

    add_action('bbp_register_theme_packages', 'wvrbbp_register_theme_packages');

endif;        // hook our templates

function wvrbbp_get_css_template_path()
{
    // WP_CONTENT_DIR  // no trailing slash, full paths only

    $file = wvrbbp_getopt('alt_style_file');
    $file = str_replace(WP_CONTENT_URL, WP_CONTENT_DIR, $file);
    //$file = str_replace('/wp-content', '' , $file);	// strip the leading /wp-content
    $pat = strrchr($file, '/');    // strip off the file name
    $file = str_replace($pat, '', $file);    // I'm sure someone clever could do all this with regexp, but not me.

    //wvrbbp_alert('css template path: ' . $file);
    return $file;
}

// =================================== Fix | Subscribe ===================================

add_filter('bbp_get_topic_subscribe_link', 'wvrbbp_bbp_get_topic_subscribe_link', 9, 2);

function wvrbbp_bbp_get_topic_subscribe_link($retval, $r)
{

    if ($r['before'] == '&nbsp;|&nbsp;') {
        $retval = str_replace('&nbsp;|&nbsp;', '', $retval);
    }

    return $retval;
}


// =================================== enable_visual_editor ===================================


if (wvrbbp_getopt('enable_visual_editor')) :
    function wvrx_bbp_enable_visual_editor($args = array())
    {
        // Use this function below to only show the visual editor and not the html editor at all.
        $args['tinymce'] = true;        /* tiny MCE */
        $args['quicktags'] = true;        /* true to enable HTML Text mode */
        $args['teeny'] = false;            /* true for limited editor */
        $args['elementpath'] = false;

        return $args;
    }

    add_filter('bbp_after_get_the_content_parse_args', 'wvrx_bbp_enable_visual_editor');

    function wvrx_bbp_tinymce_paste_plain_text($plugins = array())
    {
        $plugins[] = 'paste';    // add paste plain text button

        return $plugins;
    }

    add_filter('bbp_get_tiny_mce_plugins', 'wvrx_bbp_tinymce_paste_plain_text');


    add_filter('bbp_kses_allowed_tags', 'wvrx_bbp_custom_kses_allowed_tags');
    function wvrx_bbp_custom_kses_allowed_tags($tags)
    {
        return array(
            // p
            'p' => array('class' => true, 'style' => true),
            // Links
            'a' => array('class' => true, 'href' => true, 'title' => true, 'rel' => true, 'class' => true, 'target' => true),
            // Quotes
            'blockquote' => array('cite' => true),
            // Div
            'div' => array('class' => true),
            // Span
            'span' => array('class' => true),
            // Code
            'code' => array(),
            'pre' => array('class' => true),
            // Formatting
            'em' => array(),
            'strong' => array(),
            'del' => array('datetime' => true),
            // Lists
            'ul' => array(),
            'ol' => array('start' => true),
            'li' => array(),
            // Images
            'img' => array('class' => true, 'src' => true, 'border' => true, 'alt' => true, 'height' => true, 'width' => true),
            // Tables
            'table' => array('align' => true, 'bgcolor' => true, 'border' => true),
            'tbody' => array('align' => true, 'valign' => true),
            'td' => array('align' => true, 'valign' => true),
            'tfoot' => array('align' => true, 'valign' => true),
            'th' => array('align' => true, 'valign' => true),
            'thead' => array('align' => true, 'valign' => true),
            'tr' => array('align' => true, 'valign' => true),
        );
    }
endif;    // enable_visual_editor


// =================================== Hide Admin Bar ===================================


if (wvrbbp_getopt('hide_wp_admin_bar')) :        // may not want to hider admin bar for forum users

    function wvrx_bbp_hide_admin_bar_settings()
    {
        ?>
        <style type="text/css">
            .show-admin-bar {
                display: none !important;
            }
        </style>
        <?php
    }

    function wvrx_bbp_disable_admin_bar()
    {
        $min_role = wvrbbp_getopt('hide_wp_admin_bar'); // bbp_keymaster, bbp_participant, bbp_moderator

        if ($min_role == 'all' || !current_user_can($min_role))    // change this to allow editors to see the admin bar
        {
            add_filter('show_admin_bar', '__return_false');
            add_action('admin_print_scripts-profile.php', 'wvrx_bbp_hide_admin_bar_settings');
        }
    }

    add_action('init', 'wvrx_bbp_disable_admin_bar', 9);

endif;    // ****************** hide admin bar


// =================================== Style Sheets ===================================


// replace style sheet with ours

if (wvrbbp_getopt('theme', 'bbpress-enhanced') != 'bbpress0') :    // don't replace if bbpress0 0 - the raw default
    add_filter('bbp_default_styles', 'wvrbbp_bbp_default_styles');

    function wvrbbp_bbp_default_styles($styles)
    {
        /*
            // LTR
            $styles['bbp-default'] = array(
                'file'         => 'css/bbpress.css',
                'dependencies' => array()
            );
            // RTL helpers
            if ( is_rtl() ) {
                $styles['bbp-default-rtl'] = array(
                    'file'         => 'css/bbpress-rtl.css'
                    'dependencies' => array( 'bbp-default' )
            );
            */
        $theme = wvrbbp_getopt('theme', 'bbpress-enhanced');

        if ($theme == 'use_alt_style_file') {    // a start...

            $styles['bbp-default']['file'] = strrchr(wvrbbp_getopt('alt_style_file'), '/');        // must be in media lib

        } else {
            $styles['bbp-default']['file'] = 'css/' . $theme . '.css';
        }


        $styles['bbp-default']['dependencies'] = array();
        if (is_rtl()) {
            $styles['bbp-default-rtl']['file'] = 'css/bbpress-rtl.css';    // need our new rtl rules, too
            $styles['bbp-default-rtl']['dependencies'] = array('bbp-default');
        }

        return $styles;
    }
endif; // theme


//======================================== lib functions used by multiple features

function wvrbbp_is_private($reply_id = 0)
{
    // Determines if a reply is marked as private

    $retval = false;

    // Checking a specific reply id
    if (!empty($reply_id)) {
        $reply = bbp_get_reply($reply_id);
        $reply_id = !empty($reply) ? $reply->ID : 0;

        // Using the global reply id
    } elseif (bbp_get_reply_id()) {
        $reply_id = bbp_get_reply_id();

        // Use the current post id
    } elseif (!bbp_get_reply_id()) {
        $reply_id = get_the_ID();
    }

    if (!empty($reply_id)) {
        $retval = get_post_meta($reply_id, '_bbp_reply_is_private', true);
    }

    return (bool)apply_filters('bbp_reply_is_private', (bool)$retval, $reply_id);

}

//======================================== CLOSED TOPICS, FORUMS =============================

add_filter('bbp_get_topic_title', 'wvrbbp_get_topic_title', 10, 2);
function wvrbbp_get_topic_title($title, $topic_id)
{
    // add decrations to topic titles - closed, favorited, subscribed
    $pre = '';
    $post = '';
    if (bbp_is_topic_closed($topic_id)) {
        $pre .= '<span class="dashicons dashicons-lock" style="font-size:90%;vertical-align:middle;"></span> ';
    }

    $user_id = bbp_get_user_id(0, true, true);    // Changed 3.1.3 for bbPress 2.6 compatibility

    if (bbp_is_user_favorite($user_id, $topic_id) && !wvrbbp_getopt('hide_fav_sub')) {
        $post .= ' <span class="dashicons dashicons-heart wvrbbp-fav-sub" style="font-size:80%;vertical-align:middle;"></span>';
    }

    if (bbp_is_user_subscribed_to_topic($user_id, $topic_id) && !wvrbbp_getopt('hide_fav_sub')) {
        $post .= ' <span class="dashicons dashicons-visibility wvrbbp-fav-sub" style="font-size:80%;vertical-align:middle;"></span>';
    }

    return $pre . $title . $post;
}

function wvrbbp_is_topic_unapproved($topic_id = 0)
{

    // Get topic
    $topic = bbp_get_topic($topic_id);
    if (empty($topic)) {
        return false;
    }

    return $topic->post_status == bbp_get_pending_status_id();
}


add_filter('bbp_get_forum_title', 'wvrbbp_get_forum_title', 10, 2);
function wvrbbp_get_forum_title($title, $forum_id)
{
    // add decrations to forum titles - closed, favorited, subscribed
    $pre = '';
    $post = '';
    if (bbp_is_forum_closed($forum_id)) {
        $pre .= '<span class="dashicons dashicons-lock" style="font-size:90%;vertical-align:middle;"></span> ';
    }

    $user_id = bbp_get_user_id(0, true, true);    // Changed 3.1.3 for bbPress 2.6 compatibility
    if (bbp_is_user_subscribed_to_forum($user_id, $forum_id) && !wvrbbp_getopt('hide_fav_sub')) {
        $post .= ' <span class="dashicons dashicons-visibility wvrbbp-fav-sub" style="font-size:80%;vertical-align:middle;"></span>';
    }

    return $pre . $title . $post;
}


// =================================== Login Widget ===================================


if (wvrbbp_getopt('logged_in_message')) :

    add_filter('bbp_login_widget_title', 'wvrbbp_login_widget_title', 10, 3);

    function wvrbbp_login_widget_title($title, $instance, $id_base)
    {
        $alt = wvrbbp_getopt('logged_in_message');

        if (is_user_logged_in() && $alt) {
            return $alt == 'hide' ? '' : $alt;    // make blank just in case theme has weird widget classes
        }

        return $title;
    }
endif;

if (wvrbbp_getopt('logout_widget_msg') || wvrbbp_getopt('logout_show_time')) :    // add logout box message

    add_filter('bbp_get_logout_link', 'wvrbbp_get_logout_link', 9, 2);

    function wvrbbp_get_logout_link($link, $redirect)
    {
        //return $link;

        $time = '';
        if (wvrbbp_getopt('logout_show_time')) {
            $t = current_time(get_option('time_format'));
            $time = '<div class="wvrbbp-logout-time"><span class="dashicons dashicons-clock"></span> ' . $t . '</div>';
        }

        $retval = $time . '<div class="wvrbbp-pre-logout-link"">'
            . wvrbbp_getopt('logout_widget_msg') . '</div><div class="wvrbbp-logout-link">' . $link . '</div>';

        return $retval;
    }
endif;    // logout_widget_msg


// =================================== Redirect Registration/Lost Password =====================================

if (wvrbbp_getopt('register_page')) :

    /*
     * The 'login_url' filter can be used to redirect to an alternate page if the
     * user clicks "login" on the widget without filling in a name or password
     *
     */

    //add_filter( 'login_url', 'wvrbbp_login_page', 10, 3 );
    //function wvrbbp_login_page( $login_url, $redirect, $force_reauth ) {
    //	wp_redirect( home_url( wvrbbp_getopt('register_page') ) );
    //	exit(); 		// always call `exit()` after `wp_redirect`
    //}

    // intercept the Register and Lost your password? links from the standard WP login screen

    add_filter('register_url', 'wvrbbp_redirect_login_register');
    //add_filter('lostpassword_url','wvrbbp_redirect_login_register');

    function wvrbbp_redirect_login_register($reg_url)
    {
        return home_url(wvrbbp_getopt('register_page'));
    }

    /**
     * Redirects visitors to `wp-login.php?action=register` to
     * `site.com/registration-page-name`
     */

    //add_action( 'login_form_register', 'wvrbbp_catch_register' );
    //function wvrbbp_catch_register()
    //{
    //	wp_redirect( home_url( wvrbbp_getopt('register_page') ) );
    //	exit();
    //}

    /*
     * intercept bbforum.me/wp-login.php?action=lostpassword and
     *           bbforum.me/wp-login.php?action=retrievepassword
     *
     */

    //add_action( 'login_form_lostpassword', 'wvrbbp_filter_option' );
    //add_action( 'login_form_retrievepassword', 'wvrbbp_filter_option' );
    /**
     * Simple wrapper around a call to add_filter to make sure we only
     * filter an option on the login page.
     */
    //function wvrbbp_filter_option()
    //{
    //	wp_redirect( home_url( wvrbbp_getopt('register_page') ) );
    //	exit();
    //}

endif;


// =================================== Block on Spam =====================================

if (wvrbbp_getopt('enable_auto_block')) :

// Auto set author role to Blocked when a topic or reply is marked as spam. Change back to Participant if UNSPAM.

    add_action('bbp_spammed_topic', 'wvrbbp_spammed_topic');

    function wvrbbp_spammed_topic($topic_id)
    {
        $author_id = bbp_get_topic_author_id($topic_id);
        $blocked = bbp_get_blocked_role();
        bbp_set_user_role($author_id, $blocked);    // block the user from now on
    }

    add_action('bbp_unspammed_topic', 'wvrbbp_unspammed_topic');

    function wvrbbp_unspammed_topic($topic_id)
    {
        $author_id = bbp_get_topic_author_id($topic_id);
        $role = bbp_get_participant_role();
        bbp_set_user_role($author_id, $role);    // block the user from now on
    }

    add_action('bbp_spammed_reply', 'wvrbbp_spammed_reply');

    function wvrbbp_spammed_reply($reply_id)
    {
        $author_id = bbp_get_reply_author_id($reply_id);
        $blocked = bbp_get_blocked_role();
        bbp_set_user_role($author_id, $blocked);    // block the user from now on
    }

    add_action('bbp_unspammed_reply', 'wvrbbp_unspammed_reply');

    function wvrbbp_unspammed_reply($reply_id)
    {
        $author_id = bbp_get_reply_author_id($reply_id);
        $role = bbp_get_participant_role();
        bbp_set_user_role($author_id, $role);    // block the user from now on
    }

endif;


// =================================== New Topic Notice ===================================

if (wvrbbp_getopt('new_topic_msg')) :
    add_action('bbp_theme_before_topic_form_notices', 'wvrbbp_theme_before_topic_form_notices');

    function wvrbbp_theme_before_topic_form_notices()
    {
        echo wvrbbp_getopt('new_topic_msg');
    }
endif;


// =================================== Topic Resolution ===================================


if (wvrbbp_getopt('enable_resolution')) :            // Resolutions

    require_once(dirname(__FILE__) . '/wvrbbp-resolve.php');

endif;


// =================================== Custom CSS ===================================


add_action('wp_head', 'wvrbbp_css_wp_head', 25);

function wvrbbp_css_wp_head()
{

    $css = '';

    if (wvrbbp_getopt('hide_contains_descriptions')) {
        $css .= "\n#bbpress-forums div.bbp-template-notice.info{display:none;}";
    }

    if (wvrbbp_getopt('hide_account_ability')) {
        $css .= "\n#bbpress-forums .bbp-reply-form .bbp-template-notice,
#bbpress-forums .bbp-topic-form .bbp-template-notice{display:none;}
#bbpress-forums .bbp-reply-form .bbp-template-notice.error,
#bbpress-forums .bbp-topic-form .bbp-template-notice.error,
#bbpress-forums .bbp-reply-form .bbp-template-notice.warning,
#bbpress-forums .bbp-topic-form .bbp-template-notice.warning{display:block;}";
    }

    if (wvrbbp_getopt('hide_small_avatars')) {
        $css .= "\n.bbp-template-notice .bbp-author-avatar img,
.bbp-reply-revision-log .bbp-reply-revision-log-item img{display:none;}";
    }

    if (wvrbbp_getopt('round_avatars')) {
        $css .= "\n#bbpress-forums img.avatar,
.bbp_widget_login img.avatar{border-radius:50%;}";
    }

    if (wvrbbp_getopt('no_even_odd')) {
        $css .= "\n.forum #bbpress-forums div.even,
#bbpress-forums ul.even,
.forum #bbpress-forums div.odd,
#bbpress-forums ul.odd,
#bbpress-forums .status-trash.even,
#bbpress-forums .status-spam.even,
#bbpress-forums .status-trash.odd,
#bbpress-forums .status-spam.odd {
	background-color: inherit;
}";
    }

    if (wvrbbp_getopt('hide_voices')) {        // hide voices column (give topic title the 10% back)
        $css .= "\n@media only screen and (min-width: 768px){#bbpress-forums .bbp-topics .bbp-topic-voice-count {display:none;}
#bbpress-forums li.bbp-topic-title {width:60%;}
#bbpress-forums li.bbp-topic-freshnes{width:27%}";
    }

    if (wvrbbp_getopt('author_shadow')) {
        $css .= "\n#bbpress-forums .bbp-body .bbp-reply-author{border: 1px solid #bbb;box-shadow: 0px 0px 3px 2px rgba(0,0,0,.2)}";
    }

    $tiny_avatar_size = wvrbbp_getopt('tiny_author_avatar_size', 14);        // adjust the tiny started by and freshness author avatars

    if ($tiny_avatar_size != 14 && $tiny_avatar_size >= 10) {
        $margin = (int)($tiny_avatar_size / 2) - 7;
        $css .= "\n#bbpress-forums .bbp-topic-started-by img.avatar,
#bbpress-forums .bbp-topic-freshness-author img.avatar{width:{$tiny_avatar_size}px !important;height:{$tiny_avatar_size}px !important;margin-bottom:-{$margin}px !important;}";
    }

    /* browser note: Chrome and Edge work nicely if you float the freshness author name as well as the avatar,
     * but Firefox and Safari render those wrong, so we just leave theme with no float.
     */

    /*
     <p class="bbp-topic-meta">
        <span class="bbp-topic-started-by">Started by:
            <a href="PROFILE URL" title="profile" class="bbp-author-avatar" rel="nofollow">
                <img alt='' src='GRAVATAR URL' srcset='SRCSET' class='avatar avatar-28 photo' height='28' width='28' />
            </a>&nbsp;<a href="PROFILE URL" title="profile" class="bbp-author-name" rel="nofollow">AUTHOR</a>
        </span>
        <br><span class="wvrbbp-forum-list-views">Views: 515</span>
    </p>
     */

    $show = wvrbbp_getopt('show_started_by_avatar');        // how to float the avatars - none is default.

    if ($show == 'left' || $show == 'right') {
        if (is_rtl()) {
            $margin = ($show == 'right') ? 'margin-left:.5em !important;' : '';
        } else {
            $margin = ($show == 'left') ? 'margin-right:.5em !important;' : '';
        }
        $css .= "\n#bbpress-forums .bbp-topic-started-by img.avatar{float:{$show} !important;{$margin}}";
    }

    /*
    <p class="bbp-topic-meta">
        <span class="bbp-topic-freshness-author">
            <a href="AUTHOR URL" title="profile" class="bbp-author-avatar" rel="nofollow">
                <img alt='' src='GRAVATAR URL' srcset='SRCSET' class='avatar avatar-32 photo' height='32' width='32' />
            </a>&nbsp;<a href="AUTHOR URL" title="profile" class="bbp-author-name" rel="nofollow">AUTHOR</a>
        </span>
    </p>
     */

    $show = wvrbbp_getopt('show_freshness_avatar');
    if ($show == 'left') {
        if (is_rtl()) {
            $margin = 'margin-right:.5em !important;';
        } else {
            $margin = 'margin-right:.5em !important;';
        }
        $css .= "\nli.bbp-forum-freshness, li.bbp-topic-freshness {text-align: left !important;}
#bbpress-forums .bbp-topic-freshness-author img.avatar{float:{$show} !important;{$margin}}";


    } elseif ($show == 'right') {
        if (is_rtl()) {
            $margin = 'margin-left:.5em !important;';
        } else {
            $margin = 'margin-left:.5em !important;';
        }
        $css .= "\nli.bbp-forum-freshness, li.bbp-topic-freshness {text-align: right !important;}
#bbpress-forums .bbp-topic-freshness-author img.avatar{float:{$show} !important;{$margin}}";
    }

    if (wvrbbp_getopt('logged_in_message') == 'hide' && is_user_logged_in()) {
        $css .= "\n.bbp_widget_login .widget-title{display:none !important;}";
    }


    $base = (int)wvrbbp_getopt('base_font_size');
    if ($base) {
        $css .= "\n#bbpress-forums {font-size:{$base}px;}";
    }

    $css .= wvrbbp_getopt('custom_css');        // add custom after options so that custom overrides

    if ($css) {
        $esc_css = esc_html($css);
        $content = str_replace('&gt;', '>', $esc_css); // put these back
        $content = str_replace('&lt;', '<', $content); // put these back
        echo $css = "\n<style type='text/css'> /* Turnkey bbPress */\n" . $content . "\n</style>\n";
    }
}

