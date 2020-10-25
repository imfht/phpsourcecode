<?php
/*
Plugin Name: Smarter Navigation
Description: Generates more specific previous / next post links based on referrer.
Author: scribu, versusbassz
Version: 1.3.2
Plugin URI: http://wordpress.org/extend/plugins/smarter-navigation/
*/

class Smarter_Navigation {
	const NAME = 'smarter-navigation';
	const SEP = '__SEP__';

	static $data = false;

	function init() {
		add_action( 'template_redirect', array( __CLASS__, 'manage_cookie' ) );
		add_action( 'posts_clauses', array( __CLASS__, 'posts_clauses' ), 10, 2 );
	}

	function manage_cookie() {
		// Default conditions
		$clear_condition = false;
		$read_condition = is_singular();
		$set_condition = !is_404();

		if ( apply_filters( 'smarter_nav_clear', $clear_condition ) )
			self::clear_cookie();
		elseif ( apply_filters( 'smarter_nav_read', $read_condition ) )
			self::read_cookie();
		elseif ( apply_filters( 'smarter_nav_set', $set_condition ) )
			self::set_cookie();
	}

	private function read_cookie() {
		if ( empty( $_COOKIE[self::NAME] ) )
			return;

		$data = $_COOKIE[self::NAME];

		// No referrer
		if ( !isset( $data['query'] ) )
			return;

		$data['query'] = json_decode( stripslashes( $data['query'] ), true );

		// JSON is invalid
		if ( is_null( $data['query'] ) )
			return;

		self::$data = $data;

		self::$cache['same'] = self::get_posts( array(
			'_sn_post' => get_queried_object(),
			'_sn_op' => '=',
			'order' => 'ASC',
			'fields' => 'ids',
			'nopaging' => true
		) );

		// The current post doesn't belong to the group
		if ( !in_array( get_queried_object_id(), self::$cache['same'] ) )
			self::$data = false;
	}

	public function set_cookie( $data = '' ) {
		$data = wp_parse_args( $data, array(
			'query' => json_encode( $GLOBALS['wp_query']->query ),
			'url' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'title' => trim( wp_title( self::SEP, false, 'left' ) ),
		) );

		foreach ( $data as $key => $value )
			setcookie( self::get_name( $key ), $value, 0, '/' );
	}

	public function clear_cookie() {
		if ( empty( $_COOKIE[self::NAME] ) )
			return;

		foreach ( array_keys( $_COOKIE[self::NAME] ) as $key )
			setcookie( self::get_name( $key ), false, 0, '/' );
	}

	private function get_name( $key ) {
		return self::NAME . '[' . $key . ']';
	}

	static function adjacent_post( $format, $title_format, $previous, $fallback, $in_same_cat, $excluded_categories ) {
		$id = self::get_adjacent_id( $previous );

		if ( !$id )
			return false;

		if ( -1 == $id ) {
			if ( !$fallback )
				return false;

			if ( $previous )
				return previous_post_link( $format, $title_format, $in_same_cat, $excluded_categories );
			else
				return next_post_link( $format, $title_format, $in_same_cat, $excluded_categories );
		}

		echo self::parse_format( $format, $title_format, get_permalink( $id ), get_the_title( $id ) );
	}

	private static $cache;

	/**
	 * @return -1 if there's no data, 0 if no post found, post id otherwise
	 */
	static function get_adjacent_id( $previous = false ) {
		if ( !self::$data )
			return -1;

		$previous = (bool) $previous;

		if ( !isset( self::$cache[$previous] ) ) {
			self::$cache[$previous] = self::find_adjacent_post( $previous );
		}

		return self::$cache[$previous];
	}

	private static function find_adjacent_post( $previous ) {
		$poz = array_search( get_queried_object_id(), self::$cache['same'] );

		$poz += $previous ? 1 : -1;

		if ( isset( self::$cache['same'][ $poz ] ) )
			return self::$cache['same'][ $poz ];

		// find first post in the adjacent interval
		$next_posts = self::get_posts( array(
			'_sn_post' => get_queried_object(),
			'_sn_op' => $previous ? '<' : '>',
			'order' => $previous ? 'DESC' : 'ASC',
			'posts_per_page' => 2,
			'paged' => 1
		) );

		if ( empty( $next_posts ) )
			return 0;

		$post = reset( $next_posts );

		if ( count( $next_posts ) == 1 )
			return $post->ID;

		// there's more than one post in the adjacent interval, so need to get the first/last one
		$final_posts = self::get_posts( array(
			'_sn_post' => $post,
			'_sn_op' => '=',
			'order' => 'ASC',
			'nopaging' => true,
		) );

		if ( $previous )
			return reset( $final_posts )->ID;

		return end( $final_posts )->ID;
	}

	private static function get_posts( $args = array() ) {
		$args =	array_merge( self::$data['query'], $args, array(
			'ignore_sticky_posts' => true
		) );

		$q = new WP_Query( $args );

		return $q->posts;
	}

	static function posts_clauses( $bits, $wp_query ) {
		global $wpdb;

		$op = $wp_query->get( '_sn_op' );
		$post = $wp_query->get( '_sn_post' );

		if ( !$op )
			return $bits;

		$orderby = preg_split( '|\s+|', $bits['orderby'] );
		$orderby = reset( $orderby );

		$field = explode( '.', $orderby );
		$field = end( $field );

		if ( isset( $post->$field ) ) {
			$bits['where'] .= $wpdb->prepare( " AND $orderby $op %s ", $post->$field );
		} else {
			$bits['where'] = ' AND 1 = 0';
		}

		return $bits;
	}

	static function referrer_link( $format = '%link', $title_format = '%title', $sep = '&raquo;', $sepdirection = 'left' ) {
		$url = self::get_referrer_url();
		if ( empty( $url ) )
			return;

		$title = self::get_title( $sep, $sepdirection );
		if ( empty( $title ) )
			return;

		echo self::parse_format( $format, $title_format, $url, $title );
	}

	static function get_referrer_url() {

		if ( !isset( self::$data['url'] ) || !isset( self::$data['query'] ) )
			return '';

		return self::$data['url'];

	}

	static function get_title( $sep, $sepdir ) {
		$sep = trim( $sep );

		if ( ! $title = @self::$data['title'] )
			$title = 'Referrer';

		$parts = array_slice( explode( self::SEP, $title ), 1 );

		if ( 'right' == $sepdir )
			$parts = array_reverse( $parts );

		return implode( " $sep ", $parts );
	}

	private static function parse_format( $link_format, $title_format, $url, $title ) {
		$title = str_replace( '%title', $title, $title_format );
		$link = sprintf( "<a href='%s'>%s</a>", $url, $title );
		return str_replace( '%link', $link, $link_format );
	}
}
Smarter_Navigation::init();

include dirname( __FILE__ ) . '/template-tags.php';

