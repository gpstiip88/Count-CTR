<?php
/*
Plugin Name: Count CTR Plugin
Version: 1.0
Description: Counter for CTR of post's title
Author: Gabriele Pieretti
Author URI: http://www.stiip.it
*/


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
		<input style="width: 100%;" type="text" name="post_subtitle" value="" id="subtitle" spellcheck="true" autocomplete="off">
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