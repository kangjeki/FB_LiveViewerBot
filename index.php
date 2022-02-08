<?php  
require __DIR__ . '/apps/fbLiveStream.php';


// echo date("d-M-Y h:i:sa", 1675621641 + 60*24*3);

// echo 1675621641 + 60*24*3;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>.:Viewer FB Live Stream:.</title>
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

<?php  
	$readyAccount = glob( __DIR__ . "/apps/payloads/*.json");
?>

<div class="clear p-20"></div>

<div class="container" style="display: inline-block; text-align: center;">
	<h3>Viewer Facebook Live Stream</h3>
</div>
<div class="clear p-20"></div>
<div class="container container-fluid">
	<div class="row">
		<div class="col-6">
			<div class="box-content p-10">
				<div class="box-body">
					<label>Jumlah <i>(viewer)</i></label>
					<input type="number" class="input-control" id="jumlah" value="10">
					<div class="clear"></div>
					<div class="box-card" grid="2">
						<div class="card p-0">
							<label>Durasi <i>(menit)</i></label>
							<input type="number" class="input-control" id="durasi" value="3">		
						</div>
						<div class="card p-0">
							<label>Delay <i>(detik)</i></label>
							<input type="number" class="input-control" id="delay" value="10">
						</div>
					</div>
					<div class="clear"></div>
					<label>Link Live</label>
					<input type="text" class="input-control" id="live-url">
					<div class="clear p-10"></div>
					<button class="btn btn-secondary" id="set-viewer">set viewer</button>		
					<button class="btn btn-danger" id="stop">stop</button>	

					<!-- <button class="btn btn-danger" id="reset-cp">Reset CP</button>	 -->	
				</div>
			</div>
		</div>
		<style type="text/css">
			#notif-head, #notif-head i, #notif-head span {
				color: #12B916 !important;
			}
			#notif-body, #notif-body i, #notif-body span  {
				color: #2383EF !important;
			}
		</style>
		<div class="clear p-10"></div>
		<div class="col-6">
			<div class="alert alert-dark" style="height: 115px;">
				<div id="notif-head">
					<span id="txt1">>__ready <?= count($readyAccount) ?> viewer >>></span> 
					<span id="txt2"></span>
					<span id="txt3"></span>
				</div>
				<div class="clear"></div>
				<div id="notif-body"></div>
			</div>
			<div class="clear"></div>
			<div class="alert alert-dark" style="height: 140px; overflow-y: auto; font-size: 13px;">
				<div id="notif-ex"></div>
			</div>
		</div>
	</div>
</div>

<div class="clear p-50"></div>

<script type="text/javascript">
var loc = window.location.href;

