jQuery( document ).ready(function($) {
	var filename = 'pmpro-sws-reports.js';
	// alert(filename + ' is sorta fixed.  Specify ajax url with ajaxurl => ' + ajaxurl);
	$('.select2-container:after').css({'content':'dropdown doesn\'t seem to work'});
	$("#reports-landing").html('<h3 style="color:salmon;">' +filename + ' is sorta fixed. Can\'t use php. Specify ajax url in dashboard with ajaxurl => ' + ajaxurl + '</h3>');


	$("#pmpro_sws_sitewide_sale_select").selectWoo();
	$("#pmpro_sws_sitewide_sale_select").change(function() {
		var data = {
			'action': 'pmpro_sws_ajax_reporting',
			'sitewide_sale_id': $("#pmpro_sws_sitewide_sale_select").val()
		};
		$.post(ajaxurl, data, function(response) {
			// $("#pmpro_sws_reports_container").html(response.slice(0, -1));
			$("#reports-landing").html('<h4 style="color:salmon;">get something here is fun! Dropdown needs the php helper function to be able to return something ' + response + '</h4>');
		});
	});
});
