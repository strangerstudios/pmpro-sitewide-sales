jQuery( document ).ready(function() {
	jQuery("#pmpro_sws_sitewide_sale_select").selectWoo();
	jQuery("#pmpro_sws_sitewide_sale_select").change(function() {
		var data = {
			'action': 'pmpro_sws_ajax_reporting',
			'sitewide_sale_id': jQuery("#pmpro_sws_sitewide_sale_select").val()
		};
		jQuery.post('<?php echo esc_html( admin_url( 'admin-ajax.php' ) ); ?>', data, function(response) {
			jQuery("#pmpro_sws_reports_container").html(response.slice(0, -1));
		});
	});
});
