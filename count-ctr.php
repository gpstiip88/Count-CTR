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
	$title = html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' );
	if( $title == '')
		return;
	$points = 0;
	$powerwords = ['stupefacente', 'audacia', 'spina', 'dorsale', 'credenza', 'beato', 'coraggio', 'mozzafiato', 'esultare', 'conquistare', 'coraggio', 'audace', 'sfida', 'delizia', 'devoto', 'eccitato', 'fede', 'impavido', 'compiere', 'grato', 'grinta', 'coraggio', 'Contento', 'cuore', 'eroe', 'sperare', 'Sbalorditive', 'giubilante', 'magia', 'mitico', 'miracolo', 'cogliere', 'sensazionale', 'spettacolare', 'spirito', 'barcollante', 'splendido', 'stupendo', 'sorprendente', 'trionfo', 'edificante', 'valore', 'vittoria', 'meraviglioso', 'sfacciato', 'bramare', 'depravato', 'sporco', 'scoperto', 'smascherato', 'Proibito', 'ipnotico', 'lascivo', 'leccare', 'solitario', 'lussuria', 'nudo', 'Cattivo', 'provocatorio', 'scandaloso', 'sensuale', 'sesso', 'spudorato', 'peccaminoso', 'squallido', 'addormentato', 'sculacciare', 'coperto', 'di', 'vapore', 'sudato', 'allettante', 'pacchiano', 'entusiasmante', 'Non', 'censurato', 'sfrenato', 'frusta', 'abuso', 'arrogante', 'calci', 'in', 'culo', 'schiena', 'dorso', 'abbattere', 'dire', 'palle', 'prepotente', 'vigliacco', 'storto', 'schiacciare', 'disgustoso', 'male', 'Alimentazione', 'forzata', 'fallo', 'Odiare', 'saputello', 'bugie', 'ripugnante', 'Perdente', 'sdraiato', 'maglio', 'Avido', 'nazista', 'Non', 'buono', 'odioso', 'Rimborso', 'libbra', 'assurdo', 'punire', 'orrendo', 'spietato', 'Malato', 'stanco', 'compiaciuto', 'piagnucoloso', 'snob', 'altezzoso', 'bloccato', 'disonesto', 'spocchioso', 'moccioso', 'affare', 'migliore', 'miliardo', 'ben', 'di', 'dio', 'contanti', 'A', 'buon', 'mercato', 'sconto', 'euro', 'doppia', 'esplodere', 'extra', 'festa', 'fortuna', 'Gratuito', 'freebie', 'frenesia', 'frugale', 'dono', 'maggiore', 'poco', 'costoso', 'montepremi', 'lussuoso', 'segnato', 'massiccio', 'soldi', 'vantaggio', 'gratis', 'a', 'poco', 'premio', 'profitto', 'quadruplo', 'ridotto', 'ricco', 'risparmi', 'sei', 'cifre', 'razzo', 'elevato', 'ondata', 'tesoro', 'triplo', 'enorme', 'Anonimo', 'autentico', 'sostenuto', 'Di', 'successo', 'Annulla', 'Quando', 'Vuoi', 'certificato', 'approvato', 'garantito', 'corazzata', 'Tutta', 'la', 'vita', 'Soldi', 'Nessun', 'obbligo', 'a', 'prova', 'di', 'bomba', 'Senza', 'fare', 'domande', 'Nessun', 'rischio', 'Senza', 'rischi', 'Senza', 'obblighi', 'ufficiale', 'vita', 'privata', 'protetto', 'provacy', 'provata', 'A', 'prova', 'di', 'errore', 'rimborso', 'soddisfatto', 'rimborsati', 'o', 'soddisfatti', 'ricerca', 'risultati', 'sicuro', 'Testato', 'Provare', 'prima', 'di', 'acquistare', 'verificare', 'incondizionato', 'Nascosto', 'Porta', 'posteriore', 'Vietato', 'Dietro', 'le', 'quinte', 'Mercato', 'Nero', 'Lista', 'nera', 'o', 'Blacklist', 'Hacker', 'di', 'contrabbando', 'Censurato', 'proibito', 'confessioni', 'confidenziale', 'controverso', 'segreto', 'sotto', 'copertura', 'coprire', 'dimenticato', 'nascosto', 'illegale', 'addetto', 'ai', 'lavori', 'perso', 'fuorilegge', 'privato', 'segreti', 'strano', 'non', 'autorizzato', 'trattenuti', 'Agonia', 'Apocalisse', 'Armageddon', 'Assalto', 'Gioco', 'Battito', 'Guardarsi', 'da', 'Accecato', 'Sangue', 'Massacro', 'Raccapricciante', 'Sanguinoso', 'Bomba', 'Buffone', 'Imbranato', 'Cadavere', 'Catastrofe', 'Attenzione', 'Crollo', 'Cadavere', 'Pazzo', 'Azzoppare', 'Crisi', 'Pericolo', 'Mortale', 'Morte', 'Distruggere', 'Devastante', 'Disastroso', 'Annegamento', 'Muto', 'Imbarazzare', 'Fallire', 'Debole', 'Licenziato', 'Ingannato', 'Stupido', 'Frenetico', 'Spaventoso', 'Giochi', 'd’azzardo', 'Ingenuo', 'Violato', 'Pericoloso', 'Beffa', 'Olocausto', 'Orribile', 'Uragano', 'Insidioso', 'Invasione', 'Agenzia', 'delle', 'Entrate', 'Carcere', 'Pericolo', 'Causa', 'Incombente', 'Pazzo', 'Agguato', 'Collasso', 'Impantanato', 'Errore', 'Omicidio', 'Incubo', 'Doloroso', 'Pallido', 'Panico', 'Pericolo', 'Piranha', 'Trappola', 'Peste', 'Giocata', 'Tuffo', 'Veleno', 'Menato', 'Povero', 'Pus', 'Cancro', 'Calcolo', 'Rifugiato', 'Vendetta', 'Rischioso', 'Attentatore', 'Pauroso', 'Urlo', 'Violento', 'Ustionante', 'Frantumare', 'Sciocco', 'Massacro', 'Schiavo', 'Distruggere', 'Strangolare', 'Trafficante', 'Succhiare', 'Carro', 'armato', 'Mirata', 'Terrore', 'Terrorista', 'Tossico', 'Trappola', 'Passante', 'Vaporizzare', 'Vittima', 'Giovane', 'Volatile', 'Vulnerabile', 'Avvertimento', 'Avviso', 'Preoccuparsi', 'Ferito'];

	// search for digits
	if( preg_match_all('#\d#', $title, $digits, PREG_OFFSET_CAPTURE) ){
		$points += 10;

		// check position
		foreach ($digits[0] as $digit) {
			if($digit[1] == 0)
				$points += 5;
			elseif($digit[1] == 1)
				$points += 4;
			elseif($digit[1] == 2)
				$points += 3;
			elseif($digit[1] == 3)
				$points += 2;
			else
				$points += 1;
		}
	}

	// search for at least two uppercase letters
	if( $upper = preg_match_all('#[A-Z]#', $title) ){
		if( $upper >= 2 )
			$points += 10;
	}

	// search for powerwords
	$listpowerwords = implode('|', $powerwords);
	if( $res = preg_match_all('#\b('.$listpowerwords.')\b#i', $title, $pwords)){
		$points += 20;
		if($res > 1)
			$points += ($res-1)*25;

	}

	// get total words
	$length = str_word_count($title);
	if( $length <= 4 )
		$points += 2;
	elseif( $length > 4 && $length <= 7 )
		$points += 5;
	elseif($length > 7 && $length < 9)
		$points += 6;

	// check for special chars
	if( $res = preg_match('/[\\\'|!"£$%&\/\()=?^+*\[\]ç@°#§<>,;.:-_]/', $title) ){
		$points += 5;
	}

	echo '<strong>Punteggio:</strong> '.$points;

}

add_action('edit_form_after_title', 'calcCTR');