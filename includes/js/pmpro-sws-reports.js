jQuery( document ).ready(function($) {
	var filename = 'pmpro-sws-reports.js';

	$("#pmpro_sws_sitewide_sale_select").selectWoo();
	$("#pmpro_sws_sitewide_sale_select").change(function() {
		var data = {
			'action': 'pmpro_sws_ajax_reporting',
			'sitewide_sale_id': $("#pmpro_sws_sitewide_sale_select").val()
		};
		$.post(ajaxurl, data, function(response) {
			$("#pmpro_sws_reports_container").html(response.slice(0, -1));
		});
	});
});
