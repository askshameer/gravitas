(function () {
	'use strict';

	function request(action, data) {
		var body = new URLSearchParams(Object.assign({ action: 'gravitas_video_' + action, nonce: gravitasVideo.nonce }, data || {}));
		return fetch(gravitasVideo.ajaxUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body.toString() }).then(function (response) { return response.json(); });
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-video-player]').forEach(function (player) {
			var postId = player.dataset.videoId;
			var seenKey = 'gravitas-view-' + postId;
			if (!sessionStorage.getItem(seenKey)) {
				setTimeout(function () { request('view', { post_id: postId }); sessionStorage.setItem(seenKey, '1'); }, 3500);
			}
			var history = JSON.parse(localStorage.getItem('gravitasVideoHistory') || '[]').filter(function (id) { return String(id) !== String(postId); });
			history.unshift(postId);
			localStorage.setItem('gravitasVideoHistory', JSON.stringify(history.slice(0, 50)));

			var preroll = player.dataset.preroll;
			var stage = player.querySelector('.gravitas-player__stage');
			if (preroll && stage && !sessionStorage.getItem('gravitas-ad-' + postId)) {
				var current = stage.innerHTML;
				var skipAfter = parseInt(player.dataset.prerollSkip || '5', 10);
				stage.innerHTML = '<div class="gravitas-ad"><video autoplay playsinline controls src="' + encodeURI(preroll) + '"></video><a class="gravitas-ad__link" target="_blank" rel="sponsored noopener" href="' + encodeURI(player.dataset.prerollClick || '#') + '">Sponsored</a><button type="button" class="gravitas-ad__skip" disabled>Skip in ' + skipAfter + '</button></div>';
				var ad = stage.querySelector('video');
				var skip = stage.querySelector('.gravitas-ad__skip');
				var remaining = skipAfter;
				var interval = setInterval(function () { remaining -= 1; skip.textContent = remaining > 0 ? 'Skip in ' + remaining : 'Skip ad'; if (remaining <= 0) { skip.disabled = false; clearInterval(interval); } }, 1000);
				function finishAd() { sessionStorage.setItem('gravitas-ad-' + postId, '1'); stage.innerHTML = current; }
				skip.addEventListener('click', finishAd);
				ad.addEventListener('ended', finishAd);
			}
		});

		document.addEventListener('click', function (event) {
			var chapter = event.target.closest('[data-chapter]');
			if (chapter) {
				event.preventDefault();
				var parts = chapter.dataset.chapter.split(':').map(Number);
				var seconds = parts.length === 3 ? parts[0] * 3600 + parts[1] * 60 + parts[2] : parts[0] * 60 + parts[1];
				var video = document.querySelector('[data-video-player] video');
				if (video && !Number.isNaN(seconds)) { video.currentTime = seconds; video.play(); }
				return;
			}
			var button = event.target.closest('[data-video-action]');
			if (!button) return;
			event.preventDefault();
			if (!gravitasVideo.userId && ['react', 'watch_later', 'subscribe'].indexOf(button.dataset.videoAction) !== -1) {
				window.location.href = button.dataset.loginUrl || '/wp-login.php';
				return;
			}
			var data = { post_id: button.dataset.postId || 0, reaction: button.dataset.reaction || '', channel_id: button.dataset.channelId || 0, reason: button.dataset.reason || '' };
			request(button.dataset.videoAction, data).then(function (response) {
				if (!response.success) throw new Error();
				button.classList.toggle('is-active', response.data.added || response.data.subscribed || response.data.active === button.dataset.reaction);
				if (response.data.likes !== undefined) document.querySelectorAll('[data-like-count]').forEach(function (el) { el.textContent = response.data.likes; });
				if (response.data.dislikes !== undefined) document.querySelectorAll('[data-dislike-count]').forEach(function (el) { el.textContent = response.data.dislikes; });
				if (button.dataset.videoAction === 'report') button.textContent = gravitasVideo.labels.reported;
			}).catch(function () { window.alert(gravitasVideo.labels.error); });
		});
	});
})();
