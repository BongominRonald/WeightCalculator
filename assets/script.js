/* Consolidated UI script
   - single DOMContentLoaded initializer
   - handles landing tabs, sidebar toggles, topbar actions, theme toggle, logout modal,
     select focus locking, and accessible keyboard handlers.
*/

// Landing tab switcher
function switchTab(tab) {
  const loginTab = document.getElementById('tab-login');
  const signupTab = document.getElementById('tab-signup');
  const loginPanel = document.getElementById('panel-login');
  const signupPanel = document.getElementById('panel-signup');
  if (!loginTab || !signupTab || !loginPanel || !signupPanel) return;
  if (tab === 'login') {
    loginTab.classList.add('active'); signupTab.classList.remove('active');
    loginPanel.classList.remove('hidden'); signupPanel.classList.add('hidden');
  } else {
    signupTab.classList.add('active'); loginTab.classList.remove('active');
    signupPanel.classList.remove('hidden'); loginPanel.classList.add('hidden');
  }
}

function initLandingTabs() {
  if (window.location.hash === '#signup') switchTab('signup'); else switchTab('login');
}

function showLandingMessages() {
  const errorBox = document.getElementById('msg-error');
  const successBox = document.getElementById('msg-success');
  const params = new URLSearchParams(window.location.search);
  const error = params.get('error'); const success = params.get('success');
  if (error && errorBox) { errorBox.textContent = error; errorBox.classList.remove('hidden'); }
  if (success && successBox) { successBox.textContent = success; successBox.classList.remove('hidden'); }
}

// Theme toggle
function toggleTheme() {
  const isLight = document.body.classList.toggle('light');
  const toggle = document.querySelector('.theme-toggle');
  if (toggle) {
    toggle.classList.toggle('on', isLight);
    toggle.classList.toggle('off', !isLight);
    toggle.setAttribute('aria-checked', isLight ? 'true' : 'false');
  }
  // Persist theme choice
  localStorage.setItem('theme', isLight ? 'light' : 'dark');
}

// Sidebar toggle
function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const backdrop = document.getElementById('sidebar-backdrop');
  if (!sidebar) return;
  const open = !sidebar.classList.contains('open');
  sidebar.classList.toggle('open', open);
  document.body.classList.toggle('sidebar-open', open);
  // update hamburger aria-expanded
  document.querySelectorAll('.hamburger').forEach(h => h.setAttribute('aria-expanded', open ? 'true' : 'false'));
  if (backdrop) { backdrop.setAttribute('aria-hidden', open ? 'false' : 'true'); }
  if (open) {
    // focus first nav link for accessibility
    const first = sidebar.querySelector('.nav-link'); if (first) first.focus();
  }
}

// Helpers
function closeSidebar() {
  const sidebar = document.querySelector('.sidebar'); if (sidebar) sidebar.classList.remove('open');
}

function openModal(modal) { if (!modal) return; modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); }
function closeModal(modal) { if (!modal) return; modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); }

