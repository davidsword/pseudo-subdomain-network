<?php
/**
 * Plugin Name:     Pseudo Subdomain Network
 * Plugin URI:      https://github.com/davidsword/pseudo-subdomain-network/
 * Description:     On a WordPress Network site, using a Subdirectories (path-based) install for sub sites, this plugin adds an option in to quickly Domain Map the Subdirectory site as a subdomain of the network.
 * Author:          davidsword
 * Author URI:      https://davidsword.ca/
 * Text Domain:     psdn
 * Domain Path:     /languages
 * Version:         1.0.0
 * Network:         true
 *
 * @package         pseudo-subdomain-network
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Don't waste resources loading if not admin or not proper Network setup.
 *
 * @TODO add an `admin_notice` to let admin know why the plugin might not be working.
 */
if ( ! is_admin() || ! is_multisite() || is_subdomain_install() ) {
	return;
}

/**
 * Network_pseudo_Sub_Domains
 */
class Network_pseudo_Sub_Domains {

	/**
	 * Build'r up!
	 */
	public function __construct() {
		$this->hook_into_wp();
	}

	/**
	 * Hook on'into WordPress.
	 */
	public function hook_into_wp() {

		// The follow hooks are very specific, so we don't need to do any page_hook condtionals.
		add_action( 'admin_print_footer_scripts-site-new.php', [ $this, 'enqueue_script' ] );
		add_action( 'network_site_new_form', [ $this, 'add_network_form_row' ], 99 );

		// On Network » Sites » Add New Site form submission, process the added field.
		add_action( 'wpmu_new_blog', [ $this, 'map_to_subdomain' ] );
	}

	/**
	 * Add a feild to Network » Sites » Create New.
	 *
	 * Will add a checkbox to "Map this site as a subdomain"
	 * The JS populates a preview of what the subdomain will look like, based on the slug entered.
	 */
	public function add_network_form_row() {
		$network_url = $this->get_network_url_parts();
		?>
		<table id='psdn--holder'>
			<tr class="form-field" id="psdn--tr-row">
				<th scope="row">
					<?php esc_html_e( 'Domain Map Subdomain', 'psdn' ); ?>
				</th>
				<td>
					<input name="blog[domain_map]" type="checkbox" id="map-subdomain" required="" value="1">
					<label for="map-subdomain">
						<?php esc_html_e( 'Map this new site slug as a subdomain', 'psdn' ); ?>
					</label><br />
					<p class='description' id='psdn--descirption-disabled'>
						<?php esc_html_e( 'Enter a Site Address above to enable this option.', 'psdn' ); ?>
					</p>
					<p class='description' id='psdn--descirption-enabled'>
						<?php
						echo sprintf(
							// translators: database options names.
							esc_html__( 'This will set the domain, %1$s and %2$s option values to', 'psdn' ),
							'<strong>home</strong>',
							'<strong>siteurl</strong>'
						);
						?>
						<code>
							<?php echo esc_html( $network_url['scheme'] ); ?><span id='psdn--subdomain-preview'></span>.<?php echo esc_html( $network_url['domain'] . $network_url['path'] ); ?>
						</code>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Enqueue JS into the Network » Sites » Add New Site form.
	 */
	public function enqueue_script() {
		wp_enqueue_script(
			'pseudo-subdomain-network-js',
			plugins_url( 'js/index.js', __FILE__ ),
			[ 'jquery' ],
			// expensive parsing, but this is only run on this form, so it's acceptable.
			SCRIPT_DEBUG ? time() : get_plugin_data( __FILE__ )->Version,
			true
		);
	}

	/**
	 * Maybe map the subfolder network site as a sub domain.
	 *
	 * Fires on Network » Sites » Add New Site form submission.
	 *
	 * @param int $blog_id the ID of the blog that was just created.
	 */
	public function map_to_subdomain( $blog_id ) {
		if ( false === check_admin_referer( 'add-blog', '_wpnonce_add-blog' ) ) {
			wp_die( esc_html__( 'Nice try.', 'psdn' ) );
		}
		if ( ! current_user_can( 'manage_sites' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to add sites to this network.', 'psdn' ) );
		}
		// Check that this subdomain is actually wanted.
		if ( ! isset( $_POST['blog']['domain_map'] ) || '1' !== ( $_POST['blog']['domain_map'] ) ) { //phpcs:ignore
			return;
		}

		// Retrieve the just-created blog path from the database.
		$slug = trim( get_blog_details( $blog_id )->path, '/' );

		// Don't make things complicated, if www just ignore, edge case.
		if ( 'www' === $slug ) {
			return;
		}

		// Get the network domain and path.
		$network_url = $this->get_network_url_parts();

		// Build the new URL.
		$new_domain = $slug . '.' . $network_url['domain'];
		$new_url    = esc_url( $network_url['scheme'] . $new_domain . $network_url['path'] );

		// The ol' switcher'oo.
		switch_to_blog( $blog_id );

		/**
		 * Domain map the subdomain!
		 *
		 * @see https://wordpress.org/support/article/wordpress-multisite-domain-mapping/
		 */
		$new_blog_details = [
			'domain' => $new_domain,
			'path'   => $network_url['path']
		];
		update_blog_details( $blog_id, $new_blog_details );
		update_option( 'home', $new_url );
		update_option( 'siteurl', $new_url );

		// The ol' switcher'oo, back to the network admin.
		restore_current_blog();
	}

	/**
	 * Get network URL detials.
	 *
	 * Note that the domain strips the "www." out.
	 *
	 * @return array URL parts of network URL.
	 */
	public function get_network_url_parts() {
		$scheme = is_ssl() ? 'https' : 'http';
		$domain = preg_replace( '|^www\.|', '', get_network()->domain );
		$path   = get_network()->path;
		return [
			'scheme' => $scheme . '://',
			'domain' => $domain,
			'path'   => $path,
		];
	}

}

new Network_pseudo_Sub_Domains();
