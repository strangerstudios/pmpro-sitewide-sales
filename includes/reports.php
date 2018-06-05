<?php

$options = pmprosws_get_options();
if ( false !== $options['discount_code_id'] && false !== $options['landing_page_post_id'] ) {
	echo '<hr><h2>Reporting</h2>';
}
