<?php
/*
	Copyright 2014 Michael Cannon (email: mc@aihr.us)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


function ldd_gettext( $translated ) {
	static $do_it;

	if ( is_null( $do_it ) ) {
		// why is this so dificult to figure what post_type we're currently on?
		$post_type = isset( $_GET['post_type'] ) ? esc_attr( $_GET['post_type'] ) : false;
		if ( ! $post_type ) {
			global $post;
			if ( ! is_null( $post ) )
				$post_type = get_the_ID() ? get_post_type( get_the_ID() ) : false;
		}

		if ( $post_type )
			$do_it     = in_array( $post_type, array( LDD::PT ) );
	}

	if ( ! $do_it )
		return $translated;

	$client     = 'Client';
	$translated = str_replace( 'Author', $client, $translated );

	return $translated;
}

add_filter( 'gettext', 'ldd_gettext' );

?>