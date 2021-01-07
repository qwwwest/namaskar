

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
