<?php
/*
Plugin Name: Count CTR Plugin
Version: 1.0
Description: Counter for CTR of post's title
Author: Gabriele Pieretti
Author URI: http://www.stiip.it
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/** CSS Style Ritle pluin page  */
function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/ritleadmin.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'admin_register_head');

add_action( 'admin_menu', 'ritle_add_admin_menu' );
add_action( 'admin_init', 'ritle_settings_init' );


function ritle_add_admin_menu(  ) {

	add_menu_page( 'Ritle', 'Ritle', 'manage_options', 'ritle', 'ritle_options_page' );

}


function ritle_settings_init(  ) {

	register_setting( 'pluginPage', 'ritle_settings' );

	add_settings_section(
		'ritle_pluginPage_section',
		__( 'Inserisci il purchase code che ti &egrave; stato fornito in fase di acquisto', 'ritle' ),
		'ritle_settings_section_callback',
		'pluginPage'
	);

	add_settings_field(
		'purchase_code',
		__( 'Purchase Code', 'ritle' ),
		'purchase_code_render',
		'pluginPage',
		'ritle_pluginPage_section'
	);


}

function sample_admin_notice__update_nag_notice() { ?>
	<div class="update-nag notice is-dismissible">
		<h4>Per iniziare ad usare Tuno Plugin inserisci il purchase code nella pagina del plugin</h4>
		<p>Durante la fase di acquisto ti abbiamo inviato una mail con il purchase code. Recati sul menu "Tuno Plugin" ed inserisci il codice.</p>
	</div>
<?php
}
add_action( 'admin_notices', 'sample_admin_notice__update_nag_notice' );

function purchase_code_render(  ) {

	$options = get_option( 'ritle_settings' );
	?>
	<input type='text' name='ritle_settings[purchase_code]' value='<?php echo $options['purchase_code']; ?>'>
	<?php

}


function ritle_settings_section_callback(  ) {

	//echo __( 'This section description', 'ritle' );

}


function ritle_options_page(  ) {

	?>
	<form action='options.php' method='post'>

		<h2>Ritle</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}

function calcCTR(){
	$options = get_option( 'ritle_settings' );
	$purchase_code = $options['purchase_code'];

	if( ! $purchase_code ){
		echo 'Per favore Inserisci il tuo purchase code';
		return;
	}

	wp_enqueue_style( 'ctrCountPluginStylesheet', plugins_url( 'style.css', __FILE__ ) );
	wp_register_script('ctrCountPluginJS', plugins_url( 'main.js', __FILE__ ), array('jquery'), '1.0', true);

	$plugin_data = array(
		'purchase_code' => $purchase_code,
		'api_url' => 'http://dev.stiip.it/api-ctr/calc-ctr.php'
	);
	wp_localize_script( 'ctrCountPluginJS', 'ritle', $plugin_data );

	// Enqueued script with localized data.
	wp_enqueue_script( 'ctrCountPluginJS' );

	$title = html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' );
	if( $title != ''){

		$response = json_decode( wp_remote_retrieve_body(wp_remote_post($plugin_data['api_url'], array(
				    'body'      => ['title' => $title, 'purchaseCode' => $purchase_code],
				))) );
		$points = $response->points;
	} else {
		$points = 0;
	}

	echo '


	<div class="row">
		<h2> Titolo Alternativo </h2>
		<input style="width: 100%;" type="text" name="post_subtitle" value="" id="subtitle" spellcheck="true" autocomplete="off" placeholder="Confronta un titolo alternativo in tempo reale.">
	</div>
<div class="text row">

	<div class="col-left-1" id="original-title">
		<h2>CTR Titolo</h2>
		<div class="c100 p'.$points.'">
			<span class="points">'.$points.'%</span>
				<div class="slice">
			    	<div class="bar">
			    	</div>
				    <div class="fill">
				    </div>
				</div>
		</div>

		<div class="tips">';
		foreach ($response->tips as $tip => $text) {
			echo '<div class="cornice">
				<p><span class="text-font">Suggerimento: </span>'.$text.'</p>
			</div>';
		}

		echo '
		</div>
	</div>

	<div class="col-left-2" id="alternative-title">
		<h2>CTR Titolo Alternativo</h2>
		<div class="c100 p0">
			<span class="points hover-dif">0%</span>
				<div class="slice">
			    	<div style="border: 0.08em solid #aa0909;" class="bar">
			    	</div>
				    <!--<div style="border: 0.08em solid #aa0909;"  class="fill">
				    </div>-->
				</div>
		</div>
		<div class="tips">
		</div>
	</div>
</div>';

}

add_action('edit_form_after_title', 'calcCTR');