document.addEventListener("readyApps", Res => {
	let liveURL;
	let durasiLive = 0;
	let jumlah = 0;
	let listAccount = [];

	let payload;
	let notifEx = query('#notif-ex');
	let ctrHit = 1;
	let timeOpen = 0;
	let invTimer;
	let timerLeft = 0;
	let delay = 10000;
	let session = false;
	let hostBot = "https://sosmed.artaniaga.com/fblivestream/bot.php";

	function setViewer(liveURL, index, durasiLive) {
		ajax.POST({url: loc, send:`jumlah=${index}&live-url=${liveURL}&durasi=${durasiLive}&delay=${delay}&set-viewer`}, response => {
			if ( response ) {
				query('#txt3').innerHTML = " => time: " + new Date().getHours() + ":" + new Date().getMinutes() + ":" + new Date().getSeconds();
				jcApp.jsonParse(response, res => {
					
					if ( res.data.status ) {
						payload = res.data.payload;
						query('#notif-body').innerHTML = `
						status view: ${res.data.status}<br>
						video_id: ${payload.vi}<br>
						vewer active: ${payload.d.c}
						`;
						if ( ctrHit > 1 ) {
							//notifEx.querySelector('span').remove();
						}
						notifEx.innerHTML += `<span style="color:#2383EF">>_uid >>> ${res.data.user_id} => success</span><br>`;
						//console.log(res.data);
						notifEx.parentElement.scrollTo(0,notifEx.parentElement.scrollHeight);
					}
					else {
						console.log(JSON.parse(res.data.data));
						if ( ctrHit > 1 ) {
							notifEx.querySelector('span').remove();
						}
						notifEx.innerHTML += `<span style="color:red">>_uid >>> ${res.data.user_id} => failed!</span><br>`;
						notifEx.parentElement.scrollTo(0,notifEx.parentElement.scrollHeight);
					}
				});
			}
		});
	};

	function setTimeViewer(timerOpen) {
		for (let i = 0; i < jumlah; i++ ) {
			query('#txt1').innerHTML = `<i class="fas fa-radiation fa-spin"></i>`;
			let startTime = new Date(timeOpen).getHours() + ":" + new Date(timeOpen).getMinutes() + ":" + new Date(timeOpen).getSeconds();

			let timeEnd = timeOpen + (parseInt(durasiLive)*1000*60);
			let endTime = new Date(timeEnd).getHours() + ":" + new Date(timeEnd).getMinutes() + ":" + new Date(timeEnd).getSeconds();	
			query('#txt2').innerHTML = ` progress hit: ` + ctrHit + " | start: " + startTime + " => end: " + endTime;
			setViewer(liveURL, i, durasiLive);
		}
		
		/* mode client side ====================================================================== */
		setTimeout(_ => {
			//console.log( timeOpen + parseInt(durasiLive)*1000*60 + " | "+ new Date().getTime() );
			if ( timeOpen + parseInt(durasiLive)*1000*60 > new Date().getTime() && session ) {
				setTimeViewer(false);
				ctrHit += 1;
			}
			else {
				// clearInterval(invTimer);
				session = false;
				localStorage.removeItem("progress_hit");
				query('#txt1').innerHTML = ">_ finish";
				query('#txt2').innerHTML = "";
				query('#txt3').innerHTML = "";
			}	
		}, delay);
	};

	function autoRload() {
		if ( timeOpen + parseInt(durasiLive)*1000*60 > new Date().getTime() && session) {
			setTimeout( _ => { 
				localStorage.setItem("progress_hit", JSON.stringify({url: liveURL, durasi: durasiLive, jumlah: jumlah, delay: delay/1000, timeOpen: timeOpen, ctrHit: ctrHit, session: session}));
				location.reload();
			}, 1000*60*1);
		}
	}

	query('#set-viewer').addEventListener("click", _ => {
		ctrHit = 1;
		session = true;
		timeOpen = new Date().getTime();
		delay = parseInt(query('#delay').value) * 1000;

		liveURL = query('#live-url').value;
		durasiLive = query('#durasi').value;
		jumlah = query('#jumlah').value;
		timerLeft = parseInt(durasiLive)*60;
		
		if ( liveURL.split("videos").length <= 1 ) {
			jcApp.alertDialog("Link / URL tidak benar", false);
			return;
		}
		else {
			setTimeViewer(true);
			autoRload();
		}
	});

	if ( localStorage.getItem("progress_hit") ) {
		let sess = JSON.parse( localStorage.getItem("progress_hit") );
		session = sess.session;
		ctrHit = sess.ctrHit;
		timeOpen = sess.timeOpen;
		delay = sess.delay*1000;
		liveURL = sess.url;
		durasiLive = sess.durasi;
		jumlah = sess.jumlah;

		query('#delay').value = sess.delay;
		query('#jumlah').value = jumlah;
		query('#durasi').value = durasiLive;
		query('#live-url').value = liveURL;
		if ( session ) {
			setTimeViewer(true);
			autoRload();	
		}
	};
	query('#stop').addEventListener("click", _ => {
		session = false;
		localStorage.removeItem("progress_hit");
		query('#txt1').innerHTML = ">_ stop!";
		refreshPage();
	});

});

</script>

</body>
</html>