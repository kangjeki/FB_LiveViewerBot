export default function BoosterProto(Doc) {
	jcApp.version = "v.1.3";
	let regEvent = document.createEvent("Event");
	jcApp.bodyScroll = function(bool) {
		let body = document.body;
		if ( ! bool || body == "undefined") {
			body.style.height = '100vh';
			body.style.overflow = 'hidden';
		}
		else {
			body.style.height = '';
			body.style.overflow = '';
		}
	};
	let __confirmDialog = function(cf, cb) {
		let title = ( cf.title ) ? cf.title : "Message!",
			message = ( cf.message ) ? cf.message : "Confirm Your Action!",
			type = ( cf.type ) ? cf.type : "secondary",
			ficon = ( cf.ficon ) ? cf.ficon : "",
			btnMode = ( cf.btnMode ) ? cf.btnMode : 0,
			btnText = ( cf.btnText ) ? cf.btnText : ["confirm", "close"],
			width = ( cf.width ) ? cf.width : "300px";

		let fm_opt = document.querySelector("body");
		let fmDl = document.createElement('div');
			fmDl.style.cssText = `
				position: fixed; top:0; right:0; left:0; bottom:0;
				background: rgba(0,0,0,.5);
				z-index: 1140;
			`;

		let dv = document.createElement('div');
			dv.classList.add('jcApp-confirm');
			dv.classList.add('app-box');
			dv.classList.add('animated');
			dv.classList.add('zoomIn');
			dv.classList.add('faster');
			dv.style.cssText = `
				padding: 0px; background: #fff;
				box-shadow: var(--box-shadow);
				border: 0px solid;
				position: fixed;
				top: 0;
				width: ${width};
				right: 0;
				left: 0;
				overflow: hidden;
				z-index: 1150;
			`;
		let btnSet = [
				`<button class="__jcaction_setResolve btn btn-${type}" style="margin-right: 5px;">${btnText[0]}</button>
				<button class="__jcaction_setRejected btn btn-out-info">${btnText[1]}</button>`,
				`<button class="__jcaction_setResolve btn btn-${type}" style="margin-right: 5px;">${btnText[0]}</button>`,
				`<button class="__jcaction_setRejected btn btn-out-info">${btnText[1]}</button>`
			];

		let log = `
			<div class="box-content">
				<div class="box-header">
					${ficon} ${title}
				</div>
				<div class="box-body" style="text-align: center">
					${message}
				</div>
				<div class="box-footer">
					${btnSet[btnMode]}
				</div>
			</div>
		`;
		dv.innerHTML = log;
		fmDl.appendChild(dv);
		fm_opt.appendChild(fmDl);

		let mg = ( ( window.innerHeight - dv.offsetHeight) / 2 );
		dv.style.margin = mg.toString() + "px auto";

		( typeof cb == "function" ) ? cb(fmDl) : cb(false);
	};

	let closeConfirm = function(elem, eventName) {
		let anim = ( elem.querySelector('.jcApp-confirm') !== null ) ? elem.querySelector('.jcApp-confirm') : elem;
			anim.classList.remove('zoomIn');
			anim.classList.add('zoomOut');
			regEvent.content = elem;
			regEvent.initEvent(eventName, true, true);
			document.dispatchEvent(regEvent);
		setTimeout( e => {elem.remove()}, 300);	
	};

	let __promptDialog = function(cf, cb) {
		let title 	= ( cf.title != undefined ) ? cf.title : "Prompt",
			mode 	= ( cf.mode != undefined ) ? cf.mode : "text",
			type 	= ( cf.type != undefined ) ? cf.type : "secondary",
			ficon = ( cf.ficon != undefined ) ? cf.ficon : '',
			value = ( cf.value != undefined ) ? cf.value : '',
			member = ( cf.member != undefined ) ? cf.member : [],
			width = ( cf.width != undefined ) ? cf.width : '350px';

		let elemMode = {
			'text': `<input type="text" class="input-control __in_prompt" value="${value}">`,
			'number': `<input type="number" class="input-control __in_prompt" value="${value}">`,
			'long-text': `<textarea class="input-control __in_prompt" rows="5">${value}</textarea>`
		};
		if ( mode == "select" ) {
			let slcTag = `<select class="input-control __in_prompt">`;
			member.forEach( txt => {
				slcTag += `<option value="${txt}">${txt}</option>`;
			});
			slcTag += `</select>`;
			elemMode["select"] = slcTag;
		};

		let fm_opt 	= document.querySelector("body");
		let fmDl 	= document.createElement('div');
			fmDl.style.cssText = `
				position: fixed; top:0; right:0; left:0; bottom:0;
				background: rgba(0,0,0,.5);
				z-index: 1140;
			`;

		let dv 		= document.createElement('div');
			dv.classList.add('jcApp-confirm');
			dv.classList.add('app-box');
			dv.classList.add('animated');
			dv.classList.add('zoomIn');
			dv.classList.add('faster');
			dv.style.cssText = `
				padding: 0px; background: #fff;
				box-shadow: var(--box-shadow);
				border: 0px solid;
				position: fixed;
				top: 0;
				width: ${width};
				right: 0;
				left: 0;
				overflow: hidden;
				z-index: 1150;
			`;

		let log = `
			<div class="box-content">
				<div class="box-header">
					${ficon} ${title}
				</div>
				<div class="box-body">
					${elemMode[mode]}
				</div>
				<div class="box-footer">
					<button class="__jcaction_prompt_setResolve btn btn-${type}">Confirm</button>
					<button class="__jcaction_prompt_setRejected btn btn-out-info" style="margin-left:5px;">Cancle</button>	
				</div>
			</div>
		`;
		dv.innerHTML = log;
		fmDl.appendChild(dv);
		fm_opt.appendChild(fmDl);

		let mg 	= ( ( window.innerHeight - dv.offsetHeight) / 2 );
		dv.style.margin = mg.toString() + "px auto";

		( typeof cb == "function" ) ? cb(fmDl) : cb(false);
	};


	jcApp.prompt = async function(cf, cb) {
		__promptDialog(cf, elem => {
			regEvent.content = elem;
			regEvent.methodName = 'prompt';
			regEvent.initEvent("promptShow", true, true);
			document.dispatchEvent(regEvent);
			new Promise( function(resolve, rejected) {
				elem.querySelector('.__jcaction_prompt_setResolve').addEventListener('click', function() {
					resolve(elem.querySelector('.__in_prompt').value);
				});
				elem.querySelector('.__jcaction_prompt_setRejected').addEventListener('click', function() {
					rejected();
				});
			})
			.then( res => {
				cb(res);
				closeConfirm(elem, 'promptHide');
			})
			.catch( _ => {
				cb(false);
				closeConfirm(elem, 'promptHide');
			});	
		});
	};

	jcApp.confirm = function(msg, cb) {
		new Promise( function(resolve, rejected) {
			__confirmDialog(msg, elem => {
				regEvent.content = elem;
				regEvent.methodName = 'confirm';
				regEvent.initEvent("confirmShow", true, true);
				document.dispatchEvent(regEvent);
				if ( elem.querySelector('.__jcaction_setResolve') != null ) {
					elem.querySelector('.__jcaction_setResolve').addEventListener('click', function() {
						resolve(elem);
					});	
				}
				if ( elem.querySelector('.__jcaction_setRejected') != null ) {
					elem.querySelector('.__jcaction_setRejected').addEventListener('click', function() {
						rejected(elem);
					});	
				}
			});
		})
		.then( elem => {
			cb(true);
			closeConfirm(elem, 'confirmHide');
		})
		.catch( elem => {
			cb(false);
			closeConfirm(elem, 'confirmHide');
		});
	};

	jcApp.alert = function(txt, mode) {
		let wk = 1500,
			wd = ( window.innerWidth < 767.98 ) ? '65%' : "30%";
		let el = document.createElement('div');
			el.classList.add('app-box');
			el.classList.add('animated');
			el.classList.add('zoomIn');
			el.classList.add('faster');
			el.innerHTML = txt;
			el.style.cssText = `
				margin: auto; padding: 0px;
				position: fixed;
				top: 0; right: 0; left: 0; bottom: 0;
				align-items: center;
				width: ${wd};
				border: 0px #ddd solid;
				text-align: center;
				overflow: hidden;
				z-index: 1200;
				max-height: 40px;
				line-height: 40px;
				white-space: nowrap;
				text-overflow: ellipsis;
			`;
		document.body.appendChild(el);

		if (mode == undefined) {
			el.style.background = "#008000";
			el.style.color = "#fff";
			wk = 1500;
		}
		else if (mode == true) {
			el.style.background = "#008000";
			el.style.color = "#fff";
			wk = 1500;
		}
		else if (mode == false) {
			el.style.background = "#D31919";
			el.style.color = "#fff";
			wk = 2000;
		}

		setTimeout(function() {
			el.classList.remove('zoomIn');
			el.classList.add('zoomOut');
			setTimeout( e => { el.remove();}, 500);
		}, wk);	
	};

	jcApp.alertDialog = function(txt, mode) {
		let wk = 2000;
		let ie = "";
		if( mode == undefined ) { ie = "success" };
		( mode == false ? ie = "danger" : ie = "success" );
		let fD = document.createElement('div');
			fD.classList.add('box-dialog');
			fD.classList.add('app-box');
			fD.setAttribute('alert', ie);

		let iH = window.innerHeight,
			iW = window.innerWidth,
			iY = (iH - 60) / 2,
			iX = (iW - 390) / 2;

		fD.style.cssText = `
			position: fixed;
			margin: auto;
			z-index: 1200;
			top: 0; 
			right: 0;
			left: 0; 
			bottom: 0;
		`;
		let jns = ( (ie == "danger") ? 'times' : 'check' );

		let fI = document.createElement('div');
			fI.classList.add('icon-dialog');
			fI.innerHTML = `<i class="fas fa-${jns}"></i>`;

		let fT = document.createElement('div');
			fT.classList.add('text-dialog');
			fT.innerHTML = txt;

		fD.appendChild(fI);
		fD.appendChild(fT);
		document.body.appendChild(fD);

		setTimeout(function() {
			fD.remove();
		}, wk);	
	};

	jcApp.include = function(uri, cb) {
		fetch(uri).then( r => { 
			r.text()
			.then( res => {
				if (typeof cb == "function") { cb(res) }
			})
			.catch( er => { console.log(er) })
		})
		.catch(e => {console.log(e)});
	};

	jcApp.restringCode = function(tar) {
		return tar.replace(/&/g, '*a#;')
			.replace(/\?/g, '*b#;')
			.replace(/\./g, '*c#;')
			.replace(/:/g, 	'*d#;')
			.replace(/</g, 	'*e#;')
			.replace(/>/g, 	'*f#;')
			.replace(/%/g, 	'*g#;')
			.replace(/-/g, 	'*h#;')
			.replace(/_/g, 	'*i#;')
			.replace(/\+/g, '*j#;')
			.replace(/=/g, 	'*k#;')
			.replace(/\//g, '*l#;');
	};

	jcApp.hargaRp = function(harga) {
		return "Rp." + harga.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ",-";
	};

	jcApp.numberFormat = function(number) {
		return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	};

	jcApp.touchBind = function(target, callback) {
		let noTime = 0;
		jcEvent(target, 'touchstart', function() {
			this.invTouch = setInterval(function() {
				if ( noTime >= 1 ) {
					if (typeof callback == 'function') {
						callback();
					}
					noTime = 0;
					clearInterval(this.invTouch);
				}
				noTime += 1;
			}, 500);
		});
		jcEvent(target, 'touchend', function() {
			clearInterval(this.invTouch);
		});	
	};

	jcApp.setExpander = function(tg) {
		let heightNode = 0,
			node = tg.children,
			exAct = tg.classList.contains('expanded');
		if (exAct == false) {
			for (let i = 0; i < node.length; i++) {
				heightNode += node[i].offsetHeight;
			}
			tg.setAttribute('style', `height: ${heightNode.toString()}px;`);
			setTimeout( function() {
				tg.classList.add('expanded');
			}, 500);
		}
		else {
			heightNode = 0;
			tg.style.cssText = `height: ${heightNode.toString()}px;`;
			tg.classList.remove('expanded');
		}
	};

	jcApp.removeCookies = function() {
		let cookies = document.cookie.split(";");
		for (let i = 0; i < cookies.length; i++) {
			let cookie = cookies[i],
				eqPos = cookie.indexOf("="),
				name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
			document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
		}
	};
	
	let __setterFrame = function(cf, mode) {
		let frame = document.createElement('div');
			frame.classList.add('notification-' + mode);
			frame.classList.add('notification-'+ mode +'-' + cf.position);
			frame.classList.add('animated');
			frame.classList.add( ( (cf.speed !== null) ? cf.speed : "") );
			frame.classList.add(cf.animate);

		let dv = document.createElement('div');
			dv.classList.add(mode);
			dv.classList.add(mode + '-' + cf.style);

		let ctn = `<h3>${cf.data.title}</h3><span>${cf.data.body}</span>`;
		dv.innerHTML = ctn;
		frame.append(dv);
		document.body.append(frame);

		setTimeout(function() {
			frame.remove();
		}, parseInt(cf.timeout));
	};

	jcApp.notification = {
		alert : function(cf) {
			__setterFrame(cf, 'alert');
		},
		note : function(cf) {
			__setterFrame(cf, 'note');
		},
		help : `
		-optional function: "alert | note";\n 
		-object param:
			\n\t{\n
				\t\tdata: {
					title: "name",
					body: "content"
				},\n
				\t\tposition: "top-right",\n
				\t\tstyle: "primary",\n
				\t\tanimate: "fadeIn",\n
				\t\tspeed: "faster",\n
				\t\ttimeout: 2000\n
			\t}
		`
	};

	jcApp.removeElement = function(el, animate) {
		if (el != null) {
			if ( el.classList.contains('animated') ) {
				( ( animate ) ? el.classList.add(animate) : jcApp.removeElement(el, 'fadeOut') );
				setTimeout( _ => el.remove(), 2000);
			}
			else {
				el.classList.add('animated');
				el.classList.add('faster');
				jcApp.removeElement(el, animate);
			}	
		}
		else {
			throw Error("Element target not Found!");
		}
	};

	jcApp.toCamelCase = function(str) {
		return str.replace(/\b\w/g, e => e.toUpperCase());
	};

	jcApp.spa = function(cb) {
		jcApp.__spaevent = function(element, content) {
			cb(element, content);
		}
	};

	jcApp.parseReporter = function(elem, cb) {
		let dv = document.createElement('div');
			dv.innerHTML = elem;
		cb(dv.querySelector('reporter').innerHTML);
	};

	jcApp.setEvent = function(elem, event) {
		event = (event) ? event : "click";
		if ( document.createEvent ) {
			let ev = document.createEvent('MouseEvent');
				ev.initEvent(event, false, false);
			elem.dispatchEvent(ev);
		}
	};

	let __createFrameImgFullScreen = function() {
		let dvFm = document.createElement('div');
			dvFm.setAttribute('id', '__prevFm-galery__');
			dvFm.style.cssText = `
				margin: auto; padding: 0;
				position: fixed;
				top: 0; bottom: left: 0; right: 0;
				z-index: 20000;
				background: rgba( 0,0,0, .7);
				width: 100%;
				height: -webkit-fill-available;
			`;

		let dvBl = document.createElement('div');
			dvBl.setAttribute('id', '__prevBl-galery__');
			dvBl.style.cssText = `
				margin: auto; padding: 0;
				position: relative;
				width: 100%;
				z-index: 20001;
			`;
		let btnCls = document.createElement('button');
			btnCls.innerHTML = '<i class="fas fa-times" color="white" style="font-size: 25px"></i>';
			btnCls.classList.add('__btn-clsFm');
			btnCls.classList.add('btn');
			btnCls.style.cssText = `
				position: absolute;
				top: 10px;
				right: 10px;
				z-index: 20005;
				background: transparent;
			`;
		dvFm.append(btnCls);
		dvFm.append(dvBl);

		btnCls.addEventListener('click', _ => {
			dvFm.remove();
		});

		return [dvFm, dvBl];
	};

	jcApp.setImageFullScreen = function(url, cb) {
		let targetPrev = document.body;
		targetPrev.classList.add('preload');

		ajax.ONLOAD({url: url, send: "", type: "blob"}, res => {
			if (res) {
				let img = new Image();
				
				let dvFm = __createFrameImgFullScreen();
				img.src = window.URL.createObjectURL(res);
				img.style.cssText = `
					width: 100%;
					margin: auto;
					max-width: 700px;
					position: fixed;
					top: 0; left: 0; right: 0; bottom: 0;
					z-index: 20003;
				`;
				dvFm[1].append(img);
				targetPrev.append(dvFm[0]);
				targetPrev.classList.remove('preload');
				cb(img, dvFm);
			}
		});
	};

	jcApp.setPanelOnloader = function(msg, mode) {
		let fm = document.createElement('div');
		let bg = document.createElement('div');
		let dv = document.createElement('div');
		fm.style.cssText = `margin:auto;padding:0;`;
		bg.style.cssText = `margin: 0; padding: 0; position: fixed; left:0; right:0; bottom:0; top:0; background:rgba(0,0,0,.5);z-index:20008`;
		if ( mode ) {
			fm.append(bg);
		}
		dv.style.cssText = `margin:auto; padding: 20px; position: fixed; left:0; right:0; bottom:0; top:0; z-index:20009;
		width: 450px; height: 130px; text-align:center; background: #fff; box-shadow:0 0 5px 3px rgba(0,0,0,.2)`;
		dv.innerHTML = `<i class="fas fa-radiation fa-spin" style="font-size: 45px; color: #191848;"></i><br><br>
		<span>${msg}</span>`;
		fm.append(dv);
		document.body.append(fm);

		fm.setFinish = function(msg) {
			let i = fm.querySelector('i');
			i.classList.remove("fas");
			i.classList.remove("fa-radiation");
			i.classList.remove("fa-spin");
			i.classList.add("far");
			i.classList.add("fa-check-circle");
			i.classList.add("zoomIn");
			i.classList.add("faster");
			i.classList.add("animated");
			i.style.color = "green";
			if ( msg ) {
				fm.querySelector('span').innerHTML = msg;
			}
		};
		fm.setMessage = function(msg) {
			fm.querySelector('span').innerHTML = msg;
		};
		return fm;
	};

	let __mnt = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
	jcApp.dateEncode = function(tgl) {
		if ( tgl.search("-") ) {
			let nt = tgl.split('-').reverse();
			return nt[0] + "-" + __mnt[parseInt(nt[1])-1] + "-" + nt[2];	
		}
		else {
			throw Error("Tidak terdapat symbol pemisah -");
		}
		
	};

	jcApp.dateDecode = function(tgl) {
		if ( tgl.search("-") ) {
			let nt = tgl.split('-').reverse();
			let mn = __mnt.indexOf(nt[1])+1;
				mn = ((mn<=9)?"0"+mn:mn);
			return nt[0] + "-" + mn + "-" + nt[2];
		}
		else {
			throw Error("Tidak terdapat symbol pemisah -");
		}
	};

	jcApp.urlToObject = function(url) {
		let arr = url.split(/\?|\&|\=/).slice(1);
		let obj = {};
		for (let i = 0; i < arr.length/2; i++) obj[arr[i*2]] = arr[i*2+1];
		return obj;
	};

	let parseFragment = function() {

	};

	jcApp.setFragmentURL = function(fragment, cb) {
		let loc = window.location.href;
		fragment = (fragment.match(/#/)) ? fragment : `#${fragment}`;
		let newURL = (loc.match(/#/)) ? loc.replace(/#(.+)/, fragment): loc + fragment;
		window.history.pushState({
			html: document.querySelector('body').innerHTML,
			pageTitle: document.querySelector('title').innerHTML
		}, "", newURL );
		if ( typeof cb == 'function' ) {
			if ( window.location.href == newURL ) {
				cb(newURL, fragment);
			}
			else {
				throw Error("Set Fragment URL ERROR!");
			}
		}
	};
	
	jcApp.jsonParse = function(json, cb) {
		try { JSON.parse(json) }  
		catch (e) {
			cb({status: false, message: e + "<br><br>" + json});
		}
		finally {
			cb({status: true, message: "ok", data: JSON.parse(json)});
		}
	};

	jcApp.event('modalShow', ev => {
		jcApp.bodyScroll(false);
	});
	jcApp.event('modalHide', ev => {
		jcApp.bodyScroll(true);
	});
	jcApp.event('promptShow', ev => {
		jcApp.bodyScroll(false);
	});
	jcApp.event('promptHide', ev => {
		jcApp.bodyScroll(true);
	});
	jcApp.event('confirmShow', ev => {
		jcApp.bodyScroll(false);
	});
	jcApp.event('confirmHide', ev => {
		jcApp.bodyScroll(true);
	});


};
