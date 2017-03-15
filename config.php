<?php 

function getJSON($servico){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://app.asana.com/api/1.0/'.$servico);
	curl_setopt($ch, CURLOPT_USERPWD, '2Xpo44G9.yTinP1rWSjuXOGtqXgy3fhB');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec ($ch);
	curl_close ($ch);
	return json_decode($output);
}



?>