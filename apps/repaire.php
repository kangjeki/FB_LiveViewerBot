<?php  
foreach ( glob(__DIR__."/payloads/*.json") as $d ) {
	$json = file_get_contents($d);
	$parse = json_decode($json, true);
	if ( is_null($parse) ) {
		$id = substr($json, 0, 15);
		$dtsg = substr($json, 15, strlen($json) );

		$newRepaire = ["user_id" => $id, "fb_dtsg" => $dtsg];
		file_put_contents( __DIR__ . "/payloads/" . $id . "_payload.json", json_encode($newRepaire) );
		var_dump($dtsg);
	}
}