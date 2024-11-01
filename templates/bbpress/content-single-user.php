<?php

/**
 * Single User Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
?>
<!-- Turnkey bbPress content-single-user -->
<div id="bbpress-forums">
	<?php

	$current_user = is_user_logged_in() ? wp_get_current_user() : false;

	if ( wvrbbp_getopt( 'enable_profile_options' ) ) {
		$visibility = wvrbbp_getopt( 'profile_visibility', 'own_and_moderator' );
		if ( wvrbbp_hide_profile( bbp_get_displayed_user_id() ) )                // per user option
		{
			$visibility = 'own_and_moderator';
		}
	} else {
		$visibility = 'all';            // default is Everyone true
	}

	$show = false;

	if ( current_user_can( 'edit_users' ) ) {
		$show = true;
	} else {
		switch ( $visibility ) {
			case 'all':
				$show = true;
				break;

			case 'loggedin':
				$show = is_user_logged_in();
				break;

			case 'own':
				$show = bbp_is_user_home();
				break;

			case 'own_and_moderator':
				$show = bbp_is_user_home();
				if ( ! $show ) {
					$show = current_user_can( 'bbp_moderator' );
				}
				break;

			case 'admin_moderator':
				$show = current_user_can( 'edit_users' );
				if ( ! $show ) {
					$show = current_user_can( 'bbp_moderator' );
				}
				break;

			case 'admin':
				$show = current_user_can( 'edit_users' );
				break;

			default:
				break;
		}
	}
	if ( $show ) {
		?>

		<?php do_action( 'bbp_template_notices' ); ?>

		<div id="bbp-user-wrapper">
			<?php
			bbp_get_template_part( 'user', 'details' );
			/* The following bbp-user-body div will display appropriate content
			 * after user selects an item selected from the user-details menu.
			 */
			?>

			<div id="bbp-user-body">
				<?php if ( bbp_is_favorites() ) {
					bbp_get_template_part( 'user', 'favorites' );
				} ?>
				<?php if ( bbp_is_subscriptions() ) {
					bbp_get_template_part( 'user', 'subscriptions' );
				} ?>
				<?php if ( bbp_is_single_user_topics() ) {
					bbp_get_template_part( 'user', 'topics-created' );
				} ?>
				<?php if ( bbp_is_single_user_replies() ) {
					bbp_get_template_part( 'user', 'replies-created' );
				} ?>
				<?php if ( bbp_is_single_user_edit() ) {
					bbp_get_template_part( 'form', 'user-edit' );
				} ?>
				<?php if ( bbp_is_single_user_profile() ) {
					bbp_get_template_part( 'user', 'profile' );
				} ?>
				<?php if ( bbp_is_single_user_engagements() ) {
					bbp_get_template_part( 'user', 'engagements' );
				} ?>

				<?php if ( wvrbbp_getopt( 'enable_email_link' ) && ( current_user_can( 'edit_users' ) || current_user_can( 'bbp_moderator' ) ) ) {
					$topic = get_bloginfo( 'name' );
					$topic = str_replace( array( 'https://', 'http://' ), '', $topic );
					echo 'Click to send Email: <a href="mailto:';
					bbp_displayed_user_field( 'user_email', 'edit' );
					echo '?Subject=' . $topic . '">';
					bbp_displayed_user_field( 'user_email', 'edit' );
					echo '</a>';
				} ?>
			</div>
		</div>

	<?php } else {
		echo '<h4>';
		_e( "Sorry, you don't have permission to view this profile.", 'weaver-for-bbpress' );
		echo '</h4>';
	}
	?>
</div>
