<?php
// ======================================================== mentions admin ===============================
function wvrbbp_save_restore_admin()
{
    ?>
    <h2 style="color:blue;"><?php _e('Save and Restore Settings', 'weaver-for-bbpress'); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="wvrbbp_save_restore" value="Saved/Restored"/>
        <input style="display:none;" type="submit" name="atw_stop_enter" value="Ignore Enter"/>

        <?php

        wvrbbp_define_save_restore();            // define content display options

        wvrbbp_nonce_field('wvrbbp_save_restore');
        ?>

    </form>
    <hr/>
    <?php
}


// ========================================= >>> wvrbbp_define_mentions <<< ===============================

function wvrbbp_define_save_restore()
{
    // need to add each option value to wvrbbp_save_form_options in wvrbbp-admin-top.php
    ?>

    <h3><u><?php _e('Save/Restore', 'weaver-for-bbpress'); ?></u></h3>
    <div class="wvrx-opts-section">

        <div class="wvrx-opts-opts">

            <div class="wvrx-opts-title" style="clear:both;">
                &bull; <?php _e('Save/Restore', 'weaver-for-bbpress'); ?> <span
                        class="wvrx-opts-title-description"><?php _e('Save and Restore Settings', 'weaver-for-bbpress'); ?></span>
            </div>

            <?php

            $time = date('Y-m-d-Hi');

            echo "<div style='margin-top:15px;'>\n";


            wvrbbp_download_link('<strong>Save Settings</strong>',
                'weaver-bbpress-settings', 'wvrbbp', $time);

            ?>
        </div>

        <input style="margin-left:2em;" class="download-link" type="submit" name="wvrbbp_save_restore"
               value="<?php _e('Restore Settings', 'weaver_for_bbpress'); ?>"/>
        <span style="border:1px solid #CCC;width:400px;padding:2px;"><input name="post_uploaded" type="file"/></span>
        <input type="hidden" name="uploadit"
               value="yes"/>- <?php _e('Upload file to restore settings', 'weaver-for-bbpress'); ?>
        <br/><br/>

        <div style="clear:both;"></div>
        <div class="wvrx-opts-description">
            <hr/>
            <p>
                <?php _e('Options to save and restore settings to your own computer.', 'weaver-for-bbpress'); ?>
            </p>
        </div>

    </div>
    </div>

    <?php
}