document.addEventListener('DOMContentLoaded', () => {
  // Landing init
  if (document.getElementById('panel-login')) { initLandingTabs(); showLandingMessages(); }

  // Sidebar close on nav click
  const sidebar = document.querySelector('.sidebar');
  if (sidebar) {
    sidebar.querySelectorAll('a.nav-link').forEach(link => link.addEventListener('click', () => { if (sidebar.classList.contains('open')) closeSidebar(); }));
  }

  // Bind hamburger (sidebar)
  document.querySelectorAll('.hamburger').forEach(h => {
    h.addEventListener('click', toggleSidebar);
    h.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleSidebar(); } });
  });

  // Backdrop click closes sidebar
  const backdrop = document.getElementById('sidebar-backdrop');
  if (backdrop) {
    backdrop.addEventListener('click', () => { closeSidebar(); });
  }

  // Close sidebar function should also remove body class and update aria
  function closeSidebar() { const sb = document.querySelector('.sidebar'); if (sb) sb.classList.remove('open'); document.body.classList.remove('sidebar-open'); document.querySelectorAll('.hamburger').forEach(h=>h.setAttribute('aria-expanded','false')); const bd = document.getElementById('sidebar-backdrop'); if (bd) bd.setAttribute('aria-hidden','true'); }

  // (actions-hamburger removed) single hamburger toggles sidebar

  // Theme toggle binding and init
  const themeToggle = document.querySelector('.theme-toggle');
  if (themeToggle) {
    themeToggle.setAttribute('role','switch');
    themeToggle.setAttribute('tabindex','0');
    themeToggle.addEventListener('click', toggleTheme);
    themeToggle.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleTheme(); } });
    // Initialize theme from localStorage
    const savedTheme = localStorage.getItem('theme');
    const isLight = savedTheme === 'light';
    document.body.classList.toggle('light', isLight);
    themeToggle.classList.toggle('on', isLight);
    themeToggle.classList.toggle('off', !isLight);
    themeToggle.setAttribute('aria-checked', isLight ? 'true' : 'false');
  }

  // Show/Hide password toggles for all password inputs (login/signup)
  document.querySelectorAll('input[type="password"]').forEach((pwd) => {
    // wrap input in a div for positioning
    const wrapper = document.createElement('div');
    wrapper.className = 'pw-wrapper';
    pwd.parentNode.insertBefore(wrapper, pwd);
    wrapper.appendChild(pwd);
    // create toggle icon inside wrapper
    const toggle = document.createElement('span');
    toggle.className = 'pw-toggle';
    toggle.setAttribute('aria-label', 'Show password');
    toggle.setAttribute('tabindex', '0');
    toggle.setAttribute('role', 'button');
    // two icons: show and hide
    const showIcon = document.createElement('span');
    showIcon.className = 'pw-icon show';
    showIcon.textContent = '👁️';
    const hideIcon = document.createElement('span');
    hideIcon.className = 'pw-icon hide';
    hideIcon.textContent = '🙈';
    hideIcon.style.display = 'none'; // hide initially
    toggle.appendChild(showIcon);
    toggle.appendChild(hideIcon);
    wrapper.appendChild(toggle);
    const toggleFunc = () => {
      const showing = pwd.type === 'password';
      pwd.type = showing ? 'text' : 'password';
      showIcon.style.display = showing ? 'none' : 'inline';
      hideIcon.style.display = showing ? 'inline' : 'none';
      toggle.setAttribute('aria-label', showing ? 'Hide password' : 'Show password');
    };
    toggle.addEventListener('click', toggleFunc);
    toggle.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleFunc(); } });
  });

  // Prevent page overscroll while a select is focused
  document.querySelectorAll('select').forEach(s => { s.addEventListener('focus', () => document.body.classList.add('select-open')); s.addEventListener('blur', () => document.body.classList.remove('select-open')); });

  // ESC key closes sidebar or modal
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      // close sidebar if open
      const sb = document.querySelector('.sidebar'); if (sb && sb.classList.contains('open')) { closeSidebar(); }
      // close logout modal if open
      const lm = document.getElementById('confirm-logout-modal'); if (lm && lm.classList.contains('open')) { closeModal(lm); }
    }
  });

  // Sidebar search/filter
  // Removed search functionality

  // Logout confirmation binding
  const logoutModal = document.getElementById('confirm-logout-modal');
  document.querySelectorAll('a.logout-link').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault(); const href = link.getAttribute('href');
      if (!logoutModal) { window.location = href; return; }
      logoutModal.dataset.href = href; openModal(logoutModal);
      const confirmBtn = logoutModal.querySelector('.confirm-logout'); if (confirmBtn) confirmBtn.focus();
    });
  });

  // Modal buttons
  if (logoutModal) {
    logoutModal.addEventListener('click', (e) => {
      if (e.target.matches('.confirm-logout')) { const h = logoutModal.dataset.href; closeModal(logoutModal); if (h) window.location = h; }
      if (e.target.matches('.cancel-logout') || e.target === logoutModal) { closeModal(logoutModal); }
    });
  }
});
