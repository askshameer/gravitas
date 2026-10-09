(function () {
  'use strict';

  const i18n = window.elevoraTheme || {};

  document.documentElement.classList.remove('no-js');

  const storageKey = 'elevora-theme';
  const store = {
    get: function (key) { try { return window.localStorage.getItem(key); } catch (e) { return null; } },
    set: function (key, value) { try { window.localStorage.setItem(key, value); } catch (e) { /* storage unavailable */ } }
  };
  const savedTheme = store.get(storageKey);
  const preferredDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  if (savedTheme === 'dark' || (!savedTheme && preferredDark)) {
    document.documentElement.dataset.theme = 'dark';
  }

  const themeToggle = document.querySelector('[data-theme-toggle]');
  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      const isDark = document.documentElement.dataset.theme === 'dark';
      if (isDark) {
        delete document.documentElement.dataset.theme;
        store.set(storageKey, 'light');
      } else {
        document.documentElement.dataset.theme = 'dark';
        store.set(storageKey, 'dark');
      }
    });
  }

  const menuToggle = document.querySelector('[data-menu-toggle]');
  if (menuToggle) {
    menuToggle.addEventListener('click', function () {
      const open = document.body.classList.toggle('nav-open');
      menuToggle.setAttribute('aria-expanded', String(open));
    });
  }

  const searchPanel = document.querySelector('[data-search-panel]');
  const searchOpen = document.querySelector('[data-search-open]');
  const searchClosers = document.querySelectorAll('[data-search-close]');
  let searchReturnFocus = null;

  function closeSearch() {
    if (!searchPanel) return;
    searchPanel.hidden = true;
    document.body.classList.remove('search-open');
    if (searchReturnFocus) searchReturnFocus.focus();
  }

  if (searchPanel && searchOpen) {
    searchOpen.addEventListener('click', function () {
      searchReturnFocus = document.activeElement;
      searchPanel.hidden = false;
      document.body.classList.add('search-open');
      window.setTimeout(function () {
        const input = searchPanel.querySelector('input[type="search"]');
        if (input) input.focus();
      }, 50);
    });
    searchClosers.forEach(function (button) {
      button.addEventListener('click', closeSearch);
    });
  }

  // Action Modal Handling for buttons with shortcodes
  let activeModal = null;
  let modalReturnFocus = null;

  function closeModal(modal) {
    const target = modal || activeModal;
    if (!target) return;
    target.hidden = true;
    target.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    if (activeModal === target) activeModal = null;
    if (modalReturnFocus) {
      modalReturnFocus.focus();
      modalReturnFocus = null;
    }
  }

  function openModal(modalId, trigger) {
    const selector = modalId.startsWith('#') ? modalId : '#' + modalId;
    const modal = document.querySelector(selector);
    if (!modal) return;
    if (activeModal && activeModal !== modal) {
      closeModal(activeModal);
    }
    modalReturnFocus = trigger || document.activeElement;
    modal.hidden = false;
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    activeModal = modal;
    const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) focusable.focus();
  }

  document.addEventListener('click', function (event) {
    const trigger = event.target.closest('[data-modal-target]');
    if (trigger) {
      event.preventDefault();
      const modalId = trigger.getAttribute('data-modal-target');
      if (modalId) openModal(modalId, trigger);
      return;
    }
    const closer = event.target.closest('[data-modal-close]');
    if (closer) {
      event.preventDefault();
      const modal = closer.closest('.gravitas-action-modal');
      closeModal(modal);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      if (activeModal) closeModal(activeModal);
      closeSearch();
      document.body.classList.remove('nav-open');
      if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
    }
  });

  const header = document.querySelector('[data-site-header]');
  if (header) {
    const placeholder = document.createElement('div');
    placeholder.className = 'header-placeholder';
    placeholder.hidden = true;
    header.insertAdjacentElement('afterend', placeholder);
    const stickyPoint = header.offsetTop + 20;
    let ticking = false;
    function updateHeader() {
      const sticky = window.scrollY > stickyPoint;
      header.classList.toggle('is-sticky', sticky);
      placeholder.hidden = !sticky;
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(updateHeader);
        ticking = true;
      }
    }, { passive: true });
    updateHeader();
  }

  document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(function (link) {
    link.addEventListener('click', function () {
      document.body.classList.remove('nav-open');
      if (menuToggle) menuToggle.setAttribute('aria-expanded', 'false');
    });
  });

	const readingProgress = document.querySelector('[data-reading-progress] span');
	if (readingProgress) {
		let progressTicking = false;
		function updateReadingProgress() {
			const article = document.querySelector('.single-post-main article');
			if (article) {
				const start = article.offsetTop;
				const distance = Math.max(1, article.offsetHeight - window.innerHeight);
				const percent = Math.max(0, Math.min(100, ((window.scrollY - start) / distance) * 100));
				readingProgress.style.width = percent + '%';
			}
			progressTicking = false;
		}
		window.addEventListener('scroll', function () {
			if (!progressTicking) {
				window.requestAnimationFrame(updateReadingProgress);
				progressTicking = true;
			}
		}, { passive: true });
		updateReadingProgress();
	}
	document.querySelectorAll('[data-blog-results][data-pagination-mode="load_more"]').forEach(function (results) {
		const pagination = results.querySelector('.pagination');
		const grid = results.querySelector('.blog-loop');
		const status = results.querySelector('.blog-load-status');
		if (!pagination || !grid) return;
		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'button button--dark blog-load-more';
		button.textContent = i18n.loadMore || 'Load more';
		let next = pagination.querySelector('a.next');
		if (!next) return;
		pagination.hidden = true;
		pagination.after(button);
		button.addEventListener('click', async function () {
			button.disabled = true;
			if (status) status.textContent = i18n.loading || 'Loading posts…';
			try {
				const response = await fetch(next.href, { credentials: 'same-origin' });
				if (!response.ok) throw new Error('Request failed');
				const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
				const incoming = doc.querySelector('[data-blog-results]');
				const cards = incoming && incoming.querySelectorAll('.blog-loop > article');
				if (!cards || !cards.length) throw new Error('No posts returned');
				cards.forEach(function (card) { grid.appendChild(document.importNode(card, true)); });
				next = incoming.querySelector('.pagination a.next');
				const incomingNav = incoming.querySelector('.pagination');
				pagination.innerHTML = incomingNav ? incomingNav.innerHTML : '';
				if (status) status.textContent = next ? (i18n.loaded || 'More posts loaded.') : (i18n.allDone || 'All posts loaded.');
				button.hidden = !next;
			} catch (error) {
				if (status) status.textContent = i18n.failed || 'Unable to load posts. Use the page links below.';
				pagination.hidden = false;
				button.hidden = true;
			} finally { button.disabled = false; }
		});
	});

	// Soledad single post like button handler
	document.querySelectorAll('.soledad-btn-like').forEach(function (btn) {
		btn.addEventListener('click', function () {
			const countSpan = btn.querySelector('.like-number');
			let count = parseInt(countSpan ? countSpan.textContent : '0', 10) || 0;
			const isLiked = btn.classList.toggle('is-liked');
			if (isLiked) {
				count += 1;
				btn.style.background = '#b83b2a';
			} else {
				count = Math.max(0, count - 1);
				btn.style.background = '';
			}
			if (countSpan) countSpan.textContent = count;
		});
	});

	// Portfolio and Gallery Lightbox engine
	(function initLightbox() {
		let lightboxEl = document.querySelector('.gravitas-lightbox');
		if (!lightboxEl) {
			lightboxEl = document.createElement('div');
			lightboxEl.className = 'gravitas-lightbox';
			lightboxEl.setAttribute('role', 'dialog');
			lightboxEl.setAttribute('aria-modal', 'true');
			lightboxEl.setAttribute('aria-label', 'Image lightbox');
			lightboxEl.innerHTML = `
				<div class="gravitas-lightbox__backdrop"></div>
				<div class="gravitas-lightbox__container">
					<button class="gravitas-lightbox__close" type="button" aria-label="Close lightbox">&times;</button>
					<button class="gravitas-lightbox__prev" type="button" aria-label="Previous image">&#10094;</button>
					<button class="gravitas-lightbox__next" type="button" aria-label="Next image">&#10095;</button>
					<div class="gravitas-lightbox__figure">
						<div class="gravitas-lightbox__img-wrap">
							<img class="gravitas-lightbox__img" src="" alt="" />
						</div>
						<div class="gravitas-lightbox__info">
							<span class="gravitas-lightbox__title"></span>
							<span class="gravitas-lightbox__counter"></span>
						</div>
					</div>
				</div>
			`;
			document.body.appendChild(lightboxEl);
		}

		const backdrop = lightboxEl.querySelector('.gravitas-lightbox__backdrop');
		const closeBtn = lightboxEl.querySelector('.gravitas-lightbox__close');
		const prevBtn  = lightboxEl.querySelector('.gravitas-lightbox__prev');
		const nextBtn  = lightboxEl.querySelector('.gravitas-lightbox__next');
		const imgEl    = lightboxEl.querySelector('.gravitas-lightbox__img');
		const titleEl  = lightboxEl.querySelector('.gravitas-lightbox__title');
		const countEl  = lightboxEl.querySelector('.gravitas-lightbox__counter');

		let currentGallery = [];
		let currentIndex   = 0;
		let isOpen         = false;

		function updateImage() {
			if (!currentGallery.length) return;
			const item = currentGallery[currentIndex];
			imgEl.classList.remove('is-loaded');
			imgEl.src = item.src;
			imgEl.alt = item.title || '';
			titleEl.textContent = item.title || '';
			countEl.textContent = currentGallery.length > 1 ? (currentIndex + 1) + ' / ' + currentGallery.length : '';

			imgEl.onload = function () {
				imgEl.classList.add('is-loaded');
			};

			if (currentGallery.length <= 1) {
				prevBtn.style.display = 'none';
				nextBtn.style.display = 'none';
			} else {
				prevBtn.style.display = 'flex';
				nextBtn.style.display = 'flex';
			}

			// Preload adjacent images
			if (currentGallery.length > 1) {
				const nextIdx = (currentIndex + 1) % currentGallery.length;
				const prevIdx = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
				const preloadNext = new Image();
				preloadNext.src = currentGallery[nextIdx].src;
				const preloadPrev = new Image();
				preloadPrev.src = currentGallery[prevIdx].src;
			}
		}

		function openLightbox(gallery, index) {
			currentGallery = gallery;
			currentIndex   = Math.max(0, Math.min(index, gallery.length - 1));
			updateImage();
			lightboxEl.classList.add('is-active');
			document.body.classList.add('lightbox-open');
			isOpen = true;
			closeBtn.focus();
		}

		function closeLightbox() {
			if (!isOpen) return;
			lightboxEl.classList.remove('is-active');
			document.body.classList.remove('lightbox-open');
			isOpen = false;
			imgEl.src = '';
		}

		function nextImage() {
			if (!currentGallery.length) return;
			currentIndex = (currentIndex + 1) % currentGallery.length;
			updateImage();
		}

		function prevImage() {
			if (!currentGallery.length) return;
			currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
			updateImage();
		}

		closeBtn.addEventListener('click', closeLightbox);
		backdrop.addEventListener('click', closeLightbox);
		nextBtn.addEventListener('click', function (e) { e.stopPropagation(); nextImage(); });
		prevBtn.addEventListener('click', function (e) { e.stopPropagation(); prevImage(); });

		document.addEventListener('keydown', function (e) {
			if (!isOpen) return;
			if (e.key === 'Escape') closeLightbox();
			else if (e.key === 'ArrowRight') nextImage();
			else if (e.key === 'ArrowLeft') prevImage();
		});

		// Delegate click on portfolio images and triggers
		document.addEventListener('click', function (e) {
			const link = e.target.closest('.portfolio-grid a, .lightbox-trigger, [data-lightbox], .wp-block-gallery a, .gallery a');
			if (!link) return;
			const href = link.getAttribute('href');
			if (!href) return;

			const isImgUrl = href.match(/\.(jpeg|jpg|png|webp|gif|svg)(\?.*)?$/i) || link.closest('.portfolio-grid');
			if (!isImgUrl) return;

			e.preventDefault();

			const container = link.closest('.portfolio-grid, .wp-block-gallery, .gallery') || link.parentElement;
			const allLinks  = Array.from(container.querySelectorAll('a.lightbox-trigger, .portfolio-grid a, [data-lightbox], .wp-block-gallery a, .gallery a'))
				.filter(function (a) {
					const h = a.getAttribute('href') || '';
					return h.match(/\.(jpeg|jpg|png|webp|gif|svg)(\?.*)?$/i) || a.closest('.portfolio-grid');
				});

			const galleryList = (allLinks.length > 0 ? allLinks : [link]).map(function (a) {
				const thumb = a.querySelector('img');
				return {
					src: a.getAttribute('href'),
					title: a.getAttribute('data-title') || a.getAttribute('data-caption') || (thumb ? thumb.getAttribute('alt') : '') || ''
				};
			});

			const targetIdx = allLinks.indexOf(link);
			openLightbox(galleryList, targetIdx >= 0 ? targetIdx : 0);
		});
	})();

	// Trainer Carousel Controller
	(function initTrainerCarousel() {
		const carousels = document.querySelectorAll('[data-trainer-carousel]');
		if (!carousels.length) return;

		carousels.forEach(function (carousel) {
			const track = carousel.querySelector('[data-carousel-track]');
			if (!track) return;

			const section = carousel.closest('.trainers-section') || carousel.parentElement;
			const prevBtn = section ? section.querySelector('[data-carousel-prev]') : null;
			const nextBtn = section ? section.querySelector('[data-carousel-next]') : null;
			const dotsContainer = carousel.querySelector('[data-carousel-dots]');
			const slides = Array.from(track.querySelectorAll('.trainer-carousel-slide'));
			if (!slides.length) return;

			function getScrollStep() {
				const slide = slides[0];
				const rect = slide.getBoundingClientRect();
				const style = window.getComputedStyle(track);
				const gap = parseFloat(style.columnGap || style.gap) || 26;
				return rect.width + gap;
			}

			function updateNavState() {
				const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
				const currentScroll = track.scrollLeft;

				if (prevBtn) {
					const atStart = currentScroll <= 6;
					prevBtn.disabled = atStart;
					prevBtn.setAttribute('aria-disabled', String(atStart));
				}
				if (nextBtn) {
					const atEnd = currentScroll >= maxScroll - 6;
					nextBtn.disabled = atEnd;
					nextBtn.setAttribute('aria-disabled', String(atEnd));
				}

				if (dotsContainer) {
					const dots = Array.from(dotsContainer.querySelectorAll('.trainer-carousel-dot'));
					if (dots.length) {
						const trackRect = track.getBoundingClientRect();
						let activeIdx = 0;
						let minDiff = Infinity;
						slides.forEach(function (s, idx) {
							const r = s.getBoundingClientRect();
							const diff = Math.abs(r.left - trackRect.left);
							if (diff < minDiff) {
								minDiff = diff;
								activeIdx = idx;
							}
						});
						dots.forEach(function (dot, idx) {
							const isActive = idx === activeIdx;
							dot.classList.toggle('is-active', isActive);
							dot.setAttribute('aria-current', isActive ? 'true' : 'false');
						});
					}
				}
			}

			// Generate pagination dots
			if (dotsContainer) {
				dotsContainer.innerHTML = '';
				slides.forEach(function (slide, idx) {
					const dot = document.createElement('button');
					dot.type = 'button';
					dot.className = 'trainer-carousel-dot' + (idx === 0 ? ' is-active' : '');
					dot.setAttribute('aria-label', (i18n.slide || 'Go to slide ') + (idx + 1));
					dot.setAttribute('aria-current', idx === 0 ? 'true' : 'false');
					dot.addEventListener('click', function () {
						slide.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
					});
					dotsContainer.appendChild(dot);
				});
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					const step = getScrollStep();
					track.scrollBy({ left: -step, behavior: 'smooth' });
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					const step = getScrollStep();
					track.scrollBy({ left: step, behavior: 'smooth' });
				});
			}

			// Drag to scroll
			let isDown = false;
			let startX = 0;
			let scrollStartLeft = 0;

			track.addEventListener('mousedown', function (e) {
				if (e.button !== 0 || e.target.closest('a, button')) return;
				isDown = true;
				startX = e.pageX - track.offsetLeft;
				scrollStartLeft = track.scrollLeft;
				track.style.scrollBehavior = 'auto';
				track.style.cursor = 'grabbing';
			});

			window.addEventListener('mouseup', function () {
				if (!isDown) return;
				isDown = false;
				track.style.scrollBehavior = 'smooth';
				track.style.cursor = '';
			});

			window.addEventListener('mousemove', function (e) {
				if (!isDown) return;
				e.preventDefault();
				const x = e.pageX - track.offsetLeft;
				const walk = (x - startX) * 1.4;
				track.scrollLeft = scrollStartLeft - walk;
			});

			// Keyboard support
			track.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowLeft') {
					e.preventDefault();
					track.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
				} else if (e.key === 'ArrowRight') {
					e.preventDefault();
					track.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
				}
			});

			let ticking = false;
			track.addEventListener('scroll', function () {
				if (!ticking) {
					window.requestAnimationFrame(function () {
						updateNavState();
						ticking = false;
					});
					ticking = true;
				}
			}, { passive: true });

			updateNavState();
			window.addEventListener('resize', updateNavState, { passive: true });
		});
	}());
}());

