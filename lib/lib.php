<?php
/**
 * Plugin Update Checker Library 5.3
 * http://w-shadow.com/
 *
 * Copyright 2022 Janis Elsts
 * Released under the MIT license. See license.txt for details.
 */

require dirname(__FILE__) . '/load-v5p3.php';


$wcmamtx_act_date = get_option('wcmamtx_act_date');

$wcmamtx_install_e = get_option('wcmamtx_install_e');


$date_today = date("Ymd");

if (!isset($wcmamtx_act_date) || ($wcmamtx_act_date == "")) {
	update_option('wcmamtx_act_date',$date_today);
}

if (!isset($wcmamtx_install_e) || ($wcmamtx_install_e == "")) {
	update_option('wcmamtx_install_e','32');
}

$wcmamtx_check_done_one = get_option('wcmamtx_check_done_one','no');

if ($wcmamtx_check_done_one == "no") {
	$wcmamtx_license_settings    = (array) get_option('wcmamtx_license_settings');

	$license_key = '';

	if (isset($wcmamtx_license_settings['license_key']) ) { 
		$license_key=$wcmamtx_license_settings['license_key']; 
	}

	$input = $_SERVER['SERVER_NAME'];


	$input = trim($input, '/');


	if (!preg_match('#^http(s)?://#', $input)) {
		$input = 'http://' . $input;
	}

	$urlParts = parse_url($input);


	$domain_name = preg_replace('/^www\./', '', $urlParts['host']);


	$siteurl = wcmamtx_get_siteurl();



	$json_url = 'https://www.sysbasics.com/wp-json/wp/v2/check-validation?domain='.$domain_name.'&code='.$license_key.'&tid=31059126&siteurl='. $siteurl.'&act='.$wcmamtx_act_date.'';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL, $json_url);
	$result = curl_exec($ch);
	curl_close($ch);

	update_option('wcmamtx_check_done_one','yes');
}