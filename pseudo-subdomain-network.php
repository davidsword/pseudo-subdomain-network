<?php
/**
 * Plugin Name:     pseudo-subdomain-network
 * Plugin URI:      https://github.com/davidsword/pseudo-subdomain-network/
 * Description:     Plugin - For subfolder networks, have the Create New Site form site the home and siteurl's to a subdomain
 * Author:          davidsword
 * Author URI:      https://davidsword.ca/
 * Text Domain:     psdn
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         pseudo-subdomain-network
 */

// Prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Don't waste resources loading if not admin or not proper Network setup.
 *
 * @TODO check for www ? `strstr( get_network()->domain, 'www.' )`
 * @TODO add an admin_notice here.
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
		$new_domain = preg_replace( '|^www\.|', '', get_network()->domain );
		$path       = get_network()->path;
		$add_path   = ( '/' === $path ) ? '' : $path;
		$prefix     = is_ssl() ? 'https' : 'http';
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
							esc_html__( 'Set the new sites %s and %s option values to', 'psdn' ),
							'<strong>home</strong>',
							'<strong>siteurl</strong>'
						);
						?>:
						<code>
							<?php echo esc_html( $prefix ) . '://'; ?><span id='psdn--subdomain-preview'></span>.<?php echo esc_html( $new_domain . $add_path ); ?>
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

		// Retrieve the just-created sanitized path from the database.
		$path = get_blog_details( $blog_id, false )->path;
		$slug = trim( $path, '/' );

		// Don't make things complicated.
		if ( 'www' === $slug ) {
			return;
		}

		// Get the network domain and path.
		$network_domain = preg_replace( '|^www\.|', '', get_network()->domain );
		$network_path   = get_network()->path;
		$prefix         = is_ssl() ? 'https' : 'http';

		$new_domain = esc_url( $prefix . '://' . $slug . '.' . $network_domain . $network_path );

		// The ol' switcher'oo.
		switch_to_blog( $blog_id );

		/**
		 * Domain map the subdomain!
		 *
		 * @see https://wordpress.org/support/article/wordpress-multisite-map-subdomainping/
		 */
		update_option( 'home', $new_domain );
		update_option( 'siteurl', $new_domain );

		// The ol' switcher'oo, back to the network admin.
		restore_current_blog();
	}

}

new Network_pseudo_Sub_Domains();
