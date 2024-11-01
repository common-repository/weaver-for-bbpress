<?php

// Turnkey bbPress Topic Resolution
// Derived from bbResolutions plugin
// This is replacement compatible with bbResolution - resolutions set by bbResolutions will be recognized
//
// This code is considerably simplified, and NOT designed for modification by other plugins or custom code.
//
// If resolutions enabled, then everything needs loaded a runtime since it all interacts with the front-end view
//

define('wvrbbp_RES_META_ID', 'bbr_topic_resolution');    // this keeps it compatible with bbResoluton

// ======================================== resolution form for topic =================

function wvrbbp_topic_resolution_feedback($answered = '', $topic_id = 0)
{
    // display for non-logged users

    $topic_id = bbp_get_topic_id($topic_id);

    if (empty($topic_id)) {
        return;
    }

    $topic_resolution = wvrbbp_get_topic_resolution_object($topic_id);
    $resolution = '';
    if (!empty($topic_resolution['label'])) {
        $resolution = $topic_resolution['label'];
    }

    if ($answered != '') {
        $resolution = __('Answered', 'weaver-for-bbpress');
    }    // Let best answer force 'Answered' as status

    if ($resolution != '') { ?>
        <div class="wvrbbp-feedback">
        <div class="wvrbbp-inner-message wvrbbp-topic-resolution-message">
            <?php printf(__('Topic Resolution: %s', 'weaver-for-bbpress'), '<span class="wvrbbp-topic-resolution">' . $resolution . '<span>') ?>
        </div>
        </div><?php
    }
}

add_action('bbp_template_before_single_topic', 'wvrbbp_topic_resolution_form');


function wvrbbp_topic_resolution_form($topic_id = 0)
{
    // show the form above the Topic
    $topic_id = bbp_get_topic_id($topic_id);


    if (!$topic_id) {
        return;
    }

    if (class_exists('wvrbbp_BestAnswer') && wvrbbp_BestAnswer::topic_has_answer($topic_id)) {

        wvrbbp_topic_resolution_feedback('answered');

    } elseif (current_user_can('edit_topic', $topic_id)) {

        $visibility = wvrbbp_get_topic_resolution_key($topic_id);
        ?>
        <div class="wvrbbp-form-wrapper wvrbbp-resolution-form">
        <form method="POST" action="<?php echo esc_url(home_url('/')) ?>"
              class="wvrbbp-form wvrbbp-form-topic-resolution">

            <div class="wvrbbp-field-wrapper">
                <label for="wvrbbp-topic-resolution"><?php esc_html_e('Resolution:', 'weaver-for-bbpress') ?></label>
                <select name='wvrbbp_topic_resolution'>
                    <option value="" <?php selected($visibility == ''); ?>><?php _e('None', 'weaver-for-bbpress'); ?></option>
                    <option value="resolved" <?php selected($visibility == 'resolved'); ?>><?php _e('Resolved', 'weaver-for-bbpress'); ?></option>
                    <option value="investigating" <?php selected($visibility == 'investigating'); ?>><?php _e('Investigating', 'weaver-for-bbpress'); ?></option>
                    <option value="not-resolved" <?php selected($visibility == 'not-resolved'); ?>><?php _e('Not Resolved', 'weaver-for-bbpress'); ?></option>
                    <option value="not-support" <?php selected($visibility == 'not-support'); ?>><?php _e('Not a Question', 'weaver-for-bbpress'); ?></option>
                </select>
            </div>

            <div class="wvrbbp-submit-wrapper wvrbbp-resolution-submit">
                <input type="submit" value="<?php esc_attr_e('Update', 'weaver-for-bbpress') ?>"/>
            </div>

            <input type="hidden" name="wvrbbp_topic_id" value="<?php echo esc_attr($topic_id) ?>"/>
            <input type="hidden" name="wvrbbp_action" value="wvrbbp_update_topic_resolution"/>

            <?php wp_nonce_field('wvrbbp_topic_resolution', 'wvrbbp_res_nonce') ?>
        </form>
        </div><?php
    } else {
        wvrbbp_topic_resolution_feedback();
    }

}

add_action('bbp_theme_before_topic_title', 'wvrbbp_topic_resolution_sticker');


function wvrbbp_topic_resolution_sticker($topic_id = 0)
{
    echo wvrbbp_get_topic_resolution_sticker($topic_id);
}


function wvrbbp_get_topic_resolution_sticker($topic_id = 0)
{

    $topic_id = bbp_get_topic_id($topic_id);


    if (empty($topic_id)) {
        return '';
    }

    if (class_exists('wvrbbp_BestAnswer') && wvrbbp_BestAnswer::topic_has_answer($topic_id)) {
        return '';        // don't show resolution status if Best Answer set
    }

    $resolution = wvrbbp_get_topic_resolution_object($topic_id);


    if ($resolution !== null && !empty($resolution['sticker'])) {

        $class = "wvrbbp-resolution-sticker wvrbbp-resolution-{$resolution['sticker']}-sticker";


        return "<span class='{$class}'>" . $resolution['sticker'] . '</span>';

    }
    return '';
}

// ====================================== handler for update button ==========================

add_action('template_redirect', 'wvrbbp_topic_actions_handler', 11);


