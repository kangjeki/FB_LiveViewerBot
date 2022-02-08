<?php
/**
 * injector viewer facebook live stream
 *
 * @package 	: jcbooster
 * @author 		: imam nasrudin 
 * @copyright 	: jcbooster | artaniaga.com
 * @version 	: 1.0.0
 *
 * @create | @update-script	: -
 *
 * -------------------------------------------------------------------------------------------------
 * @description : description
*/

set_time_limit(60*60*5);
$config = [];

function _curl($config) {
	$url = $config["url"];
	$header = $config["header"];
	$cookie = $config["cookie"];
	$RTRF = $config["return_transfer"];
	$referer = $config["referer"];
	$fields = $config["fields"];
	$httpheader = $config["httpheader"];
	$method = $config["method"];
	$video_id = false;
	$user_id = false;
	$fb_dtsg = false;

	if ( isset($config["video_id"]) ) {
		$video_id = $config["video_id"];
	}

	if ( isset($config["user_id"]) ) {
		$user_id = $config["user_id"];
	}

	if ( isset($config["fb_dtsg"]) ) {
		$fb_dtsg = $config["fb_dtsg"];
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_HEADER, $header);
	curl_setopt($ch, CURLOPT_NOBODY, $header);	
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie );
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );	
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

	if ( $httpheader ) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader );
	}
	if ( $fields ) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );
	}
	
	if ($RTRF) {
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	}
	
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.157 Safari/537.36");
	curl_setopt($ch, CURLOPT_REFERER, $referer);

	$result = curl_exec($ch);

    if (curl_errno($ch) != 0 && empty($result)) {
        $result = false;
    }
    curl_close($ch);

    return $result;
}


if ( isset($_POST["login"]) || isset($_GET["login"])) {
	if ( isset($_POST["login"]) ) {
		$EMAIL      = urlencode( htmlspecialchars($_POST["email"]) );
		$PASSWORD   = urlencode( htmlspecialchars($_POST["password"]) );	
	}
	if ( isset($_GET["login"]) ) {
		$EMAIL      = urlencode( htmlspecialchars($_GET["email"]) );
		$PASSWORD   = urlencode( htmlspecialchars($_GET["password"]) );	
	}
	

	function setLoginFB($EMAIL, $PASSWORD) {
		/* mengelompokan cookie user */
		$sessCP = urldecode( $EMAIL );
		$cookieFile = __DIR__ . "/cookies/$sessCP"."_cookie.txt";
		file_put_contents( $cookieFile, "");
		$config = [
			"url" => "https://www.facebook.com",
			"header" => false,
			"cookie" => $cookieFile,
			"return_transfer" => true,
			"referer" => "https://www.facebook.com",
			"fields" => false,
			"httpheader" => false,
			"method" => "GET"
		];
		$getResponse = _curl($config);	

		preg_match_all('!action="(.*?)"!', $getResponse, $actionURL);
		$action = $actionURL[1][0];
		preg_match_all('!"jazoest" value="(.*?)"!', $getResponse, $jazoest);
		$jazoest = $jazoest[1][0];
		preg_match_all('!"lsd" value="(.*?)"!', $getResponse, $lsd);
		$lsd = $lsd[1][0];

		$httpheader = [
			"accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
			"accept-language: en-US,en;q=0.9",
			"cache-control: max-age=0",
			"content-type: application/x-www-form-urlencoded",
			"sec-fetch-dest: document",
			"sec-fetch-mode: navigate",
			"sec-fetch-site: same-origin",
			"sec-fetch-user: ?1",
			"sec-gpc: 1",
			"upgrade-insecure-requests: 1"
		];

		/* request login */		
		$fields = [
			"jazoest" => $jazoest,
			"lsd" => $lsd,
			"email" => $EMAIL,
			"pass" => $PASSWORD,
			"login_source" => "comet_headerless_login",
			"next" => ""
		];
		$fields = json_encode( $fields, true);
		$config = [
			"url" => "https://www.facebook.com" . $action,
			"header" => false,
			"cookie" => $cookieFile,
			"return_transfer" => true,
			"referer" => "https://www.facebook.com",
			"httpheader" => false,
			"fields" => "jazoest=$jazoest&lsd=$lsd&email=$EMAIL&login_source=comet_headerless_login&next=&pass=$PASSWORD",
			"method" => "POST"
		];
		$getResponse = _curl($config);

		echo $getResponse;
		exit;
		$config = [
			"url" => "https://www.facebook.com/",
			"header" => false,
			"cookie" => $cookieFile,
			"return_transfer" => true,
			"referer" => "http://www.facebook.com",
			"httpheader" => false,
			"fields" => false,
			"method" => "GET"
		];
		$getResponse = _curl($config);

		preg_match('!&__user=(.+?)&!',$getResponse, $user_id);
		preg_match('!{"token":"(.+?)"}!',$getResponse, $fb_dtsg);
		$payloadFile = __DIR__ . "/payloads/$sessCP"."_payload.json";

		if ( count($user_id) > 0 ) {
			/* save payload by email login */
			file_put_contents( $payloadFile, json_encode(["user_id" => $user_id[1], "fb_dtsg" => $fb_dtsg[1]]) );

			/* set reporter */
			$reporter = json_encode(["status" => true, "message" => "login $EMAIL success!", "data" => ["user_id" => $user_id[1],"email"=> $EMAIL, "password" => $PASSWORD]]);
			echo "<reporter>". $reporter ."</reporter>";
		}
		else {
			/* remove cookie */
			if ( file_exists($cookieFile) ) unlink( $cookieFile );
			/* remove payload */
			if (file_exists($payloadFile) ) unlink($payloadFile);

			$reporter = json_encode(["status" => false, "message" => "login $EMAIL failed!", "data" =>["user_id" => "","email"=> $EMAIL, "password" => $PASSWORD]]);
			echo "<reporter>". $reporter ."</reporter>";
		}
	}
	
	setLoginFB($EMAIL, $PASSWORD);
	exit;
}


