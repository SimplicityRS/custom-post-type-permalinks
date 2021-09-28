<?php

class CPTP_Module_Setting_Test extends WP_UnitTestCase {

	public function set_up() {
		/** @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;
		parent::set_up();

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		create_initial_taxonomies();
		$wp_rewrite->flush_rules();

		do_action( 'plugins_loaded' );
		delete_option( 'rewrite_rules' );
	}

	/**
	 * @test
	 */
	public function test_cptp_version() {
		$this->assertEquals( CPTP_VERSION, get_option( 'cptp_version' ) );
	}

}

