<?php
/**
 * Test for CPTP_Util.
 *
 * @package custom-post-type-permalinks
 */

/**
 * Class CPTP_Util_Test
 */
class CPTP_Util_Test extends WP_UnitTestCase {

	/**
	 * Setup.
	 */
	public function set_up() {
		/**
		 * WP_Rewrite
		 *
		 * @var WP_Rewrite $wp_rewrite
		 */
		global $wp_rewrite;
		parent::set_up();

		$wp_rewrite->init();
		$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%day%/%postname%/' );
		create_initial_taxonomies();
		$wp_rewrite->flush_rules();
		do_action( 'plugins_loaded' );
	}

	/**
	 * CPTP_Util::get_permalink_structure allow pass Object and String.
	 *
	 * @test
	 */
	public function test_get_permalink_structure_pass_object() {
		$post_type = rand_str( 12 );
		update_option( $post_type . '_structure', '/%year%/%monthnum%/%day%/%post_id%/' );
		register_post_type(
			$post_type,
			array(
				'public' => true,
			)
		);
		$post_type_object = get_post_type_object( $post_type );

		$this->assertEquals( CPTP_Util::get_permalink_structure( $post_type_object ), '/%year%/%monthnum%/%day%/%post_id%/' );
	}

	/**
	 * Check build structure from setting.
	 *
	 * @test
	 */
	public function test_get_permalink_structure_from_option() {
		$post_type = rand_str( 12 );
		register_post_type(
			$post_type,
			array(
				'public' => true,
			)
		);
		update_option( $post_type . '_structure', '/%year%/%monthnum%/%day%/%post_id%/' );

		$this->assertEquals( CPTP_Util::get_permalink_structure( $post_type ), '/%year%/%monthnum%/%day%/%post_id%/' );
	}

	/**
	 * Setting on code test.
	 *
	 * @test
	 */
	public function test_get_permalink_structure_from_arguments() {
		$post_type = rand_str( 12 );
		register_post_type(
			$post_type,
			array(
				'public'                   => true,
				'cptp_permalink_structure' => '/%year%/%monthnum%/%day%/%post_id%/',
			)
		);
		$this->assertEquals( CPTP_Util::get_permalink_structure( $post_type ), '/%year%/%monthnum%/%day%/%post_id%/' );
	}

	/**
	 * CPTP_Util::get_date_front test.
	 *
	 * Check no conflict /%year%/ and /%post_id%/
	 *
	 * @test
	 */
	public function test_get_date_front() {
		$post_type = rand_str( 12 );
		register_post_type(
			$post_type,
			array(
				'public' => true,
			)
		);
		update_option( $post_type . '_structure', '/%year%/%monthnum%/%day%/%post_id%/' );
		$this->assertEquals( CPTP_Util::get_date_front( $post_type ), '' );

		update_option( $post_type . '_structure', '/%post_id%/' );
		$this->assertEquals( CPTP_Util::get_date_front( $post_type ), '/date' );
	}

	/**
	 * CPTP_Util::sort_terms test.
	 *
	 * @test
	 */
	public function test_sort_terms() {
		for ( $i = 0; $i < 10; $i ++ ) {
			$this->factory->category->create(
				array(
					'name' => rand_str( 12 ),
				)
			);
		}

		$categories = get_categories(
			array(
				'hide_empty' => 0,
			)
		);
		$expected   = CPTP_Util::sort_terms( $categories );

		usort(
			$categories,
			function ( $a, $b ) {
				if ( $a->term_id > $b->term_id ) {
					return 1;
				} elseif ( $a->term_id < $b->term_id ) {
					return - 1;
				} else {
					return 0;
				}
			}
		);
		$this->assertEquals( $expected, $categories );
	}

}
