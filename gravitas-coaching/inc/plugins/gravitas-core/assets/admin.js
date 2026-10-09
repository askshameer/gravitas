(function ($) {
	'use strict';

	$(function () {
		$('.gravitas-color').wpColorPicker();


		var form = $('.gravitas-options-form');
		var dirty = false;
		var search = $('[data-options-search]');
		var icons = {brand:'art',typography:'editor-textcolor',header:'menu',hero:'cover-image',page_titles:'editor-textcolor',single_post:'admin-post',post_style:'admin-appearance',related:'screenoptions',comments:'admin-comments',pages:'admin-page',layout:'layout',navigation:'arrow-down-alt',footer:'editor-insertmore',advanced:'admin-tools'};
		$('.gravitas-tab').each(function () {
			$(this).prepend($('<span>', {class:'dashicons dashicons-' + (icons[$(this).data('tab')] || 'admin-generic'), 'aria-hidden':'true'}));
		});
		function activate(tab) {
			search.val('').trigger('change');
			$('.gravitas-field,.gravitas-option-section h3').show();
			$('.gravitas-search-empty').prop('hidden', true);
			$('.gravitas-tab').removeClass('is-active').attr('aria-selected','false');
			$('.gravitas-tab[data-tab="' + tab + '"]').addClass('is-active').attr('aria-selected','true');
			$('.gravitas-option-section').removeClass('is-active');
			$('.gravitas-option-section[data-panel="' + tab + '"]').addClass('is-active');
			try { sessionStorage.setItem('gravitas-options-tab', tab); } catch (e) {}
		}
		$('.gravitas-tab').on('click', function () { activate($(this).data('tab')); });
		try {
			var lastTab = sessionStorage.getItem('gravitas-options-tab');
			if (lastTab && $('.gravitas-tab[data-tab="' + lastTab + '"]').length) activate(lastTab);
		} catch (e) {}
		search.on('input', function () {
			var term = this.value.toLowerCase().trim();
			if (!term) { activate($('.gravitas-tab.is-active').data('tab') || 'brand'); return; }
			var matches = 0;
			$('.gravitas-option-section').each(function () {
				var section = $(this);
				var count = 0;
				section.find('.gravitas-field').each(function () {
					var match = $(this).text().toLowerCase().indexOf(term) !== -1;
					$(this).toggle(match);
					if (match) count++;
				});
				section.find('h3').hide();
				section.toggleClass('is-active', count > 0);
				matches += count;
			});
			$('.gravitas-search-empty').prop('hidden', matches > 0);
		});
		form.on('change input', 'input:not([data-options-search]),select,textarea', function () {
			dirty = true;
			$('[data-save-state]').text('Unsaved changes').addClass('is-dirty');
		});
		form.on('submit', function () { dirty = false; $('[data-save-state]').text('Saving…'); });
		window.addEventListener('beforeunload', function (event) {
			if (dirty) { event.preventDefault(); event.returnValue = ''; }
		});

		$('[data-media-select]').on('click', function (event) {
			event.preventDefault();
			var wrap = $(this).closest('.gravitas-media');
			var frame = wp.media({ title: 'Choose a brand image', button: { text: 'Use this image' }, multiple: false });
			frame.on('select', function () {
				var image = frame.state().get('selection').first().toJSON();
				wrap.find('[data-media-id]').val(image.id).trigger('change');
				wrap.find('[data-media-preview]').html('<img src="' + (image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url) + '" alt="">');
			});
			frame.open();
		});

		$('[data-media-remove]').on('click', function (event) {
			event.preventDefault();
			var wrap = $(this).closest('.gravitas-media');
			wrap.find('[data-media-id]').val('').trigger('change');
			wrap.find('[data-media-preview]').empty();
		});
	});
})(jQuery);
