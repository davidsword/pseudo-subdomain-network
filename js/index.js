/**
 * Minor tweaks for the new form field added to Network » Sites » Add New Site form
 *
 * No need to minify this file as it's use is so rare and on the admin side.
 */
jQuery(document).ready( function($) {

	// Unforunently, there's no hooks or filters so we need to "hack" this `tr` tow into the table.
	$('#domain-mapping-options').insertAfter('table.form-table tr:nth-child(1)');

	// Wrapping the to-be-moved `tr` in a table keeps the browsers DOM happy, remove once empty.
	$('#domain-mapping-options-holder').remove();

	// Take the slug entered and preview what a subdomain URL will look like.
	const siteaddress = $('#site-address');
	const subdomainmapping = $('#sub-domain-mapping');

	siteaddress
		.keyup( domainpreview )
		.keypress( domainpreview );

	function domainpreview() {
		subdomainmapping.text( siteaddress.val() );
	}

	// Fire incase predefined - a super edge case for browsers during a soft-refresh.
	domainpreview();
});
