<?php
// ========================================= >>> wvrbbp_style_admin <<< ===============================
function wvrbbp_style_admin()
{
    // admin for style options...
    ?>
    <h2 style="color:blue;"><?php _e('Alternative Themes and Custom CSS Rules', 'weaver-for-bbpress'); ?></h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="wvrbbp_save_style_opts"
               value="<?php _e('Theme and CSS Options Saved', 'weaver-for-bbpress'); ?>"/>


        <h3><u>bbPress Themes</u></h3>
        <?php wvrbbp_save_style_button(); ?>
        <div class="wvrx-opts-section">
            <div class="wvrx-opts-title">
                &bull; <?php _e('Theme Selection', 'weaver-for-bbpress'); ?> <span
                        class="wvrx-opts-title-description"><?php _e('Select a pre-defined Theme to use on your bbPress site.', 'weaver-for-bbpress'); ?></span>
            </div>

            <div class="wvrx-opts-opts">

                <?php
                $theme_dir = wvrbbp_DIR_PATH . 'templates/css';
                $theme_list = array();
                $theme_list[] = 'bbpress0';
                if ($media_dir = opendir($theme_dir)) {        // build the list of themes from directory
                    while ($m_file = readdir($media_dir)) {
                        $len = strlen($m_file);
                        $base = substr($m_file, 0, $len - 4);
                        $ext = $len > 4 ? substr($m_file, $len - 4, 4) : '';
                        if ($ext == '.css' && $base != 'bbpress-rtl') {
                            $theme_list[] = $base;
                        }
                    }
                    natcasesort($theme_list);        // sort it

                    if (wvrbbp_getopt('alt_style_file') != '') {        // did they specify an alternative file?
                        array_unshift($theme_list, 'use_alt_style_file');
                    }

                    if (!wvrbbp_getopt('theme')) {
                        wvrbbp_setopt('theme', 'bbpress-enhanced');
                    }    // force the default bbPress


                    ?>
                    <strong style="display:inline;padding-left:2.5em;text-indent:-1.7em;"><?php _e('Select a bbPress Theme: ', 'weaver-for-bbpress'); ?></strong>
                    <select name='theme'>
                        <?php

                        foreach ($theme_list as $filename) {
                            if ($filename == 'bbpress0') {
                                $name = 'bbPress Default';
                            } elseif ($filename == 'use_alt_style_file') {    // select the alternative style file
                                $name = __('Uploaded Custom File', 'weaver-for-bbpress');
                            } else {
                                $name = ucwords(str_replace('-', ' ', $filename));    // make blanks, leading caps
                                $name = str_replace('Bbpress', 'bbPress', $name);    // fix bbPress
                            }
                            ?>
                            <option value="<?php echo $filename; ?>" <?php selected(wvrbbp_getopt('theme') == $filename); ?> ><?php echo $name ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <br><span style="margin-left:3em;"></span>

                    <?php _e('Select <em>bbPress Default</em> to use the original, unenhanced bbPress theme if required.', 'weaver-for-bbpress'); ?>

                    <br/><br/>
                    <?php
                } else {
                    _e('Sorry, no themes found. The theme directory is empty.', 'weaver-for-bbpress');
                }
                ?>
                <?php // future code for custom css file
                echo '<div>';
                _e('<strong>Custom Style File</strong>: You can provide your own custom bbPress .css style file. Must include all rules.', 'weaver-for-bbpress');
                echo '</div>';
                wvrbbp_textarea('alt_style_file', __('Specify full Media Library URL of your bbPress Custom .css file. "Uploaded Custom File" will appear in Select list.', 'weaver-for-bbpress'));
                echo '<div style="margin-left:2.5em;padding-top:.25em;">';
                _e('<strong>Instructions</strong>: Upload your custom css file to the Media Library and paste its full URL in the Media Library into the text box above.', 'weaver-for-bbpress');
                echo '</div>';
                ?>
            </div>
            <br/>
            <div class="wvrx-opts-title">
                &bull; <?php _e('Theme Overrides <span class="wvrx-opts-title-description">Modify basic elements from our Themes</span>', 'weaver-for-bbpress'); ?>
            </div>

            <div class="wvrx-opts-opts">
                <div style="margin-left:1em;font-weight: bold;"> <? _e('Avatar Styling', 'weaver-for-bbpress'); ?></div>
                <br/>
                <?php
                wvrbbp_checkbox('round_avatars', __('<em><strong>Round Avatar</strong></em> Make Participant avatars in Forums round.', 'weaver-for-bbpress'), '<br />');
                wvrbbp_checkbox('hide_small_avatars', __('Hide <em><strong>Tiny Avatars</strong></em> in Notices.', 'weaver-for-bbpress'), '<br /><br />');

                wvrbbp_textarea('tiny_author_avatar_size', 'px' . __(' - <strong>Avatar Size</strong>. Size of small "Started by" and "Freshness" author avatars. Default is 14. Specify size 10 or larger. Recommended: 34-40.', 'weaver-for-bbpress'),
                    '<br />', 4);

                $show_started_by = wvrbbp_getopt('show_started_by_avatar', 'none');
                ?>

                <strong style="display:inline;padding-left:2.5em;text-indent:-1.7em;"><?php _e('"Started by" Avatar Location:', 'weaver-for-bbpress'); ?></strong>
                <select name='show_started_by_avatar'>
                    <option value="none" <?php selected($show_started_by == 'none'); ?>><?php _e('Default location', 'weaver-for-bbpress'); ?></option>
                    <option value="left" <?php selected($show_started_by == 'left'); ?>><?php _e('On Left of its column', 'weaver-for-bbpress'); ?></option>
                    <option value="right" <?php selected($show_started_by == 'right'); ?>><?php _e('On Right of its column', 'weaver-for-bbpress'); ?></option>
                    <option value="hide" <?php selected($show_started_by == 'hide'); ?>><?php _e('Hide avatar', 'weaver-for-bbpress'); ?></option>
                </select>
                &nbsp;&nbsp;<?php _e('The "Started by" avatar is normally displayed in the <em>Topics</em> column of topic lists.', 'weaver-for-bbpress'); ?>

                <br/>

                <?php
                $show_fresh = wvrbbp_getopt('show_freshness_avatar', 'none');
                ?>

                <strong style="display:inline;padding-left:2.5em;text-indent:-1.7em;"><?php _e('"Freshness" Avatar Location:', 'weaver-for-bbpress'); ?></strong>
                <select name='show_freshness_avatar'>
                    <option value="none" <?php selected($show_fresh == 'none'); ?>><?php _e('Default location', 'weaver-for-bbpress'); ?></option>
                    <option value="left" <?php selected($show_fresh == 'left'); ?>><?php _e('On Left of its column', 'weaver-for-bbpress'); ?></option>
                    <option value="right" <?php selected($show_fresh == 'right'); ?>><?php _e('On Right of its column', 'weaver-for-bbpress'); ?></option>
                    <option value="hide" <?php selected($show_fresh == 'hide'); ?>><?php _e('Hide avatar', 'weaver-for-bbpress'); ?></option>
                </select>
                &nbsp;&nbsp;<?php _e('The "Freshness" avatar is normally displayed in the <em>Freshness</em> column of topic lists.', 'weaver-for-bbpress'); ?>

            </div>


            <div style="margin-left:1em;font-weight: bold;"> <? _e('Additional Styling', 'weaver-for-bbpress'); ?></div>
            <br/> <?php

            wvrbbp_checkbox('no_even_odd', __("<em><strong>No Even/Odd Background Colors</strong></em> Don't use the Even/Odd background colors for lists.", 'weaver-for-bbpress'), '<br /><br />');
            wvrbbp_checkbox('author_shadow', __("<em><strong>Author Shadow Border</strong></em> Add shadow border around Author info on Topics.", 'weaver-for-bbpress'), '<br /><br />');


            wvrbbp_textarea('base_font_size', __('px - <strong>Base font size</strong> of forum text. This will increase or decrease all font sizes relative to this base value. Do not enter px, just the number. Default is 12.', 'weaver-for-bbpress'),
                '<br /><br />', 4);
            ?>


            <div style="clear:both;"></div>
            <div class="wvrx-opts-description">
                <p>
                    <?php _e("Replace the default bbPress theme with one of several pre-defined alternative themes provide by <em>Turnkey bbPress</em>.
	These themes offer different color combinations that will blend well with almost any WP Theme.
	The extra options can be used to modify basic element of the alternative themes such as base font size.
	Note: <em>bbPress Original</em> is the raw original bbPress theme. You probably don't want to use it.
	<em>bbPress Original Updated</em> is the original with minimal styling updates for this plugin.
	<em>bbPress Enhanced</em> is the original version enhanced with mixed font sizes and better button styling.
	Most of the other named themes are based on the <em>bbPress Enhanced</em> version.", 'weaver-for-bbpress'); ?>

                </p>
            </div>
        </div>

        <?php wvrbbp_save_style_button(); ?>


        <!-- ======== -->

        <h3><u><?php _e('Custom CSS', 'weaver-for-bbpress'); ?></u></h3>
        <div class="wvrx-opts-section">
            <div class="wvrx-opts-title">
                &bull; <?php _e('Add Custom CSS Rules', 'weaver-for-bbpress'); ?> <span
                        class="wvrx-opts-title-description"><?php _e('Add your own custom CSS Rules', 'weaver-for-bbpress'); ?></span>
            </div>
            <p>
                <?php _e("This section allows you to add new CSS Rules. You can make the plugin's output more closely match your own WP theme.
You can add any standard CSS rule to the text box.", 'weaver-for-bbpress'); ?>
                <?php _e('For help with bbPress styles, see the <a href="//codex.bbpress.org/bbpress-styling-crib/" target="_blank">bbPress Styling Crib</a>.', 'weaver-for-bbpress'); ?>
            </p>

            <?php
            if (current_user_can('unfiltered_html')) {
                $css = wvrbbp_getopt('custom_css');
                ?>
                <textarea name="wvrbbp_custom_css"
                          placeholder="<?php _e('.sample-class { ... } /* enter CSS Rules */', 'weaver-for-bbpress'); ?>"
                          rows=8 style="width: 95%"><?php echo esc_textarea($css); ?></textarea>
                <?php
            } else {
                ?>
                <p><?php _e('Sorry, due to security issues, you must have an Administrator user role to add Custom CSS Rules.', 'weaver-for-bbpress'); ?></p>
                <?php
            }
            ?>
        </div>
        <?php
        wvrbbp_save_style_button();
        wvrbbp_nonce_field('wvrbbp_save_style_opts');
        ?>
    </form>
    <?php
}

function wvrbbp_save_style_button()
{
    ?>
    <input style="margin-bottom:5px;" class="button-primary" type="submit" name="wvrbbp_save_style_options"
           value="<?php _e('Save Theme and Style Options', 'weaver-for-bbpress'); ?>"/>
    <?php
}

