<?php  
$dest = __DIR__ . "/apps";


$test = file_get_contents(__DIR__ . "/apps/cookies/100055303567726_cookie.txt");

$re = preg_replace('!\t[0-9]+\t!', "\t1651904726\t", $test);
var_dump($re);


if ( isset($_POST["new-payload"]) ) {
	$cookie = $_POST["cookie"];
	$payload = $_POST["payload"];

	$name = json_decode($payload, true);
	$name = $name["user_id"];

	file_put_contents($dest . "/cookies/$name"."_cookie.txt", $cookie);
	file_put_contents($dest . "/payloads/$name"."_payload.json", $payload);
	echo "ok";
	exit;
}


/* save cookie to pull ======================================================= */
if ( isset($_POST["save"]) ) {
	foreach( glob($dest . "/cookies/*.txt") as $cookie ) {
		$ck = file_get_contents($cookie);
		$to = str_replace( "/cookies", "/__cookies", $cookie);
		file_put_contents($to, $ck);
		if ( file_exists($to) ) {
			unlink($cookie);
		}
	} 

	foreach( glob($dest . "/payloads/*.json") as $payload ) {
		$pl = file_get_contents($payload);
		$to = str_replace( "/payloads", "/__payloads", $payload);
		file_put_contents($to, $pl);
		if ( file_exists($to) ) {
			unlink($payload);
		}
	} 

	echo "\nmove";
	exit;
}

/* update cookie expired ======================================================= */
if ( isset($_POST["update-cookie-expired"]) ) {
	$expired = 1675625961;
	foreach( glob($dest . "/cookies/*.txt") as $cookie ) {
		$ck = file_get_contents($cookie);
		//$to = str_replace( "/cookies", "/cookies", $cookie);
		$re = preg_replace('!\t[0-9]+\t!', "\t$expired\t", $ck);
		file_put_contents($cookie, $re);
	} 

	echo "\nupdate";
	exit;
}

/* set payload to main folder payload ========================================== */
if ( isset($_POST["set-all-payload"]) ) {
	$cnc = 0;
	foreach( glob($dest . "/__cookies/*.txt") as $cookie ) {
		$ck = file_get_contents($cookie);
		$to = str_replace( "/__cookies", "/cookies", $cookie);
		file_put_contents($to, $ck);
		// if ( file_exists($to) ) {
		// 	unlink($cookie);
		// }
		$cnc += 1;
	} 

	$cnp = 0;
	foreach( glob($dest . "/__payloads/*.json") as $payload ) {
		$pl = file_get_contents($payload);
		$to = str_replace( "/__payloads", "/payloads", $payload);
		file_put_contents($to, $pl);
		// if ( file_exists($to) ) {
		// 	unlink($payload);
		// }
		$cnp += 1;
	} 

	echo "\nduplicate to ready >>> cnc: $cnc >>> cnp $cnp\n";
	exit;
}

