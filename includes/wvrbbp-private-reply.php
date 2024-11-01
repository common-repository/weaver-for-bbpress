<?php
define('wvrbbp_PRIVATE_CAPABILITY', 'moderate');


// Allow others to change the capability required to view private posts.
//add_action( 'plugins_loaded', 'wvrbbp_filter_capability' ) ;

// show the "Private Reply?" checkbox
add_action('bbp_theme_before_reply_form_submit_wrapper', 'wvrbbp_private_checkbox');

// save the private reply state
add_action('bbp_new_reply', 'wvrbbp_update_reply', 0, 6);
add_action('bbp_edit_reply', 'wvrbbp_update_reply', 0, 6);

// hide reply content
add_filter('bbp_get_reply_excerpt', 'wvrbbp_hide_reply', 999, 2);
add_filter('bbp_get_reply_content', 'wvrbbp_hide_reply', 999, 2);
add_filter('the_content', 'wvrbbp_hide_reply', 999);
add_filter('the_excerpt', 'wvrbbp_hide_reply', 999);

// private replies will simply send "Private reply" message to all subscribers, including moderators
add_filter('bbp_subscription_mail_message', 'wvrbbp_prevent_subscription_email', 999999, 3);

// add a class name indicating the read status
add_filter('post_class', 'wvrbbp_reply_post_class');


function wvrbbp_hide_reply($content = '', $reply_id = 0)
{
    // Hides the reply content for users that do not have permission to view it

    if (empty($reply_id)) {
        $reply_id = bbp_get_reply_id($reply_id);
    }


    if (wvrbbp_is_private($reply_id)) {

        $can_view = false;
        $current_user = is_user_logged_in() ? wp_get_current_user() : false;
        $topic_author = bbp_get_topic_author_id();
        $reply_author = bbp_get_reply_author_id($reply_id);

        if (!empty($current_user) && $topic_author === $current_user->ID && user_can($reply_author, wvrbbp_PRIVATE_CAPABILITY)) {
            // Let the thread author view replies if the reply author is from a moderator
            $can_view = true;
        }

        if (!empty($current_user) && $reply_author === $current_user->ID) {
            // Let the reply author view their own reply
            $can_view = true;
        }

        if (current_user_can(wvrbbp_PRIVATE_CAPABILITY)) {
            // Let moderators view all replies
            $can_view = true;
        }

        if (!$can_view) {
            $content = __('This reply has been marked as private.', 'weaver-for-bbpress');
        }
    }

    return $content;
}

/*
 * Probably don't actually need that as editing private replies seems to work right now. Gillian thought it didn't, but it seems to.
//add_filter('bbp_is_reply_edit', 'wvrbbp_is_reply_edit' ) ;
function wvrbbp_is_reply_edit($retval ) {
	global $wp_query, $pagenow;

	if ( $retval )
		return true;

	// Check query - allow moderator as well as admin
	if ( current_user_can( wvrbbp_PRIVATE_CAPABILITY ) && ( 'post.php' === $pagenow ) && ( get_post_type() === bbp_get_reply_post_type() ) && ( ! empty( $_GET['action'] ) && ( 'edit' === $_GET['action'] ) ) ) {
		$retval = true;
	}

	return (bool) $retval;
}
*/


function wvrbbp_private_checkbox()
{
    // Outputs the "Set as private reply" checkbox
    ?>
    <p>

        <input name="bbp_private_reply" id="bbp_private_reply"
               type="checkbox"<?php checked('1', wvrbbp_is_private(bbp_get_reply_id())); ?> value="1"
               tabindex="<?php bbp_tab_index(); ?>"/>

        <?php if (bbp_is_reply_edit() && (get_the_author_meta('ID') != bbp_get_current_user_id())) : ?>

            <label for="bbp_private_reply"><?php _e('Set post as private.', 'bbp_private_replies'); ?></label>

        <?php else : ?>

            <label for="bbp_private_reply"><?php _e('Set as <em>Private Reply</em> readable only by you, the topic author, and Moderators.', 'bbp_private_replies'); ?></label>

        <?php endif; ?>
    </p>
    <?php
}

/**
 * Stores the private state on reply creation and edit
 *
 * @param $reply_id int The ID of the reply
 * @param $topic_id int The ID of the topic the reply belongs to
 * @param $forum_id int The ID of the forum the topic belongs to
 * @param $anonymous_data bool Are we posting as an anonymous user?
 * @param $author_id int The ID of user creating the reply, or the ID of the reply's author during edit
 * @param $is_edit bool Are we editing a reply?
 *
 * @return void
 */

function wvrbbp_update_reply($reply_id = 0, $topic_id = 0, $forum_id = 0, $anonymous_data = false, $author_id = 0, $is_edit = false)
{


    if (isset($_POST['bbp_private_reply'])) {
        update_post_meta($reply_id, '_bbp_reply_is_private', '1');
    } else {
        delete_post_meta($reply_id, '_bbp_reply_is_private');
    }

}


function wvrbbp_reply_post_class($classes)
{

    // Adds a new class to replies that are marked as private

    $reply_id = bbp_get_reply_id();

    // only apply the class to replies
    if (bbp_get_reply_post_type() != get_post_type($reply_id)) {
        return $classes;
    }

    if (wvrbbp_is_private($reply_id)) {
        $classes[] = 'bbp-private-reply';
    }

    return $classes;
}

function wvrbbp_prevent_subscription_email($message, $reply_id, $topic_id)
{

    if (wvrbbp_is_private($reply_id)) {
        return __('This reply has been marked as private.', 'bbp_private_replies');
    }

    return $message; // message unchanged
}

/* function filter_capability() {
	// Called during the plugins_loaded action to filter the capability required to view private replies.
	$this->capability = apply_filters( 'bbp_private_replies_capability', $this->capability );
} */


