<?php
// ======================================================== mentions admin ===============================
function wvrbbp_admin_users_admin()
{
    ?>
    <h2 style="color:blue;"><?php _e('Admin &amp; User Options', 'weaver-for-bbpress'); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="wvrbbp_save_user_options" value="Mentions Options Saved"/>
        <input style="display:none;" type="submit" name="atw_stop_enter" value="Ignore Enter"/>

        <?php

        wvrbbp_define_admin_users();            // define content display options
        wvrbbp_save_admin_users_button();

        wvrbbp_nonce_field('wvrbbp_save_user_options');
        ?>

    </form>
    <hr/>
    <?php
}

function wvrbbp_save_admin_users_button()
{
    ?>
    <input style="margin-bottom:5px;" class="button-primary" type="submit" name="wvrbbp_save_user_options"
           value="<?php _e("Save Admin &amp; User Options", 'weaver-for-bbpress'); ?>"/>
    <?php
}


// ========================================= >>> wvrbbp_define_mentions <<< ===============================

function wvrbbp_define_admin_users()
{
    // need to add each option value to wvrbbp_save_form_options in wvrbbp-admin-top.php
    ?>
    <h3><u>Set Site Email Name and Address</u></h3>
    <div class="wvrx-opts-section">
        <div class="wvrx-opts-title">
            &bull; <?php _e("eMail Settings", 'weaver_for_bbpress'); ?> <span
                    class="wvrx-opts-title-description"> <?php _e("Use this site name and site email address for emails sent out by your forum.", 'weaver_for_bbpress'); ?></span>
        </div>

        <div class="wvrx-opts-opts">
            <?php
            wvrbbp_textarea('email_name', __('Name used for Email Sender From field.', 'weaver_for_bbpress'));
            wvrbbp_textarea('email_address', __('Email address to use for your site.', 'weaver_for_bbpress'));
            ?>
        </div>

        <div style="clear:both;"></div>
        <div class="wvrx-opts-description">
            <p>
                <?php _e('Replace the default WordPress "From" name (Word Press) and sender email address. Leave blank to use WP defaults.', 'weaver_for_bbpress'); ?>
            </p>
        </div>
    </div>


    <?php wvrbbp_save_admin_users_button(); ?>


    <h3><u><?php _e('Interface Options', 'weaver-for-bbpress'); ?></u></h3>
    <div class="wvrx-opts-section">

        <div class="wvrx-opts-title">
            &bull; <?php _e('Interface Options', 'weaver-for-bbpress'); ?> <span
                    class="wvrx-opts-title-description"><?php _e('Control User Interface Options', 'weaver-for-bbpress'); ?></span>
        </div>

        <div class="wvrx-opts-opts">
            <?php
            wvrbbp_checkbox('enable_visual_editor', __('Enable: <em><strong>Visual Editor</strong></em>. Enable WP tinyMCE editor. We also recommend the <a href="//wordpress.org/plugins/wp-edit/" target="_blank"><em>WP Edit</em></a> plugin to customize the Visual Editor buttons.', 'weaver-for-bbpress'), '<br /><br />');
            wvrbbp_checkbox('enable_resolution', __('Enable: <em><strong>Topic Resolution</strong></em>. Enable adding Topic Resolution status: Resolved, Not Resolved, Not a Question, or none.', 'weaver-for-bbpress'), '<br />');

            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>'
                . __('(Accepted Answer option has priority and sets status to Answered)', 'weaver-for-bbpress') . '</small><br />';

            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

            wvrbbp_checkbox('use_resolve_icons', __('<em><strong>Use Icons for Resolution Status</strong></em>. Use icons to indicate status: Resolved, Not Resolved, Investigating.', 'weaver-for-bbpress'), '<br /><br />');


            wvrbbp_checkbox('show_post_status', __('Enable: <em><strong>Post Status Indicator</strong></em>. Status displayed above post author info. Private Reply for all users, Trash and Spam for moderators. <strong>Highly recommended</strong>.', 'weaver-for-bbpress'), '<br /><br />');


            wvrbbp_checkbox('enable_email_link', __('Enable: <em><strong>Email Link</strong></em> in Profile. Enable display of "mailto:" link in profiles for Moderators. Does not show to Participants.', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('enable_auto_block', __('Enable: <em><strong>Auto-Block Spam Author</strong></em>. Automatically change role of author to <em>Blocked</em> when a topic or reply is marked as SPAM. Will auto reset to <em>Participant</em> if a topic or reply is UNSPAMmed. Will <em>not</em> automatically mark other topics by same author as SPAM.', 'weaver-for-bbpress'), '<br /><br />');
            ?>
        </div>

        <?php
        $hide_bar = wvrbbp_getopt('hide_wp_admin_bar'); ?>

        <strong style="display:inline;padding-left:2.5em;text-indent:-1.7em;"><?php _e('Hide WP Admin Bar for:', 'weaver-for-bbpress'); ?></strong>
        <select name='hide_wp_admin_bar'>
            <option value="" <?php selected($hide_bar == ''); ?>><?php _e('None - show for all logged in users', 'weaver-for-bbpress'); ?></option>
            <option value="all" <?php selected($hide_bar == 'all'); ?>> <?php _e('All users', 'weaver-for-bbpress'); ?></option>
            <option value="bbp_keymaster" <?php selected($hide_bar == 'bbp_keymaster'); ?>><?php _e('Moderators and Participants', 'weaver-for-bbpress'); ?></option>

            <option value="bbp_moderator" <?php selected($hide_bar == 'bbp_moderator'); ?>><?php _e('Participants', 'weaver-for-bbpress'); ?></option>
        </select>
        &nbsp;&nbsp;<small><?php _e("You probably don't want Participants to see the admin bar. Visitors not logged in will never see admin bar.", 'weaver-for-bbpress'); ?></small>

        <br/><br/>
        <div class="wvrx-opts-title">
            &bull; <?php _e('bbPress Moderation', 'weaver-for-bbpress'); ?> <span
                    class="wvrx-opts-title-description"><?php _e('Control the bbPress Topic and Reply content moderation.', 'weaver-for-bbpress'); ?></span>
        </div>

        <div class="wvrx-opts-opts">
            <br/>
            <?php wvrbbp_checkbox('disable_moderation', __('Disable: <em><strong>Native bbPress Moderation</strong></em>. This prevents silent <em>Pending</em> status for user posts and replies new to bbPress 2.6.',
                'weaver-for-bbpress'),
                '<br />'); ?>
            <div style="display:inline;padding-left:5em;">
                <?php
                _e('You can also fine tune post/reply moderation directly in the <em>Dashboard &rarr; Settings &rarr; Discussion &rarr; Comment Moderation</em> option instead of disabling bbPress moderation here.',
                    'weaver-for-bbpress')
                ?>
            </div>
            <br/><br/>
        </div>

        <br/><br/>
        <div class="wvrx-opts-title">
            &bull; <?php _e('Login Widget', 'weaver-for-bbpress'); ?> <span
                    class="wvrx-opts-title-description"><?php _e('Enhance the bbPress Login Widget', 'weaver-for-bbpress'); ?></span>
        </div>

        <div class="wvrx-opts-opts">
            <?php wvrbbp_textarea('logged_in_message', __('Alternate widget title after logged in. Enter "hide" to hide title.', 'weaver-for-bbpress')); ?>
            <?php wvrbbp_textarea('logout_widget_msg', __('Add message in smaller font above Log Out button (e.g., "Click your user name to edit your profile.")', 'weaver-for-bbpress')); ?>
            <br/>
            <?php wvrbbp_checkbox('logout_show_time', __('Show current time below user name on Logout widget.', 'weaver-for-bbpress'), '<br /><br />'); ?>
        </div>


        <div class="wvrx-opts-title">
            &bull; <?php _e('Redirect Register links.', 'weaver-for-bbpress'); ?>
            <span class="wvrx-opts-title-description"><?php _e('Force use of site-specific Registration forms.', 'weaver-for-bbpress'); ?></span>
        </div>
        <p style="margin-left:3em;">
            <?php _e('It is best for a bbPress site to allow new user Registration only from site designed pages using the bbPress <em>[bbp-register]</em> shortcode, usually in conjunction the bbPress Login widget or site custom login page. This ensures proper forum participation roles, and prevents hackers and spammers from using wp-signup.php to directly register for your site. We highly recommend building such a WP page for a consistent user experience, and to prevent unwanted spam accounts.', 'weaver-for-bbpress'); ?>
        </p>
        <div class="wvrx-opts-opts">
            <?php wvrbbp_textarea('register_page', __("Page for site Register form. (Don't include site home URL, e.g., just <em>/site-login</em> for example.)", 'weaver-for-bbpress')); ?>
        </div>

        <br/><br/>

        <div class="wvrx-opts-title">
            &bull; <?php _e('Create New Topic Instructions', 'weaver-for-bbpress'); ?> <span
                    class="wvrx-opts-title-description"></span>
        </div>

        <div style="margin-top:5px;display:inline-block;padding-left:4em;text-indent:-1.7em;"><label>

                <textarea style="margin-bottom:-8px;" cols="72" rows="3" maxlength="1023"
                          name="new_topic_msg"><?php echo wp_kses_post(wvrbbp_getopt('new_topic_msg')); ?></textarea>
                &nbsp;&nbsp;


                <?php _e('Optional "Create New Topic" posting instructions when user creates new topic. Can include HTML.', 'weaver-for-bbpress'); ?>

            </label></div>

        <br/><br/>


        <div class="wvrx-opts-title">
            &bull; <?php _e('Other', 'weaver-for-bbpress'); ?> <span class="wvrx-opts-title-description"></span>
        </div>

        <?php wvrbbp_checkbox('hide_donate', __("I've donated.", 'weaver-for-bbpress'), '<br /><br />');
        ?>


        <div style="clear:both;"></div>
        <div class="wvrx-opts-description">
            <p>
                <?php _e('Set options for users.', 'weaver-for-bbpress'); ?>
            </p>
        </div>
    </div>

    <?php
}

?>
