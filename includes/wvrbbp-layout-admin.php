<?php
// ======================================================== layout admin ===============================
function wvrbbp_layout_admin()
{
    ?>
    <h2 style="color:blue;"><?php _e('Forums &amp; Topic List Layout Options', 'weaver-for-bbpress'); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="wvrbbp_save_layout_opts" value="Layout Options Saved"/>
        <input style="display:none;" type="submit" name="atw_stop_enter" value="Ignore Enter"/>

        <?php

        wvrbbp_definelayout();            // define content display options
        wvrbbp_savelayout_button();

        wvrbbp_nonce_field('wvrbbp_save_layout_opts');

        ?>

    </form>
    <hr/>
    <?php
}

function wvrbbp_savelayout_button()
{
    ?>
    <input style="margin-bottom:5px;" class="button-primary" type="submit" name="wvrbbp_save_layout_opts"
           value="<?php _e('Save Layout Options', 'weaver_for_bbpress'); ?> "/>
    <?php
}


// ========================================= >>> wvrbbp_define_mentions <<< ===============================

function wvrbbp_definelayout()
{
    // define display filter options
    // need to add each option value to wvrbbp_save_form_options in wvrbbp-admin-top.php

// site email stuff
    ?>

    <div class="wvrx-opts-section">
        <div class="wvrx-opts-opts">

            <div class="wvrx-opts-title" style="clear:both;">
                &bull; <?php _e('Forum &amp; Topic Lists Layout <span class="wvrx-opts-title-description">Change default Layouts of Forum Lists</span>', 'weaver-for-bbpress'); ?>
            </div>
            <div style="margin-left:1.5em;color: #0000dd;">
                <strong><?php _e('New Features Added by <em>Turnkey bbPress</em>', 'weaver-for-bbpress'); ?></strong>
            </div>
            <br/>
            <?php

            wvrbbp_checkbox('enable_view_count', __('Enable: <em><strong>Topic Views Counter</strong></em> for all topic views. (Styled with .wvrbbp-topic-list-views and .wvrbbp-forum-list-views)', 'weaver-for-bbpress'), '<br />');

            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

            wvrbbp_checkbox('view_count_only_logged', __('<em><strong>Count Only Logged In Views</strong></em> - Count only views by logged in visitors instead of all page views.', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('enable_new_topic_link', __('Add <em><strong>Create New Topic</strong></em> link at top of Single Forum views. (Styled with .wvrbbp-new-topic)', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('hide_fav_sub',
                __('Hide <em><strong>Favorited and Subscribed Icons</strong></em> on Forum and Topic Lists.', 'weaver-for-bbpress'), '<br /><br />');

            ?>
            <div style="margin-left:1.5em; color: #0000dd;">
                <strong><?php _e('Display Options for standard <em>bbPress</em> Features', 'weaver-for-bbpress'); ?></strong>
            </div>
            <br/><?php


            wvrbbp_checkbox('hide_counts', __('Hide <em><strong>Voices/Posts Counts</strong></em> on Forum Lists.', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('hide_voices', __('Hide <em><strong>Voices Column</strong></em> on Topics Lists.', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('add_forum_description', __('Add <em><strong>Forum Description</strong></em> to top of Single Forum Views. (Styled with .wvrbbp-forum-description)', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('forum_list_columns', __('Display <em><strong>Subforums in Columns</strong></em> rather than a comma separated list.', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('hide_contains_descriptions', __('Hide <em><strong>Forum/Topic Header Info</strong></em>. Hide the "This forum/topic contains count ... " info at top of lists. WARNING! Enabling this option will also HIDE the notice when new topics are tagged for moderation.', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('hide_account_ability', __('Hide <em><strong>Account Ability Message</strong></em>. Hide the "Your account has the ability to post ..." message above post/reply editor.', 'weaver-for-bbpress'), '<br /><br />');

            wvrbbp_checkbox('clear_after_breadcrumbs', __('<em><strong>Clear After Breadcrumbs</strong></em>. Add "clear:both;" after forum/topic breadcrumbs. Allows easier styling for <em>Subscribe</em>, <em>Create New Topic</em>, etc. <em></strong>Recommended.</strong></em>', 'weaver-for-bbpress'), '<br /><br />');
            ?>

            <div style="clear:both;"></div>
            <div class="wvrx-opts-description">
                <hr/>
                <p>
                    <?php _e('These options are used to show/hide and add extra options to the Forums and Topics Lists Index displays.', 'weaver-for-bbpress'); ?>
                </p>
            </div>
        </div>
    </div>


    <?php
}

