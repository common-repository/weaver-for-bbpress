<?php
// ======================================================== m admin ===============================
function wvrbbp_members_admin()
{
    // wvrbbp_save_members_opts
    ?>
    <h2 style="color:blue;"><?php _e('Member Options', 'weaver-for-bbpress'); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="wvrbbp_save_member_opts"
               value="<?php _e('Member Options Saved', 'weaver-for-bbpress'); ?>"/>
        <input style="display:none;" type="submit" name="atw_stop_enter" value="Ignore Enter"/>

        <?php

        wvrbbp_define_members();            // define content display options
        wvrbbp_save_members_button();

        wvrbbp_nonce_field('wvrbbp_save_member_opts');

        ?>

    </form>
    <hr/>
    <?php
}

function wvrbbp_save_members_button()
{
    ?>
    <input style="margin-bottom:5px;" class="button-primary" type="submit" name="wvrbbp_save_member_opts"
           value="<?php _e('Save Member Options', 'weaver-for-bbpress'); ?>"/>

    <?php
}


// ========================================= >>> wvrbbp_define_members <<< ===============================

function wvrbbp_define_members()
{
    // define display filter options
    // need to add each option value to wvrbbp_save_members_opts in wvrbbp-admin-top.php

// site email stuff

    ?>

    <h3><u><?php _e('Member Options', 'weaver-for-bbpress'); ?></u></h3>
    <div class="wvrx-opts-section">
        <div class="wvrx-opts-title">
            &bull; <?php _e('Member Settings', 'weaver-for-bbpress'); ?> <span
                    class="wvrx-opts-title-description"> <?php _e('Set Member Interface Options', 'weaver-for-bbpress'); ?></span>
        </div>
        <div class="wvrx-opts-opts">
            <?php
            wvrbbp_checkbox('enable_private_reply', __('Enable: <em><strong>Private Replies</strong></em> readable only by Moderators and topic originator. (Styled with .bbp-private-reply)', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('enable_best_answer', __('Enable: <em><strong>Best Answer</strong></em> - allows Moderators and topic originator to accept one reply as best answer to topic. ', 'weaver-for-bbpress'), '<br /><br />');
            ?>

            <div class="wvrx-opts-title" style="margin-left:-1em;">
                &bull; <?php _e('Profile', 'weaver-for-bbpress'); ?> <span
                        class="wvrx-opts-title-description"><?php _e('User Profile Options', 'weaver-for-bbpress'); ?></span>
            </div>

            <?php
            wvrbbp_checkbox('enable_profile_options', __('Enable: <em><strong>User Profile Options</strong></em>. Enable support for following User Profile options.', 'weaver-for-bbpress'), '<br /><br />');


            $visibility = wvrbbp_getopt('profile_visibility'); ?>

            <strong style="display:inline;padding-left:2.5em;text-indent:-1.7em;"><?php _e('User Profile Visibility:', 'weaver-for-bbpress'); ?></strong>
            <select name='profile_visibility'>
                <option value="all" <?php selected($visibility == 'all'); ?>><?php _e('Everyone, including visitors', 'weaver-for-bbpress'); ?></option>
                <option value="loggedin" <?php selected($visibility == 'loggedin'); ?>><?php _e('Must be logged in', 'weaver-for-bbpress'); ?></option>
                <option value="own" <?php selected($visibility == 'own'); ?>><?php _e('Owner only', 'weaver-for-bbpress'); ?></option>
                <option value="own_and_moderator" <?php selected($visibility == 'own_and_moderator'); ?>><?php _e('Owner plus Moderators', 'weaver-for-bbpress'); ?></option>
                <option value="admin_moderator" <?php selected($visibility == 'admin_moderator'); ?>><?php _e('Only Admin and Moderators', 'weaver-for-bbpress'); ?></option>
                <option value="admin" <?php selected($visibility == 'admin'); ?>><?php _e('Only Admin</option>', 'weaver-for-bbpress'); ?></option>
            </select>
            &nbsp;&nbsp;<small><?php _e('Admins can always see all profiles.', 'weaver-for-bbpress'); ?></small>
        </div>

        <div style="clear:both;"></div>
        <div class="wvrx-opts-description">
            <hr/>
            <p>
                <?php _e('Options to enhance Member features.', 'weaver-for-bbpress'); ?>
            </p>
        </div>
    </div>

    <?php wvrbbp_save_members_button(); ?>


    <h3><u>Mentions Options</u></h3>

    <div class="wvrx-opts-section">
        <div class="wvrx-opts-title">
            &bull; <?php _e('Mentions Settings', 'weaver_for_bbpress'); ?> <span
                    class="wvrx-opts-title-description"> <?php _e('Define the content of @mention messages.', 'weaver_for_bbpress'); ?></span>
        </div>

        <div class="wvrx-opts-opts">
            <br/>
            <?php
            wvrbbp_checkbox('enable_mentions', __('<strong>Enable mentions</strong> - When enabled, users mentioned with <span style="color:blue">@username</span> in topics or replies will be notified by email.', 'weaver_for_bbpress'), '<br /><br />');

            wvrbbp_textarea('email_subject', __('Enter a subject-line for the notification email', 'weaver_for_bbpress'),
                '<br /><br />', 60, 2, __('[author-name] mentioned you on their forum post', 'weaver_for_bbpress'));

            wvrbbp_textarea('email_body', __('Enter a body for the notification email', 'weaver_for_bbpress'),
                '<br /><br />', 60, 5,
                "Dear [user-name],

[author-name] has just mentioned you on their [post-type]: ( [post-title] ).

[post-content]

Read this post on the forums: [post-link]

To update your preferences, please visit your profile edit page.");

            wvrbbp_textarea('mention_notify', __('User Profile option label to enable mention notification.', 'weaver_for_bbpress'),
                '<br />', 60, 1, __('Notify me whenever my name is mentioned on the forums', 'weaver_for_bbpress'));
            ?>
        </div>

        <div style="clear:both;"></div>
        <div class="wvrx-opts-description">
            <br/>
            <p>
                <?php _e('<strong>You can format the subject and email body with the following patterns:</strong>
<div style="padding-left:1em;">
[user-name]: mentioned user name</br>
[user-link]: mentioned user profile link</br>
[user-edit-profile-link]: mentioned user profile edit link</br>
[author-name]: name of the user who mentions the target user (i.e topic/reply editor)</br>
[post-title]: topic/reply title</br>
[post-link]: topic or reply link</br>
[post-content]: topic or reply content text</br>
[post-date]: topic/reply publish date</br>
[post-type]: type: topic or reply</br>
[site-name]: site name</br>
[site-login-link]: login URL</br>
</div>', 'weaver_for_bbpress'); ?>

            </p>
        </div>

    </div>
    <?php
}

