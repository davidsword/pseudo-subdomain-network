/**
 * Minor tweaks for the new form field added to Network » Sites » Add New Site form
 *
 * No need to minify this file as it's use is so rare and on the admin side.
 */
jQuery(document).ready( function($) {

	// Unforunently, there's no hooks or filters so we need to "hack" this `tr` row into the table.
	$('#psdn--tr-row').insertAfter('table.form-table tr:nth-child(1)');

	// Wrapping the to-be-moved `tr` in a table keeps the browsers DOM happy, lets remove it now.
	$('#psdn--holder').remove();

	// Take the slug entered and preview what a subdomain URL will look like.
	const siteaddress = $('#site-address');
	const subdomainmapping = $('#psdn--subdomain-preview');

	siteaddress
		.keyup( domainpreview )
		.keypress( domainpreview );

	function domainpreview() {
		var slug = siteaddress.val();
		subdomainmapping.text( slug );

		// Our added form field is a little confusing when there's no slug. Toggle some things for +UX.
		if ( 0 === slug.length ) {
			$('#domain-map').removeAttr( 'checked' );
			$('#domain-map').attr( 'disabled', true );
			$('label[for=domain-map]').css( 'opacity', '0.75' );
			$('#psdn--descirption-enabled').hide();
			$('#psdn--descirption-disabled').show().css( 'opacity', '0.75' );
		} else {
			$('#domain-map').attr( 'checked', true );
			$('#domain-map').removeAttr( 'disabled' );
			$('label[for=domain-map]').css( 'opacity', '1' );
			$('#psdn--descirption-enabled').show();
			$('#psdn--descirption-disabled').hide();
		}
	}

	// Fire incase predefined - a super edge case for browsers during a soft-refresh.
	domainpreview();
});
