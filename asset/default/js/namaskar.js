

function qwwwestLogo(id, color = "#ffffdd", bg = "#333355") {

	const canvas = document.getElementById(id);
	const ctx = canvas.getContext('2d');
	const cx = canvas.width / 2;
	const cy = cx;


	const R = cx / 2;
	const XR = R / 5;
	const D = 360 / 42;
	const DR = 2 * Math.PI / 42;


	const q = [4, 2, 1, 1, - 1, 2, -4, -5];
	const q2 = [2, 1, -1, 1, 1, 1, -2, -3];
	const w = [3, 1, -2, 1, 2, 1, -2, 1, 2, 1, -3, -5];
	const s = [3, 1, -2, 1, 2, 3, -3, -1, 2, -1, -2, -3];
	const t = [1, 2, 2, 1, -2, 1, 1, 1, -2, -5];

	ctx.fillStyle = bg;
	ctx.arc(cx, cy, cx, 0, 42 * DR);
	ctx.fill();

	let step = 8;

	draw(q, color, 0, -1);
	draw(q2, bg, 1, 0);
	step += 6;

	while (step < 33) {
		draw(w, color);
		step += 6;
	}

	draw(s, color);
	step += 6;
	draw(t, color);

	function draw(a, color, xx = 0, yy = 0) {
		let x, y;
		ctx.fillStyle = color;
		ctx.strokeStyle = color;
		ctx.lineWidth = 2;
		ctx.lineCap = "round";
		ctx.beginPath();
		x = polarX((step + xx) * D, R + yy * XR) + cx;
		y = polarY((step + xx) * D, R + yy * XR) + cy;
		ctx.moveTo(x, y);
		for (let i = 0; i < a.length; i++) {
			if (i % 2) {
				ctx.arc(cx, cy, R + yy * XR, (step + xx) * DR, (step + xx + a[i]) * DR, a[i] < 0);
				xx += a[i];

			}
			else {
				yy += a[i];
				x = polarX((step + xx) * D, R + yy * XR) + cx;
				y = polarY((step + xx) * D, R + yy * XR) + cy;
				ctx.lineTo(x, y);
			}



		}

		ctx.fill();

	}
	function polarX(angle, d) { return Math.cos(0.0174532925 * (angle)) * d; }
	function polarY(angle, d) { return Math.sin(0.0174532925 * (angle)) * d; }
}

function toggleSideMenu() {
	// document.querySelector(" .hamburger").classList.toggle('active');
	document.body.classList.toggle("sideMenuOpen"); // //
	//    document.getElementById('aside').style.width = '250px'; // document.getElementById('main').style.marginLeft='250px' ;
}
window.onresize = () => {
	if (document.body.classList.contains("sideMenuOpen")) toggleSideMenu();
};

var script = document.currentScript;
var fullUrl = script.src;

window.onload = () => {

	animeyes("logo");


	let res = loadFile(fullUrl + ".bg.txt", then);

	function then(data) {


		data = data.replace(/(\r\n|\n|\r)/gm, " ");
		document.getElementById('bg').innerText = data;
		microlight.reset();
	}



}

function animeyes(id) {
	let a = document.getElementById(id);
	console.log("animeyes", id, a);
	if (a === undefined) return;
	let svgDoc = a.contentDocument;

	const eyeBall = Array.from(svgDoc.querySelectorAll(".eyeball")),
		pupil = Array.from(svgDoc.querySelectorAll(".pupil"));

	const eyeArea0 = eyeBall[0].getBoundingClientRect();
	const eyeArea1 = eyeBall[1].getBoundingClientRect();
	const pupilArea0 = pupil[0].getBoundingClientRect();
	const pupilArea1 = pupil[0].getBoundingClientRect();

	const R = eyeArea0.width / 2,
		r = pupilArea0.width / 2;

	document.addEventListener("mousemove", (e) => {
		let x = e.clientX - eyeArea0.left + R,
			y = e.clientY - eyeArea0.top + R,
			angle = (Math.atan2(y, x) * 180) / Math.PI + 720;

		pupil[0].style.transform = `translateX(${R - r + "px"}) rotate(${angle + "deg"
			})`;
		pupil[0].style.transformOrigin = `${85 - R + r + "px"} ${113 + "px"}`;

		(x = e.clientX - eyeArea1.left + R),
			(y = e.clientY - eyeArea1.top + R),
			(angle = (Math.atan2(y, x) * 180) / Math.PI + 720);

		pupil[1].style.transform = `translateX(${R - r + "px"}) rotate(${angle + "deg"
			})`;
		pupil[1].style.transformOrigin = `${120 - R + r + "px"} ${113 + "px"
			}`;
	});
};

async function loadFile(url, then) {
	try {
		const response = await fetch(url);
		const data = await response.text();
		then(data);

	} catch (err) {
		console.error(err);
	}
}

