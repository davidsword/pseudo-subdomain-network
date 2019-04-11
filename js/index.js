/**
 * Minor tweaks for the new form field added to Network » Sites » Add New Site form
 *
 * No need to minify this file as it's use is so rare and on the admin side.
 */
jQuery(document).ready( function($) {

	// Unforunently, there's no hooks or filters so we need to "hack" this `tr` row into the table.
	$('#npsdds--tr-row').insertAfter('table.form-table tr:nth-child(1)');

	// Wrapping the to-be-moved `tr` in a table keeps the browsers DOM happy, lets remove it now.
	$('#npsdds--holder').remove();

	// Take the slug entered and preview what a subdomain URL will look like.
	const siteaddress = $('#site-address');
	const subdomainmapping = $('#npsdds--subdomain-preview');

	siteaddress
		.keyup( domainpreview )
		.keypress( domainpreview );

	function domainpreview() {
		var slug = siteaddress.val();
		subdomainmapping.text( slug );

		// This setting is a little confusing when there's no slug. Toggle some things for +UX.
		if ( 0 === slug.length ) {
			console.dir( 'disabled' );
			$('#map-subdomain').removeAttr( 'checked' );
			$('#map-subdomain').attr( 'disabled', true );
			$('label[for=map-subdomain]').css( 'opacity', '0.75' );
			$('#npsdds--descirption-enabled').hide();
			$('#npsdds--descirption-disabled').show().css( 'opacity', '0.75' );
		} else {
			console.dir( 'not disabled' );
			$('#map-subdomain').attr( 'checked', true );
			$('#map-subdomain').removeAttr( 'disabled' );
			$('label[for=map-subdomain]').css( 'opacity', '1' );
			$('#npsdds--descirption-enabled').show();
			$('#npsdds--descirption-disabled').hide();
		}
	}

	// Fire incase predefined - a super edge case for browsers during a soft-refresh.
	domainpreview();
});
