<?php
/*
Plugin Name: OIPA API controller
Description: This plugin defines the main configurations required to integrate the OIPA Search API.
Version: 0.2
Author: Zimmerman & Zimmerman, 
License: AGPLV3
*/


function install_Oipa(){
	$publisher_id = get_option('oipa_pubid'); //pub key value
	$newUser=false;

	if (get_option('oipa_version') == '') {
		update_option('oipa_version', '1x');
	}

	if (get_option('oipa_per_page') == '') {
		update_option('oipa_per_page', '20');
	}

	if (get_option('oipa_empty_label') == '') {
		update_option('oipa_empty_label', 'No information available');
	}
	
	if(empty($publisher_id)){
		$newUser=true;
		update_option('oipa_pubid',trim(makePubkey()));
	}
	
}

if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
	install_Oipa();
}

function oipa_request_handler() {
	if (!empty($_REQUEST['oipa_action'])) {
		switch ($_REQUEST['oipa_action']) {
			case 'oipa_update_settings':
				if (ak_can_update_options()) {
					
					if(!empty($_POST['oipa_pkey'])){
						update_option('oipa_pubid', $_POST['oipa_pkey']);
					}
					if(!empty($_POST['oipa_search_url'])){
						$oipa_search_url=$_POST['oipa_search_url'];
						//$tagsin=htmlspecialchars_decode($tagsin);
						$oipa_search_url=trim($oipa_search_url);
						update_option('oipa_search_url',$oipa_search_url);
					}
					if(!empty($_POST['default_organisation'])){
						$default_organisation=$_POST['default_organisation'];
						//$tagsin=htmlspecialchars_decode($tagsin);
						$default_organisation=trim($default_organisation);
						update_option('oipa_default_organisation',$default_organisation);
					}
					if(!empty($_POST['empty_label'])){
						$empty_label=$_POST['empty_label'];
						//$tagsin=htmlspecialchars_decode($tagsin);
						$empty_label=trim($empty_label);
						update_option('oipa_empty_label',$empty_label);
					}
					if(!empty($_POST['per_page'])){
						$per_page=$_POST['per_page'];
						$per_page=intval($per_page);
						update_option('oipa_per_page',$per_page);
					}
					
					header('Location: '.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=oipa.php&updated=true');
					die();
				}

				break;
		}
	}
}


function oipa_options_form() {
	
	$plugin_location=WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	
	$publisher_id = get_option('oipa_pubid');
	$oipa_search_url = get_option('oipa_search_url');
	$default_organisation = get_option('oipa_default_organisation');
	$empty_label = get_option('oipa_empty_label');
	$per_page = get_option('oipa_per_page');
	
	
	print('
		<script type="text/javascript" src="'.$plugin_location.'jquery.min.js"></script> 
		<link rel="stylesheet" type="text/css" href="'.$plugin_location.'oipa.css"/>		
		
		
		
		
			<div class="wrap">
			
				<h2>'.__('OIPA Options').'</h2>
				<div style="padding:10px;border:1px solid #aaa;background-color:#9fde33;text-align:center;display:none;" id="oipa_updated">Your options were successfully updated</div>
				<form id="ak_oipa" name="ak_oipa" action="'.get_bloginfo('wpurl').'/wp-admin/index.php" method="post">
					<fieldset class="options">
						<div class="oipa_options">
													
							<div class="searchurl">
								<span class="heading">Enter the OIPA API search URL:</span><br />
								<input type="text" name="oipa_search_url" id="oipa_search_url" style="height: 30px; width: 400px;" value="'.$oipa_search_url.'">
							</div>
							<br/>
							<div class="organisation">
								<span class="heading">Please enter the default organisation ID (leave empty if no organisation filter required):</span><br />
								<input type="text" name="default_organisation" id="default_organisation" style="height: 30px; width: 400px;" value="'.$default_organisation.'">
							</div>
							<br/>
							<div class="organisation">
								<span class="heading">Default empty label:</span><br />
								<input type="text" name="empty_label" id="empty_label" style="height: 30px; width: 400px;" value="'.$empty_label.'">
							</div>
							<br/>
							<div class="organisation">
								<span class="heading">Number of items per page:</span><br />
								<input type="text" name="per_page" id="per_page" style="height: 30px; width: 400px;" value="'.$per_page.'">
							</div>
							
							
						</div>						
	');
	
	
	print('
						
					</fieldset>
					<p class="submit">
						<input type="submit" name="submit_button" value="'.__('Update OIPA Options').'" />
					</p>
					

					<input type="hidden" name="oipa_action" value="oipa_update_settings" />
				</form>
				
			</div>
	');
}

function oipa_menu_items() {
	if (ak_can_update_options()) {
		add_options_page(
		__('OIPA Options')
		, __('OIPA')
		, 'manage_options'
		, basename(__FILE__)
		, 'oipa_options_form'
		);
	}
}

function makePubkey(){
	return "wp.".sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),mt_rand( 0, 0x0fff ) | 0x4000,mt_rand( 0, 0x3fff ) | 0x8000,mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
	// return "paste-your-publisher-key-here";
}
add_action('init', 'oipa_request_handler', 9999);
add_action('admin_menu', 'oipa_menu_items');
?>