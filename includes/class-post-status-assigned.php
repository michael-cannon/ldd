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

require_once AIHR_DIR_LIB . 'wp_custom_post_status.php';

if ( class_exists( 'post_status_assigned' ) )
	return;


class post_status_assigned extends wp_custom_post_status
{
	/**
	 * @access protected
	 * @var string
	 */
	static protected $instance;


	/**
	 * Creates a new instance. Called on 'after_setup_theme'.
	 * May be used to access class methods from outside.
	 *
	 * @return void
	 */
	static public function init()
	{
		null === self :: $instance and self :: $instance = new self;
		return self :: $instance;
	}


	public function __construct()
	{
		// Set your data here. Only "$post_status" is required.
		$this->post_status = 'assigned';

		// The post types where you want to add the custom status. Allowed are string and array
		$this->post_type = LDD::PT;

		// @see parent class: defaults inside add_post_status()
		$this->args = array();

		parent :: __construct();
	}
}

?>