if ( isset($_POST["remove-payload"]) ) {
	$user_id = $_POST["user_id"];
	if ( file_exists( __DIR__ . "/apps/__payloads/".$user_id."_payload.json") ) {
		unlink( __DIR__ . "/apps/__payloads/".$user_id."_payload.json");
	}
	if ( file_exists( __DIR__ . "/apps/__cookies/".$user_id."_cookie.txt") ) {
		unlink( __DIR__ . "/apps/__cookies/".$user_id."_cookie.txt");
	}
	echo "okee";
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Template</title>
	<link rel="stylesheet" type="text/css" href="./css/jcbooster.css">
	<link rel="stylesheet" type="text/css" href="./css/jcbooster.rooter.css">
	<link rel="stylesheet" type="text/css" href="./css/jcbooster.awesome.css">
	<link rel="stylesheet" type="text/css" href="./css/jcbooster.animate.css">
 	<script type="module" src="./js/jcbooster.init.js"></script>
 	<style type="text/css">
 		body {
 			background: #222;
 		}
 		.input-control {
 			color: #256DDA !important;
 		}
 		label {
 			color: #B6B6B6 !important;
 		}
 	</style>
</head>
<body>

<div class="container container-fluid">
	<div class="row">
		<div class="col-10">
			<p style="font-weight: bold;">Payload Createor For FB Live Viewer</p>			
		</div>
	</div>
</div>

<div class="clear p-10"></div>

<div class="container container-fluid">
	<div class="row">
		<div class="col-6">
			<textarea class="input-control" id="in-cookie" rows="15">_in_cookie</textarea>
			<button class="btn btn-success" id="btn-scrap">Create Cookie</button>
		</div>

		<div class="clear"></div>
		<div class="col-6">
			<textarea class="input-control" id="out-cookie" rows="10">_out_cookie</textarea>
			<textarea class="input-control" id="payload" rows="4">_payload</textarea>
			<button class="btn btn-secondary" id="btn-create">Create</button>
			<button class="btn btn-light" id="btn-save" style="float: right;">Save Move</button>
		</div>
	</div>
</div>
<div class="clear p-20"></div>
<div class="garis"></div>
<div class="clear p-20"></div>
<div class="container container-fluid">
	<div class="row">
		<div class="col-12">
			<textarea class="input-control" id="in-remove" rows="5">__id_user_payload</textarea>
			<div class="clear"></div>
			<button class="btn btn-danger" id="btn-remove-payload">Remove Payload</button>
			<div class="clear p-10"></div>
			<div class="garis"></div>
			<div class="clear p-10"></div>
			<button class="btn btn-info btn-md" id="btn-set-all-payload" style="width: 100%">Set All Payload To Ready</button>
			<div class="clear p-10"></div>
			<div class="garis"></div>
			<label>Update Cookie Expired</label>
			<div class="clear p-10"></div>
			<button class="btn btn-success btn-md" id="update-cookie-expired" style="width: 100%">Update Cookie Expired</button>
		</div>
	</div>
</div>

<div class="clear p-20"></div>

<script type="text/javascript">
var loc = window.location.href;
let tempCookie1 = `# Netscape HTTP Cookie File
# https://curl.haxx.se/docs/http-cookies.html
# This file was generated by libcurl! Edit at your own risk.

#HttpOnly_.facebook.com	TRUE	/	TRUE	1675349573	xs	{xs}
.facebook.com	TRUE	/	TRUE	1675349573	c_user	{c_user}
#HttpOnly_.facebook.com	TRUE	/	TRUE	1651589572	fr	{fr}
#HttpOnly_.facebook.com	TRUE	/	TRUE	1706784652	sb	{sb}
`;

let tempCookie2 = `# Netscape HTTP Cookie File
# https://curl.haxx.se/docs/http-cookies.html
# This file was generated by libcurl! Edit at your own risk.

#HttpOnly_.facebook.com	TRUE	/	TRUE	1675349573	xs	{xs}
.facebook.com	TRUE	/	TRUE	1675349573	c_user	{c_user}
#HttpOnly_.facebook.com	TRUE	/	TRUE	1651589572	datr	{datr}
#HttpOnly_.facebook.com	TRUE	/	TRUE	1706784652	sb	{sb}
`;
let newCookie, newPayload;
let cIn, sb, fr, xs, c_user;
document.addEventListener("readyApps", Res => {
	query('#btn-scrap').addEventListener("click", _ => {
		cIn = query('#in-cookie').value;
		sb = cIn.match(/sb=(.+?);/)[1];
		fr = cIn.match(/fr=(.+?);/)[1];
		//datr = cIn.match(/datr=(.+?);/)[1];
		xs = cIn.match(/xs=(.+?);/)[1];
		c_user = cIn.match(/c_user=(.+?);/)[1];
		newCookie = tempCookie1.replace("{sb}", sb)
				.replace("{fr}",fr)
				.replace("{c_user}",c_user)
				.replace("{xs}",xs);
		query('#out-cookie').value = newCookie;
	});

	query('#btn-create').addEventListener("click", _ => {
		newPayload = query('#payload').value;
		ajax.POST({url: loc, send:`cookie=${newCookie}&payload=${newPayload}&new-payload`}, response => {
			if ( response ) {
				console.log(response);
			}
		});
	});

	query('#btn-save').addEventListener("click", _ => {
		ajax.POST({url: loc, send:`save`}, response => {
			if ( response ) {
				console.log(response);
			}
		});
	});

	query('#btn-set-all-payload').addEventListener("click", _ => {
		ajax.POST({url: loc, send:`set-all-payload`}, response => {
			if ( response ) {
				console.log(response);
			}
		});
	});

	query('#btn-remove-payload').addEventListener("click", _ => {
		let inR = query('#in-remove').value.split('\n');
		inR = inR.filter( e => e != "" );
		inR.forEach( id => {
			ajax.POST({url: loc, send:`user_id=${id}&remove-payload`}, response => {
			if ( response ) {
					console.log(response);
				}
			});
		})
	});

	query('#update-cookie-expired').addEventListener("click", _ => {
		ajax.POST({url: loc, send:`update-cookie-expired`}, response => {
			if ( response ) {
				console.log(response);
			}
		});
	});
});

</script>

</body>
</html>