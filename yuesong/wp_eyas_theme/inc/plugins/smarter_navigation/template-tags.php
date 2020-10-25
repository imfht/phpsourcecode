<?php

// Replaces previous_post_link()
// if $fallback is set to true, previous_post_link() will be called if there is no post found
function previous_post_smart( $format = '%link', $title = '%title', $fallback = true, $in_same_cat = true, $excluded_categories = '' ) {
	return Smarter_Navigation::adjacent_post( $format, $title, true, $fallback, $in_same_cat, $excluded_categories );
}

// Replaces next_post_link()
// if $fallback is set to true, next_post_link() will be called if there is no post found
function next_post_smart( $format = '%link', $title = '%title', $fallback = true, $in_same_cat = true, $excluded_categories = '' ) {
	return Smarter_Navigation::adjacent_post( $format, $title, false, $fallback, $in_same_cat, $excluded_categories );
}

// Returns the previous or next post id in the set
function get_adjacent_id_smart( $previous = false ) {
	return Smarter_Navigation::get_adjacent_id( $previous );
}

// Displays a link to the persistent referrer
function referrer_link( $format = '%link', $title = '%title', $sep = '&raquo;', $sepdirection = 'left' ) {
	echo Smarter_Navigation::referrer_link( $format, $title, $sep, $sepdirection );
}

// Retrieve the category, based on the referrer URL. Useful if you have posts with multiple categories
function get_referrer_category() {
	global $posts;

	if ( ! $referrer_url = get_referrer_url( false ) )
		return false;

	foreach ( get_the_category( $posts[0]->ID ) as $cat ) {
		$cat_link = get_category_link( $cat->term_id );

		if ( false !== strpos( $referrer_url, $cat_link ) )
			return $cat;
	}

	return false;
}

// Retrieve the full referrer URL
function get_referrer_url() {
	return Smarter_Navigation::get_referrer_url();
}

