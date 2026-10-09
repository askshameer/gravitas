(function () {
	'use strict';

	var form = document.querySelector('.elevora-setup__form');
	if (!form) {
		return;
	}

	var config = window.elevoraSetupWizard || {};
	var stages = config.stages && config.stages.length ? config.stages : ['Preparing your site…'];
	var button = form.querySelector('.elevora-setup__submit');
	var buttonLabel = form.querySelector('.elevora-setup__button-label');
	var progress = form.querySelector('.elevora-setup__progress');
	var track = form.querySelector('.elevora-setup__track');
	var bar = track ? track.querySelector('span') : null;
	var status = form.querySelector('.elevora-setup__status');
	var percentage = form.querySelector('.elevora-setup__percentage');
	var error = form.querySelector('.elevora-setup__error');
	var running = false;

	function setProgress(value, label) {
		var bounded = Math.max(0, Math.min(100, value));
		if (bar) {
			bar.style.width = bounded + '%';
		}
		if (track) {
			track.setAttribute('aria-valuenow', String(bounded));
		}
		if (percentage) {
			percentage.textContent = bounded + '%';
		}
		if (status && label) {
			status.textContent = label;
		}
	}

	form.addEventListener('submit', function (event) {
		if (running) {
			event.preventDefault();
			return;
		}

		running = true;
		event.preventDefault();

		form.classList.add('is-running');
		form.setAttribute('aria-busy', 'true');
		if (button) {
			button.disabled = true;
		}
		if (buttonLabel) {
			buttonLabel.textContent = config.working || 'Setup in progress…';
		}
		if (error) {
			error.hidden = true;
		}

		if (progress) {
			progress.hidden = false;
			progress.removeAttribute('hidden');
		}

		var stops = [8, 27, 48, 70, 88];
		var stageIndex = 0;
		setProgress(stops[0], stages[0]);

		window.setInterval(function () {
			if (stageIndex >= stops.length - 1) {
				return;
			}
			stageIndex += 1;
			setProgress(stops[stageIndex], stages[stageIndex] || stages[stages.length - 1]);
		}, 4500);

		/*
		 * Submit as a normal form navigation. A plain navigation survives long
		 * installs/imports and keeps the progress panel visible, whereas a fetch
		 * request can fail or time out before the browser shows any feedback.
		 * Pause for two animation frames so the progress UI paints first.
		 */
		window.requestAnimationFrame(function () {
			window.requestAnimationFrame(function () {
				window.setTimeout(function () {
					form.submit();
				}, 120);
			});
		});
	});
}());
