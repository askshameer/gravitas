(function () {
  'use strict';

  const form = document.querySelector('[data-directory-form]');
  const cards = document.querySelector('[data-directory-cards]');
  const pagination = document.querySelector('[data-directory-pagination]');
  const loader = document.querySelector('[data-directory-loader]');
  const resultCount = document.querySelector('[data-result-count]');
  const sort = document.querySelector('[data-directory-sort]');
  const viewButtons = document.querySelectorAll('[data-view]');
  if (!form || !cards || typeof elevoraDirectory === 'undefined') return;

  let controller = null;
  let debounceTimer = null;

  function currentView() {
    return window.localStorage.getItem('elevora-directory-view') || 'grid';
  }

  function setView(view) {
    cards.classList.toggle('is-list', view === 'list');
    viewButtons.forEach(function (button) {
      button.classList.toggle('is-active', button.dataset.view === view);
    });
    window.localStorage.setItem('elevora-directory-view', view);
  }

  viewButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      setView(button.dataset.view);
    });
  });
  setView(currentView());

  function buildData(page) {
    const data = new FormData(form);
    data.append('action', 'elevora_filter_trainers');
    data.append('nonce', elevoraDirectory.nonce);
    data.set('paged', String(page || 1));
    if (sort) data.set('sort', sort.value);
    return data;
  }

  function setLoading(isLoading) {
    loader.hidden = !isLoading;
    form.setAttribute('aria-busy', String(isLoading));
  }

  function updateUrl(data, responseUrl, pushState) {
    if (!pushState) return;
    const url = new URL(responseUrl || window.location.href, window.location.origin);
    if (data.get('paged') && data.get('paged') !== '1') url.searchParams.set('paged', data.get('paged'));
    window.history.pushState({ elevoraDirectory: true }, '', url);
  }

  function fetchResults(page, pushState) {
    if (controller) controller.abort();
    controller = new AbortController();
    const data = buildData(page);
    setLoading(true);

    fetch(elevoraDirectory.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: data,
      signal: controller.signal
    })
      .then(function (response) {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
      })
      .then(function (response) {
        if (!response.success) throw new Error('Invalid response');
        cards.innerHTML = response.data.cards;
        pagination.innerHTML = response.data.pagination;
        if (resultCount) resultCount.textContent = response.data.count;
        setView(currentView());
        updateUrl(data, response.data.url, pushState);
      })
      .catch(function (error) {
        if (error.name !== 'AbortError') {
          cards.innerHTML = '<div class="empty-state"><h2>' + elevoraDirectory.error + '</h2></div>';
        }
      })
      .finally(function () {
        setLoading(false);
      });
  }

  form.addEventListener('submit', function (event) {
    event.preventDefault();
    fetchResults(1, true);
  });

  form.querySelectorAll('select').forEach(function (select) {
    select.addEventListener('change', function () { fetchResults(1, true); });
  });

  const search = form.querySelector('input[type="search"]');
  if (search) {
    search.addEventListener('input', function () {
      window.clearTimeout(debounceTimer);
      debounceTimer = window.setTimeout(function () { fetchResults(1, true); }, 450);
    });
  }

  if (sort) {
    sort.addEventListener('change', function () { fetchResults(1, true); });
  }

  pagination.addEventListener('click', function (event) {
    const link = event.target.closest('a.page-numbers');
    if (!link) return;
    event.preventDefault();
    const url = new URL(link.href, window.location.origin);
    const pathMatch = url.pathname.match(/\/page\/(\d+)/);
    const page = Number(url.searchParams.get('paged') || (pathMatch ? pathMatch[1] : 1));
    fetchResults(page, true);
    cards.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  window.addEventListener('popstate', function () {
    window.location.reload();
  });
}());

