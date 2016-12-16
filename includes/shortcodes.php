<?php
/**
 * Shortcodes
 *
 * @since 0.0.2
 */

/**
 * mtollwc_courses_not_taken_func
 * Adds shortcode for untaken courses in sensei
 *
 * @param $atts string
 * @return string
 * @since 0.0.2
 */
function mtollwc_courses_not_taken_func( $atts ) {
	// course query parameters to be used for all courses
	$query_args = array(
		'post_type'        => 'course',
		'post_status'      => 'publish',
		'posts_per_page' => -1,
	);

	// get all the courses 
	$all_courses_query = new WP_Query( $query_args );

	$uid = get_current_user_id();

	$courses_not_taken = array();
	$wpg = array();
	$status = array();
	// look through all courses
	foreach( $all_courses_query->posts as $course ){

		// only keep the courses that the user not taking
		if( ! Sensei_Utils::user_started_course( $course->ID, $uid ) ){
			$courses_not_taken[] = $course->ID;
		}

	} // end foreach

	// if empty, will do full query...not what we want
	if ( empty( $courses_not_taken ) ) {
		return '';
	}
	// setup the course query again and only use the course the user has not started.
	// this query will be loaded into the global WP_Query in the render function.
	$query_args[ 'post__in' ] = $courses_not_taken;

	$query = new WP_Query( $query_args );

	global $wp_query;

	if ( ! is_user_logged_in() ) {

	    $anchor_before = '<a href="' . esc_url( sensei_user_login_url() ) . '" >';
	    $anchor_after = '</a>';
	    $notice = sprintf(
	        __('You must be logged in. Click here to %slogin%s.', 'mtollwc' ),
	        $anchor_before,
	        $anchor_after
	    );

	    Sensei()->notices->add_notice( $notice, 'info' );
	    Sensei()->notices->maybe_print_notices();

	    return '';
	}

	// keep a reference to old query
	$current_global_query = $wp_query;
	// assign the query
	$wp_query = $query;

	ob_start();
	Sensei()->notices->maybe_print_notices();
	Sensei_Templates::get_template('loop-course.php');
	$shortcode_output =  ob_get_clean();

	//restore old query
	$wp_query = $current_global_query;

	return $shortcode_output;
}
add_shortcode( 'mtollwc_courses_not_taken', 'mtollwc_courses_not_taken_func' );
