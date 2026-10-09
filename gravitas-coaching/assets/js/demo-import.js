(function () {
	'use strict';

	var wrap = document.querySelector('.elevora-import-wrap');
	var progress = document.querySelector('[data-demo-progress]');
	if (!wrap || !progress) {
		return;
	}

	document.querySelectorAll('[data-demo-form]').forEach(function (form) {
		form.addEventListener('submit', function (event) {
			var confirmation = form.getAttribute('data-confirm-message');
			if (confirmation && !window.confirm(confirmation)) {
				event.preventDefault();
				return;
			}

			var action = form.getAttribute('data-demo-form');
			var removing = action === 'remove';
			var stages = removing ? elevoraDemoImport.removeStages : elevoraDemoImport.importStages;
			var title = removing ? elevoraDemoImport.removeTitle : elevoraDemoImport.importTitle;
			var fill = progress.querySelector('[data-demo-progress-fill]');
			var bar = progress.querySelector('[data-demo-progress-bar]');
			var value = progress.querySelector('[data-demo-progress-value]');
			var stage = progress.querySelector('[data-demo-progress-stage]');
			var percent = 4;
			var submitButton = form.querySelector('[type="submit"]');

			// A normal form navigation can begin before the browser paints the
			// progress panel. Pause the navigation for two frames so the user gets
			// immediate, visible feedback even on a fast local WordPress install.
			event.preventDefault();

			wrap.classList.add('is-working');
			progress.hidden = false;
			progress.removeAttribute('hidden');
			progress.querySelector('[data-demo-progress-title]').textContent = title;
			stage.textContent = stages[0];
			fill.style.width = percent + '%';
			value.textContent = percent + '%';
			bar.setAttribute('aria-valuenow', percent);
			if (submitButton) {
				submitButton.disabled = true;
				submitButton.setAttribute('aria-busy', 'true');
			}
			progress.scrollIntoView({ behavior: 'smooth', block: 'center' });

			window.setInterval(function () {
				if (percent >= 94) {
					return;
				}
				percent = Math.min(94, percent + Math.max(1, Math.round((94 - percent) / 8)));
				var stageIndex = Math.min(stages.length - 1, Math.floor((percent / 95) * stages.length));
				fill.style.width = percent + '%';
				value.textContent = percent + '%';
				stage.textContent = stages[stageIndex];
				bar.setAttribute('aria-valuenow', percent);
			}, 700);

			window.requestAnimationFrame(function () {
				window.requestAnimationFrame(function () {
					window.setTimeout(function () {
						form.submit();
					}, 120);
				});
			});
		});
	});
}());
