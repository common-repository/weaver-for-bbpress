<?php
/**
 * Weaver Best Answer
 *
 * @copyright 2017 Weaver Theme
 */

/**
 * Weaver Best Answer
 *
 */
class wvrbbp_BestAnswer
{

    protected static $instance = null;
    protected $version = '0.1';

    /**
     * Initialize the plugin by setting localization, filters, and administration functions.
     *
     */
    private function __construct()
    {

        /************************************************************************
         * Admin
         ***********************************************************************/

        // Reply row actions, handler and notices
        add_filter('post_row_actions', array($this, 'reply_row_actions'), 10, 2);
        add_action('load-edit.php', array($this, 'toggle_reply_admin_handler'));


        /************************************************************************
         * Replies
         ***********************************************************************/
        // Add admin links
        add_filter('bbp_reply_admin_links', array($this, 'add_reply_admin_links'), 10, 2);

        // Report handler
        add_action('bbp_get_request', array($this, 'toggle_reply_handler'), 1);

        // Add notice to accepted reply
        add_action('bbp_theme_before_reply_content', array($this, 'output_reply_notice'));
    }

    /**
     * Return an instance of this class.
     */
    public static function get_instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }




    /************************************************************************
     * REPLIES
     ***********************************************************************/

    /**
     * Does the topic have a best answer?
     */
    public static function topic_has_answer($topic_id)
    {
        return get_post_meta($topic_id, '_bbp_best_reply_id', true);
    }

    /**
     * Add our Accept link to the reply admin links
     */
    public function add_reply_admin_links($links, $reply_id)
    {

        // Only display for logged in users
        if (!is_user_logged_in()) {
            return $links;
        }

        $args = array();

        $links['report'] = $this->get_reply_best_link($args);

        return $links;
    }

    /**
     * Render the reply accept admin link
     */
    public function get_reply_best_link($args = '')
    {

        // Parse arguments against default values
        $r = bbp_parse_args($args, array(
            'id' => 0,
            'link_before' => '',
            'link_after' => '',
            'best_text' => esc_html__('Accept Answer', 'weaver-for-bbpress'),
            'unbest_text' => esc_html__('Unaccept Answer', 'weaver-for-bbpress'),
        ), 'get_reply_best_link');


        $reply_id = bbp_get_reply_id((int)$r['id']);

        if (!$reply_id) {
            return false;
        }

        $accepted = $this->is_reply_best($reply_id);


        // Only display un-accept link for accepted
        if ($accepted && !$this->user_can_accept($reply_id)) {
            return '';
        }


        $topic_id = bbp_get_reply_topic_id($reply_id);
        $topics_reply = get_post_meta($topic_id, '_bbp_best_reply_id', true);        // get the value, if any

        if ($topics_reply && !$accepted)        // only show one unaccept
        {
            return '';
        }


        $display = $accepted ? $r['unbest_text'] : $r['best_text'];
        $uri = add_query_arg(array('action' => 'wvrbbp_toggle_reply_best', 'reply_id' => $reply_id));
        $uri = wp_nonce_url($uri, 'best-reply_' . $reply_id);
        $classes = array('bbp-reply-report-link');
        if (true === $accepted) {
            $classes[] = 'best-reply';
        } else {
            $classes[] = 'notbest-reply';
        }


        $retval = $r['link_before'] . '<a href="' . esc_url($uri) . '" class="' . join(' ', array_map('esc_attr', $classes)) . '" title="' . __('Select as best answer', 'weaver-for-bbpress') . '">' . $display . '</a>' . $r['link_after'];

        return apply_filters('wvrbbp_get_reply_best_link', $retval, $r);
    }

    /**
     * Is the reply marked as best?
     */
    public static function is_reply_best($reply_id = 0)
    {

        return (bool)get_post_meta(bbp_get_reply_id($reply_id), '_bbp_best_topic_id', true);        // return the value, if any
    }

    /**
     * Can the user accept/unaccept replies?
     */

    function user_can_accept($reply_id)
    {

        // moderators or topic originator can accept/unaccept answers

        return current_user_can('moderate', $reply_id)
            || bbp_get_topic_author_id(bbp_get_reply_topic_id($reply_id)) == wp_get_current_user()->ID;

    }


    /**
     * Handles the front end accept/unaccept of replies
     */
    public function toggle_reply_handler($action = '')
    {

        // Bail if required GET actions aren't passed
        if (empty($_GET['reply_id'])) {
            return;
        }

        // Setup possible get actions
        $possible_actions = array(
            'wvrbbp_toggle_reply_best',
        );

        // Bail if actions aren't meant for this function
        if (!in_array($action, $possible_actions)) {
            return;
        }

        $failure = '';                         // Empty failure string
        $view_all = false;                      // Assume not viewing all
        $reply_id = (int)$_GET['reply_id'];    // What's the reply id?
        $success = false;                      // Flag
        $post_data = array('ID' => $reply_id); // Prelim array
        $redirect = '';                         // Empty redirect URL

        // Make sure reply exists
        $reply = bbp_get_reply($reply_id);
        if (empty($reply)) {
            return;
        }

        // Bail if non-logged-in user
        if (!is_user_logged_in()) {
            return;
        }

        // What action are we trying to perform?
        switch ($action) {

            // Toggle accepted
            case 'wvrbbp_toggle_reply_best' :
                check_ajax_referer('best-reply_' . $reply_id);

                $is_best = $this->is_reply_best($reply_id);
                $success = true === $is_best ? $this->unaccept_reply($reply_id) : $this->accept_reply($reply_id);
                $failure = true === $is_best ? __('<strong>ERROR</strong>: There was a problem unaccepting the reply as best.', 'weaver-for-bbpress') : __('<strong>ERROR</strong>: There was a problem accepting the reply.', 'weaver-for-bbpress');
                break;
        }

        // No errors
        if ((false !== $success) && !is_wp_error($success)) {

            /** Redirect **********************************************************/

            // Redirect to
            $redirect_to = bbp_get_redirect_to();

            // Get the reply URL
            $reply_url = bbp_get_reply_url($reply_id, $redirect_to);

            // Add view all if needed
            if (!empty($view_all)) {
                $reply_url = bbp_add_view_all($reply_url, true);
            }

            // Redirect back to reply
            wp_safe_redirect($reply_url);

            // For good measure
            exit();

            // Handle errors
        } else {
            bbp_add_error('wvrbbp_toggle_reply', $failure);
        }
    }

    /**
     * Unaccept a reply
     */
    function unaccept_reply($reply_id = 0)
    {

        //wvrbbp_alert('unaccept');

        $reply_id = bbp_get_reply_id($reply_id);

        //$best = get_post_meta( $reply_id, '_bbp_best_topic_id', true );		// get the value, if any


        // Bail if user doesn't have moderate capability
        if (!$this->user_can_accept($reply_id)) {
            return false;
        }

        $topic_id = bbp_get_reply_topic_id($reply_id);

        delete_post_meta($reply_id, '_bbp_best_topic_id');
        delete_post_meta($topic_id, '_bbp_best_reply_id');

        // Return reply_id
        return $reply_id;
    }

    /**
     * Marks a reply as best
     */
    function accept_reply($reply_id = 0)
    {

        $reply_id = bbp_get_reply_id($reply_id);


        // Bail if user doesn't have moderate capability
        if (!$this->user_can_accept($reply_id)) {
            return false;
        }

        $topic_id = bbp_get_reply_topic_id($reply_id);

        update_post_meta($reply_id, '_bbp_best_topic_id', $topic_id);    // set to the parent topic
        update_post_meta($topic_id, '_bbp_best_reply_id', $reply_id);    // set parent topic to accepted reply

        // Return reply_id
        return $reply_id;
    }

    /**
     * Ouput a notice on the front end when a reply has been accepted
     */
    public function output_reply_notice()
    {
        global $post;

        $reply_id = get_the_ID();

        // If post is a topic, return. (handled with 'output_topic_notice')
        if (bbp_is_topic($reply_id)) {
            return;
        }

        if (!$this->is_reply_best($reply_id)) {
            return;
        }

        echo '<div class="error wvrbbpreply-is-accepted">';
        echo '<p>';
        echo apply_filters('wvrbbp_reply_notice', __('<em>This reply has been accepted as the best answer.</em>', 'weaver-for-bbpress'));
        echo '</p>';
        echo '</div>';
    }


    /**
     * Reply Row actions
     *
     * Source of do_action supplies $reply object
     *
     * Add "unreport" link to accepted Replies
     *
     */
    public function reply_row_actions($actions, $reply)
    {

        // Bail if we're not editing replies
        if (bbp_get_reply_post_type() != get_current_screen()->post_type) {
            return $actions;
        }

        // Only show the actions if the user is capable of viewing them :)
        if ($this->user_can_accept($reply->ID)) {

            // Report
            $report_uri = wp_nonce_url(add_query_arg(array('reply_id' => $reply->ID, 'action' => 'wvrbbp_toggle_reply_best'), remove_query_arg(array('bbp_reply_toggle_notice', 'reply_id', 'failed', 'super'))), 'best-reply_' . $reply->ID);
            if ($this->is_reply_best($reply->ID)) {
                $actions['report'] = '<a href="' . esc_url($report_uri) . '" title="' . esc_attr__('Unaccept the answer', 'weaver-for-bbpress') . '">' . esc_html__('Unaccept answer', 'weaver-for-bbpress') . '</a>';
            }
        }

        return $actions;
    }

    /**
     * Handle admin reply toggling
     *
     * @param string $action The requested action to compare this function to
     *
     * @since 1.0.0
     *
     */
    public function toggle_reply_admin_handler()
    {

        // Bail if we're not editing replies
        if (bbp_get_reply_post_type() != get_current_screen()->post_type) {
            return;
        }

        // Only proceed if GET is a reply toggle action
        if (bbp_is_get_request() && !empty($_GET['action']) && in_array($_GET['action'], array('wvrbbp_toggle_reply_best')) && !empty($_GET['reply_id'])) {
            $action = $_GET['action'];            // What action is taking place?
            $reply_id = (int)$_GET['reply_id'];    // What's the reply id?
            $success = false;                      // Flag
            $post_data = array('ID' => $reply_id); // Prelim array


            if (!$this->user_can_accept($reply_id)) // What is the user doing here?
            {
                wp_die(__('You do not have the permission to do that!', 'weaver-for-bbpress'));
            }

            switch ($action) {
                case 'wvrbbp_toggle_reply_best' :
                    check_admin_referer('best-reply_' . $reply_id);

                    $is_best = $this->is_reply_best($reply_id);
                    $message = true === $is_best ? 'notbest' : 'best';
                    $success = true === $is_best ? $this->unaccept_reply($reply_id) : $this->accept_reply($reply_id);

                    break;
                default:
                    $message = '';
                    $success = false;
                    break;
            }

            $message = array('bbp_reply_toggle_notice' => $message, 'reply_id' => $reply_id);

            if (false === $success || is_wp_error($success)) {
                $message['failed'] = '1';
            }

            // Redirect back to the reply
            $redirect = add_query_arg($message, remove_query_arg(array('action', 'reply_id')));
            wp_safe_redirect($redirect);

            // For good measure
            exit();
        } // end if GET request, etc.
    }

} // end class wvrbbp_BestAnswer
