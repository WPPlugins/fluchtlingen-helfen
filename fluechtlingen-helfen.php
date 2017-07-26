<?php
/*
Plugin Name: Flüchtlingen helfen
Plugin URI: http://1manfactory.com/hilfe
Description: Dieses Banner verlinkt auf eine Übersichtskarte mit konkreten Möglichkeiten vor Ort Flüchtlingen zu helfen.
Author: Jürgen Schulze
Author URI: http://1manfactory.com
Version: 1.1
License: Free, do whatever you want with it.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


fh1man_set_lang_file();
add_action('admin_init', 'fh1man_register_settings' );
add_action('admin_menu', 'fh1man_plugin_admin_menu');
register_activation_hook(__FILE__, 'fh1man_activate');
register_deactivation_hook(__FILE__, 'fh1man_deactivate');
register_uninstall_hook(__FILE__, 'fh1man_uninstall');

function fh1man_register_settings() { // whitelist options
	register_setting( 'fh1man_option-group', 'fh1man_type' );
	register_setting( 'fh1man_option-group', 'fh1man_sticky' );
}


function fh1man_set_lang_file() {
	# set the language file
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('fh1man', $moFile);
	}
}

function fh1man_deactivate() {
	// needed
	delete_option('fh1man_type');
	delete_option('fh1man_sticky');
	
}

function fh1man_activate() {
	# setting default values
	add_option('fh1man_type', 'links');
	add_option('fh1man_sticky', '1');
}


function fh1man_uninstall() {
	# delete all data stored
	delete_option('fh1man_type');
	delete_option('fh1man_sticky');
	
}

function fh1man_plugin_admin_menu() {
	add_options_page(__('Flüchtlingen helfen Settings', 'fh1man'), "Flüchtlingen helfen", 'manage_options', basename(__FILE__), 'fh1man_plugin_options');
}


function fh1man_plugin_options(){

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	print '<h2>'.__('Flüchtlingen helfen Settings', 'fh1man').'</h2>';

	print '<form method="post" action="options.php" id="fh1man_form">';
	wp_nonce_field('update-options', '_wpnonce');
	settings_fields( 'fh1man_option-group');

	print'
		<input type="hidden" name="page_options" value="fh1man_type, fh1man_sticky" />
		<h3>'.__('Alignment', 'fh1man').'</h3>
		<input type="radio" name="fh1man_type" value="links" '.fh1man_checked("fh1man_type", "links").'/> '.__('Left', 'fh1man').'<br />
		<input type="radio" name="fh1man_type" value="rechts" '.fh1man_checked("fh1man_type", "rechts").'/> '.__('Right', 'fh1man').'<br />
		<h3>'.__('Stickiness', 'fh1man').'</h3>
		<input type="checkbox" name="fh1man_sticky" value="1" '.fh1man_checked("fh1man_sticky", "1").'/> '.__('make it sticky', 'fh1man').'<br />
		<input type="hidden" name="action" value="update" />
	';


	print '<p class="submit"><input type="submit" name="submit" value="'.__('Save Changes', 'fh1man').'" /></p>';

	print '</form>';
	

	
}



function fh1man_checked($checkOption, $checkValue) {
	return get_option($checkOption)==$checkValue ? " checked" : "";
}



add_action( 'wp_footer', 'fh1man_show_ribbon', PHP_INT_MAX );
function fh1man_show_ribbon() {
	
	if (get_option("fh1man_sticky")=="1") $position="fixed"; else $position="absolute";
		
	if (get_option("fh1man_type")=="links") {
		$ribbon_url = plugins_url( 'fluechtlingen-helfen', __FILE__ )."-links.png";
		$style="position: $position; top: 0; left: 0; z-index: 100000;";
		
	} else {
		$ribbon_url = plugins_url( 'fluechtlingen-helfen', __FILE__ )."-rechts.png";
		$style="position: $position; top: 0; right: 0; z-index: 100000;";
		
	}
	?>
	<a target="_blank" class="fluechtlingen-helfen-banner" href="https://www.google.com/maps/d/viewer?mid=zc6TdvfelKuY.kUvriXoSREXw"><img src="<?php echo $ribbon_url; ?>" alt="Flüchtlingen helfen" style="<?php print $style; ?>"></a>
	<?php
}