if ( isset($_POST["set-viewer"]) ) {
	$liveURL = htmlspecialchars($_POST["live-url"]);
	$durasi = htmlspecialchars($_POST["durasi"]);
	/* jumlah atau index total viewer*/
	$jumlah = htmlspecialchars($_POST["jumlah"]);
	$delay = htmlspecialchars($_POST["delay"]);

	function setViewer($liveURL, $cookieFile, $payloadFile) {
		$payload = json_decode( file_get_contents($payloadFile), true );

		$fb_dtsg = $payload["fb_dtsg"];
		$user_id = $payload["user_id"];
		$defTK = "s+cqpiOUsBKiNg+NSJXGNlPT5v509Pc7Dw842Qooxeq4aiRWbcduGWpw+h8oqXRV14SiE4jsVN41CAGC0Z0P2A==";
		$dt_tk = ( isset($payload["token"]) ) ? $payload["token"] : $defTK;
		$video_id = explode('videos/', $liveURL);
		$video_id = str_replace("/", "", end($video_id));

		$httpheader = [
			"accept: */*",
			"accept-language: en-US,en;q=0.9",
			"content-type: application/x-www-form-urlencoded",
			"sec-fetch-dest: empty",
			"sec-fetch-mode: cors",
			"sec-fetch-site: same-origin",
			"sec-gpc: 1"
		];

		$dt = [
			"pps"=> [ "m"=>true, "pf"=>15448, "s"=>"playing", "sa"=>29732 ],
			"ps"=>[ "m"=>true, "pf"=>24972, "s"=>"playing", "sa"=>29732 ],
			"si"=>"f358a8e7ea91afc",
			"so"=>"tahoe::inline",
			"vi"=> $video_id,
			"tk"=> $dt_tk,
			"ls"=>true
		];

		$dataPayload = [
			"d" => urlencode(json_encode($dt)),
			"__user" => $user_id,
			"__a" => "",
			"__dyn" => "",
			"__csr" => "",
			"__req" => "",
			"__hs" => "",
			"dpr" => "",
			"__ccg" => "EXCELLENT",
			"__rev" => "",
			"__s" => "",
			"__hsi" => "",
			"__comet_req" => "",
			"fb_dtsg" => $fb_dtsg,
			"jazoest" => "",
			"lsd" => "",
			"__spin_r" => "",
			"__spin_b" => "",
			"__spin_t" => ""
		];

		$payloadField = [];
		if (is_array($dataPayload)) {
	        foreach ($dataPayload as $name => $value) {
	            $payloadField[] = $name.'='.$value;
	        }
	    }
		$payloadField = join("&", $payloadField);

		$config = [
			"url" => "https://www.facebook.com/video/unified_cvc/",
			"header" => false,
			"cookie" => $cookieFile,
			"return_transfer" => true,
			"referer" => $liveURL,
			"httpheader" => $httpheader,
			"fields" => $payloadField,
			"fb_dtsg" => $fb_dtsg,
			"user_id" => $user_id,
			"video_id" => $video_id,
			"method" => "POST"
		];
		$getResponse = _curl($config);
		$res = json_decode( str_replace("for (;;);", "", $getResponse), true );
		$r_userID = substr($user_id, 0, strlen($user_id) - 5 ) . "xxxx";
		//return json_encode($res);

		/* renew dtsg token */
		if ( isset($res["dtsgToken"]) ) {
			$payload["fb_dtsg"] = $res["dtsgToken"];
			file_put_contents($payloadFile, json_encode($payload));
		}

		/* renew token */
		if ( isset($res["payload"]["tk"]) ) {
			$payload["token"] = $res["payload"]["tk"];
			file_put_contents($payloadFile, json_encode($payload));
		}

		/* reporter */
		if ( isset( $res["payload"]["d"]["bs"] ) && $res["payload"]["d"]["bs"] == "LIVE" ) {
			$res["status"] = true;
			$res["user_id"] = $r_userID;
			$name = explode('/', $cookieFile);
			file_put_contents( __DIR__ . "/reporter/". end($name) . ".json", json_encode($res));
			return json_encode($res);
		}
		else {
			return json_encode(["status" => false, "user_id" => $r_userID, "message" => "Error Set Viewer!", "data" => json_encode($res)]);
		}
	}
	$report = [];
	function initViewer($liveURL, $timer, $durasi, $delay, $sessCP) {
		$cookieFile = __DIR__ . "/cookies/$sessCP"."_cookie.txt";
		$payloadFile = __DIR__ . "/payloads/$sessCP"."_payload.json";

		echo setViewer($liveURL, $cookieFile, $payloadFile);
	}

	$listPayloads = glob( __DIR__ . "/payloads/*.json");
	$jumlah = (int)$jumlah;

	
	if ( $jumlah < count($listPayloads) ) {
		$sessCP = explode("/", $listPayloads[$jumlah]);
		$sessCP = explode("_", end($sessCP) );
		$sessCP = $sessCP[0];
		initViewer($liveURL, time(), (int)$durasi, (int)$delay, $sessCP);
	}

	exit;
}
