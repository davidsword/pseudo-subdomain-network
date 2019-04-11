/**
 * Minor tweaks for the new form field added to Network » Sites » Add New Site form
 *
 * No need to minify this file as it's use is so rare and on the admin side.
 */
jQuery(document).ready( function($) {

	// Unforunently, there's no hooks or filters so we need to "hack" this `tr` row into the table.
	$('#domain-mapping-options').insertAfter('table.form-table tr:nth-child(1)');

	// Wrapping the to-be-moved `tr` in a table keeps the browsers DOM happy, lets remove it now.
	$('#domain-mapping-options-holder').remove();

	// Take the slug entered and preview what a subdomain URL will look like.
	const siteaddress = $('#site-address');
	const subdomainmapping = $('#sub-domain-mapping');

	siteaddress
		.keyup( domainpreview )
		.keypress( domainpreview );

	function domainpreview() {
		var slug = siteaddress.val();
		subdomainmapping.text( slug );

		// This setting is a little confusing when there's no slug. Toggle some things for +UX.
		if ( 0 === slug.length ) {
			console.dir( 'disabled' );
			$('#domain-map').removeAttr( 'checked' );
			$('#domain-map').attr( 'disabled', true );
			$('label[for=domain-map]').css( 'opacity', '0.5' );
			$('#domain-map--description').hide();
		} else {
			console.dir( 'not disabled' );
			$('#domain-map').attr( 'checked', true );
			$('#domain-map').removeAttr( 'disabled' );
			$('label[for=domain-map]').css( 'opacity', '1' );
			$('#domain-map--description').show();
		}
	}

	// Fire incase predefined - a super edge case for browsers during a soft-refresh.
	domainpreview();
});
