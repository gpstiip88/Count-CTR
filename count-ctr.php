<?php
/*
Plugin Name: Count CTR Plugin
Version: 1.0
Description: Counter for CTR of post's title
Author: Gabriele Pieretti
Author URI: http://www.stiip.it
*/

/** Add action Ritle pluin page */
add_action( 'admin_menu', 'my_plugin_menu' );

/** CSS Style Ritle pluin page  */
function admin_register_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/ritleadmin.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'admin_register_head');

/** Function Ritle pluin page  */
function my_plugin_menu() {
	add_menu_page( 'My Plugin Options', 'Ritle', 'manage_options', 'my-unique-identifier', 'my_plugin_options', 'dashicons-palmtree');
}

/** HTML Ritle pluin page only for admin users */
function my_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<h1>Ritle - #1 Ottimizza il Tuo Title</h1>';
	echo '<p>Inserisci il purchase code che ti è stato fornito in fase di acquisto</p>';
	echo '<table class="form-table">';
	echo '<tbody>';
	echo '<tr>';
	echo '<th scope="row">';
	echo '<label for="blogname">Codice API</label>';
	echo '</th>';
	echo '<td><input name="blogname" type="text" id="blogname" value="Inserisci il codice di API" class="regular-text"></td>';
	echo '</tr>';
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function calcCTR(){
	wp_enqueue_style( 'ctrCountPluginStylesheet', plugins_url( 'style.css', __FILE__ ) );
	wp_enqueue_script('ctrCountPluginJS', plugins_url( 'main.js', __FILE__ ), array('jquery'), '1.0', true);

	$title = html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' );
	if( $title != ''){


		$response = json_decode( wp_remote_retrieve_body(wp_remote_post('http://dev.stiip.it/api-ctr/calc-ctr.php', array(
				    'body'      => ['title' => $title],
				))) );
		$points = $response->points;
	} else {
		$points = 0;
	}

	echo '
<div class="text row">

	<div class="col-left-1" id="original-title">
		<h2>CTR Titolo Originale</h2>
			<div class="c100 p'.$points.'">
				<span class="points">'.$points.'%</span>
					<div class="slice">
				    	<div class="bar">
				    	</div>
					    <div class="fill">
					    </div>
					</div>
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
	</div>

	<div class="col-right">
	<h2> Titolo Alternativo </h2>
		<input style="width: 100%;" type="text" name="post_subtitle" value="" id="subtitle" spellcheck="true" autocomplete="off" placeholder="Confronta un titolo alternativo in tempo reale.">
		<div id="tips">';
	foreach ($response->tips as $tip => $text) {
		echo '<div class="cornice">
			<p><span id="'.$tip.'" class="text-font">Suggerimento: </span>'.$text.'</p>
		</div>';
	}

	echo '
		</div>
	</div>
</div>';

}

add_action('edit_form_after_title', 'calcCTR');