function wvrbbp_topic_actions_handler()
{

    // validate the $_POST values

    if (!isset($_POST['wvrbbp_action'])) {    // not us...
        return false;
    }

    if ('wvrbbp_update_topic_resolution' !== $_POST['wvrbbp_action']) {
        return false;
    }

    if (!isset($_POST['wvrbbp_res_nonce'])) {
        return false;
    }

    if (!wp_verify_nonce($_POST['wvrbbp_res_nonce'], 'wvrbbp_topic_resolution')) {
        return false;
    }

    if (!isset($_POST['wvrbbp_topic_id'])) {
        return false;
    }

    $topic_id = intval($_POST['wvrbbp_topic_id']);

    if (!$topic_id || !bbp_is_topic($topic_id)) {
        return false;
    }

    if (!current_user_can('edit_topic', $topic_id)) {
        return false;
    }

    if (!isset($_POST['wvrbbp_topic_resolution'])) {
        return false;
    }

    if (!empty($_POST['wvrbbp_topic_resolution'])) {
        wvrbbp_update_topic_resolution($topic_id, $_POST['wvrbbp_topic_resolution']);
    } else {
        wvrbbp_delete_topic_resolution($topic_id);
    }

    // Redirect to the topic page.
    wp_safe_redirect(bbp_get_topic_permalink($topic_id));

    // For good measure
    exit();

}

// ================================== manipulate the resolution values ===================


function wvrbbp_get_topic_resolution_key($topic_id)
{

    $object = wvrbbp_get_topic_resolution_object($topic_id);

    if (empty($object)) {
        return false;
    }

    return $object['key'];

}

function wvrbbp_get_topic_resolution_key_to_value($key)
{

    $objs = wvrbbp_get_resolutions_array();

    foreach ($objs as $obj => $val) {

        if ($val['key'] == $key) {
            return $val['value'];
        }
    }

    return '';

}


function wvrbbp_get_topic_resolution_value($topic_id)
{

    $topic_id = bbp_get_topic_id($topic_id);

    if (empty($topic_id)) {
        return false;
    }

    return get_post_meta($topic_id, wvrbbp_RES_META_ID, true);

}


function wvrbbp_get_topic_resolution_object($topic_id)
{

    $value = wvrbbp_get_topic_resolution_value($topic_id);
    $objs = wvrbbp_get_resolutions_array();

    foreach ($objs as $obj => $val) {

        if ($val['value'] == $value) {
            return $val;
        }
    }

    return array();
}

function wvrbbp_get_resolutions_array()
{
    if (!wvrbbp_getopt('use_resolve_icons')) {
        $res = array(
            array(
                'key' => 'not-support',
                'label' => __('Not a Question', 'weaver-for-bbpress'),
                //'sticker'   => __( '[Not Question]', 'weaver-for-bbpress' ),
                'value' => '1',
            ),

            array(
                'key' => 'not-resolved',
                'label' => __('Not Resolved', 'weaver-for-bbpress'),
                'sticker' => __('[Not Resolved]', 'weaver-for-bbpress'),
                //'sticker'	=> '<span class="dashicons dashicons-warning" style="vertical-align:middle"></span>',
                'value' => '2',
            ),

            array(
                'key' => 'resolved',
                'label' => __('Resolved', 'weaver-for-bbpress'),
                'sticker' => __('[Resolved]', 'weaver-for-bbpress'),
                //'sticker'   => '<span class="dashicons dashicons-yes" style="vertical-align:text-top;font-size:150%;"></span>',
                'value' => '3',
            ),
            array(
                'key' => 'investigating',
                'label' => __('Investigating', 'weaver-for-bbpress'),
                'sticker' => __('[Investigating]', 'weaver-for-bbpress'),
                //'sticker'   => '<span class="dashicons dashicons-hammer" style="vertical-align:text-top;font-size:150%;"></span>',
                'value' => '4',
            ),
        );
    } else {
        $res = array(
            array(
                'key' => 'not-support',
                'label' => __('Not a Question', 'weaver-for-bbpress'),
                //'sticker'   => __( '[Not Question]', 'weaver-for-bbpress' ),
                'value' => '1',
            ),

            array(
                'key' => 'not-resolved',
                'label' => __('Not Resolved', 'weaver-for-bbpress'),
                //'sticker'   =>  __( '[Not Resolved]', 'weaver-for-bbpress' ),
                'sticker' => '<span class="dashicons dashicons-no-alt" style="vertical-align:middle"></span>',
                'value' => '2',
            ),

            array(
                'key' => 'resolved',
                'label' => __('Resolved', 'weaver-for-bbpress'),
                //'sticker'   => __( '[Resolved]', 'weaver-for-bbpress' ),
                'sticker' => '<span class="dashicons dashicons-yes" style="vertical-align:text-top;font-size:150%;"></span>',
                'value' => '3',
            ),
            array(
                'key' => 'investigating',
                'label' => __('Investigating', 'weaver-for-bbpress'),
                //'sticker'   => __( '[Investigating]', 'weaver-for-bbpress' ),
                'sticker' => '<span class="dashicons dashicons-admin-tools" style="vertical-align:text-top;"></span>',
                'value' => '4',
            ),
        );
    }

    return $res;
}


function wvrbbp_update_topic_resolution($topic_id, $new_resolution)
{
    // $new_resolution coming in as a key value

    $topic_id = bbp_get_topic_id($topic_id);

    if (empty($topic_id)) {
        return false;
    }

    if (empty($new_resolution)) {
        return false;
    }

    $old_resolution = wvrbbp_get_topic_resolution_object($topic_id);

    if ($old_resolution && $new_resolution == $old_resolution['value']) {
        return true;
    }

    $val = wvrbbp_get_topic_resolution_key_to_value($new_resolution);

    $updated = update_post_meta($topic_id, wvrbbp_RES_META_ID, $val);

    return $updated;
}


function wvrbbp_delete_topic_resolution($topic_id)
{

    $topic_id = bbp_get_topic_id($topic_id);

    if (empty($topic_id)) {
        return false;
    }

    $deleted = delete_post_meta($topic_id, wvrbbp_RES_META_ID);

    return $deleted;
}

