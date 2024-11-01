<?php
// this code adapted from the bbp mentions plugin.


// add profile edit field
add_action('bbp_user_edit_after_contact', 'wvrbbp_peditField');
// hook into profile edit update
add_action('personal_options_update', 'wvrbbp_updateProfile');
// hook into profile edit update: when updating other users' profiles
add_action('edit_user_profile_update', 'wvrbbp_updateProfile');
// notify mentioned users
add_action('bbp_edit_topic_post_extras', 'wvrbbp_mentionsCheck');
add_action('bbp_edit_reply_post_extras', 'wvrbbp_mentionsCheck');
add_action('bbp_new_topic_post_extras', 'wvrbbp_mentionsCheck');
add_action('bbp_new_reply_post_extras', 'wvrbbp_mentionsCheck');


/** user preferences **/
function wvrbbp_canNotify($user_id)
{
    $allow = !((bool)get_user_meta($user_id, 'wvrbbp_mute', 1));

    return apply_filters("wvrbbp_can_notify", $allow, $user_id);
}

/** add field to bbp edit **/
function wvrbbp_peditField()
{
    ?>
    <div>
        <label for=""><?php echo apply_filters('wvrbbp_pedit_field_header', "Email notifications"); ?></label>
        <label>
            <input type="checkbox" name="wvrbbp_notify"
                   style="width: auto;" <?php checked(wvrbbp_canNotify(bbp_get_displayed_user_field('ID'))); ?> /> <?php echo wvrbbp_getopt('mention_notify'); ?>
        </label>
    </div>
    <?php
}

/** hook into profile update **/
function wvrbbp_updateProfile($user_id)
{
    // exclude profile.php/user-edit update
    if (is_admin()) {
        return '';
    }
    // update preference
    if (isset($_POST['wvrbbp_notify'])) {
        return delete_user_meta($user_id, "wvrbbp_mute");
    } else {
        return update_user_meta($user_id, "wvrbbp_mute", time());
    }
}

/** hook into posts to notify **/
function wvrbbp_mentionsCheck($post_id)
{
    if (!function_exists('bbp_find_mentions') || !isset($post_id)) {
        return '';
    }
    $post = get_post($post_id);
    $mentions = bbp_find_mentions($post->post_content);
    if (!$mentions || !$post->ID) {
        return '';
    }
    // get previous notified users (to avoid notifying more than once)
    $notified = get_post_meta($post->ID, "wvrbbp_notified", 1);
    if (!$notified || !is_array($notified)) {
        $notified = array();
    }
    foreach ($mentions as $slug) {
        // get mentioned user data
        $user = get_user_by('slug', $slug);
        // exclude false mentions
        if (isset($user->ID) && $user->ID) {
            // preference check
            if (!wvrbbp_canNotify($user->ID)) {
                continue;
            }
            // notified before
            if ($notified && in_array($user->ID, $notified)) {
                continue;
            }
            // notify and push into meta
            if (wvrbbp_notify($user, $post)) {
                $notified[] = $user->ID;
                // trigger hook
                do_action("wvrbbp_post_notify_user", $user, $post);
            }
        }
    }

    // push notified users to meta
    return update_post_meta($post->ID, "wvrbbp_notified", $notified);
}


/** process notifications **/
function wvrbbp_notify($user, $post)
{
    if (!isset($user->ID) && is_numeric($user)) {
        $user = get_userdata($user);
    }
    // check user
    if (!$user->ID) {
        return '';
    }

    if (!isset($post->ID) && is_numeric($post)) {
        $post = get_post($post);
    }
    // check post
    if (!$post->ID) {
        return '';
    }

    // author data
    $author = get_userdata($post->post_author);

    // pattern replace data
    $patternData = array();
    $patternData['[user-name]'] = $user->display_name;
    $patternData['[user-link]'] = bbp_get_user_profile_url($user->ID);
    $patternData['[user-edit-profile-link]'] = bbp_get_user_profile_url($user->ID) . 'edit/';
    $patternData['[author-name]'] = $author->display_name;
    $patternData['[post-title]'] = apply_filters("the_title", $post->post_title, $post->ID);
    $patternData['[post-link]'] = bbp_get_reply_url(bbp_get_reply_id($post->ID));                        // fixed Version .4
    $patternData['[post-content]'] = trim($post->post_content);
    $patternData['[post-date]'] = $post->post_date;
    $patternData['[post-type]'] = $post->post_type;
    $patternData['[site-name]'] = get_bloginfo('name');
    $patternData['[site-login-link]'] = wp_login_url();

    $notification = array();
    //subject
    $notification['subject'] = str_replace(
        array_keys($patternData),
        $patternData,
        wvrbbp_getopt('email_subject')
    );
    // body
    $notification['body'] = str_replace(
        array_keys($patternData),
        $patternData,
        wvrbbp_getopt('email_body')
    );
    // email
    $notification['email'] = $user->user_email;
    // pluggable
    $notification = apply_filters("wvrbbp_notification", $notification, $user, $post, $patternData);
    // trigger hook
    do_action("wvrbbp_pre_mail", $notification, $user, $post, $patternData);
    // send the mail
    if (false) { // @@@@@@@@@@@ for debugging make true
        wvrbbp_alert('email: ' . $notification['email'] . ' subject: ' . $notification['subject'] . ' body: ' . $notification['body']);

        return true;
    } else {
        return (bool)wp_mail(
            $notification['email'],
            $notification['subject'],
            $notification['body']
        );
    }
}

