<?php
/**
 * Tests for plugin.
 *
 * @package pseudo-subdomain-network
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	/**
	 * Test the the site's domain, home, and siteurl are all modified when selected to.
	 */
	public function test__Pseudo_Subdomain_Network__map_to_subdomain() {

		// Create user and blog.
		$new_user = [
			'user_login' => 'jdoe',
			'user_pass'  => null,
			'role'       => 'administrator',
		];
		$user = $this->factory->user->create_and_get( $new_user );
		grant_super_admin( $user->ID );
		wp_set_current_user( $user->ID );
		$new_site = [
			'path' => '/foo',
		];
		$blog_id  = $this->factory()->blog->create( $new_site );

		// fake the requirments, as if checkbox was selected and form submitted.
		$_POST['blog']['domain_map']   = '1';
		$_REQUEST['_wpnonce_add-blog'] = wp_create_nonce( 'add-blog' );

		$network = new Pseudo_Subdomain_Network();
		$network->map_to_subdomain( $blog_id );

		switch_to_blog( $blog_id );

		$domain  = get_blog_details( $blog_id )->domain;
		$home    = get_option( 'home' );
		$siteurl = get_option( 'siteurl' );

		$this->assertEquals( $domain, 'foo.example.org', "wp_blog's `domain` column is incorrect" );
		$this->assertEquals( $home, 'http://foo.example.org', 'option `home` is incorrect' );
		$this->assertEquals( $siteurl, 'http://foo.example.org', 'option `siteurl` is incorrect' );

	}

	/**
	 * Test that the correct parts of the network URL are returned.
	 */
	public function test__Pseudo_Subdomain_Network__get_network_url_parts() {
		$network = new Pseudo_Subdomain_Network();
		$url     = $network->get_network_url_parts();
		$this->assertEquals( $url['scheme'], 'http://', 'scheme is incorrect' );
		$this->assertEquals( $url['domain'], 'example.org', 'domain is incorrect' );
		$this->assertEquals( $url['path'], '/', 'path is incorrect' );
	}
}
