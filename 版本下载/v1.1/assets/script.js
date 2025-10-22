(function(){
  const root = document.documentElement;
  const TOGGLE_ID = 'themeToggle';
  const KEY = 'lynyie-theme';

  function applyTheme(theme) {
    if (theme === 'light') {
      root.setAttribute('data-theme', 'light');
    } else {
      root.removeAttribute('data-theme');
    }
    try { localStorage.setItem(KEY, theme); } catch {}
  }

  function initTheme() {
    let theme = 'dark';
    try {
      const saved = localStorage.getItem(KEY);
      if (saved) theme = saved;
    } catch {}
    applyTheme(theme);
  }

  function bindToggle() {
    const btn = document.getElementById(TOGGLE_ID);
    if (!btn) return;
    
    btn.addEventListener('click', function(e){
      // 获取按钮位置用于动画起始点
      const rect = btn.getBoundingClientRect();
      const x = rect.left + rect.width / 2;
      const y = rect.top + rect.height / 2;
      
      // 添加动画类
      root.classList.add('theme-transition');
      root.style.setProperty('--theme-transition-x', `${x}px`);
      root.style.setProperty('--theme-transition-y', `${y}px`);
      
      // 触发动画
      requestAnimationFrame(() => {
        root.classList.add('theme-transition-active');
      });
      
      // 切换主题
      const isLight = root.getAttribute('data-theme') === 'light';
      const newTheme = isLight ? 'dark' : 'light';
      
      // 等待动画完成后应用主题
      setTimeout(() => {
        applyTheme(newTheme);
        // 移除动画类
        root.classList.remove('theme-transition', 'theme-transition-active');
      }, 300);
    });
  }

  function autoFocusSearch(){
    const params = new URLSearchParams(location.search);
    if (!params.has('q')) return;
    const input = document.querySelector('input[type="search"]');
    if (input) input.focus();
  }

  function setTheme(theme) {
    applyTheme(theme);
    updateThemeRadios(theme);
  }

  function updateThemeRadios(current) {
    const radios = document.querySelectorAll('input[name="theme"]');
    radios.forEach(r => { r.checked = (r.value === current); });
  }

  function openSettingsPanel() {
    const panel = document.getElementById('settingsPanel');
    if (!panel) return;
    panel.classList.add('open');
    panel.setAttribute('aria-hidden', 'false');
    updateThemeRadios(root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');
  }

  function closeSettingsPanel() {
    const panel = document.getElementById('settingsPanel');
    if (!panel) return;
    panel.classList.remove('open');
    panel.setAttribute('aria-hidden', 'true');
  }

  function bindSettingsPanel() {
    const fab = document.getElementById('settingsFab');
    const closeBtn = document.getElementById('settingsClose');
    const backdrop = document.getElementById('settingsBackdrop');
    const radios = document.querySelectorAll('input[name="theme"]');

    if (fab) fab.addEventListener('click', openSettingsPanel);
    if (closeBtn) closeBtn.addEventListener('click', closeSettingsPanel);
    if (backdrop) backdrop.addEventListener('click', closeSettingsPanel);

    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape') closeSettingsPanel();
    });

    radios.forEach(r => {
      r.addEventListener('change', function(){
        if (this.checked) setTheme(this.value);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function(){
    initTheme();
    bindToggle();
    autoFocusSearch();
    bindSettingsPanel();
  });
})();