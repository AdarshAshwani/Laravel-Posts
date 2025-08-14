import './bootstrap';

/* Spotlight cursor effect for elements that use a radial ::after */
document.addEventListener('mousemove', (e)=>{
  document.querySelectorAll('.spotlight, .composer-launch').forEach(el=>{
    const r = el.getBoundingClientRect();
    const x = e.clientX - r.left, y = e.clientY - r.top;
    el.style.setProperty('--x', `${x}px`);
    el.style.setProperty('--y', `${y}px`);
  });
});

/* Theme toggle (attach to any element with data-theme-toggle) */
function toggleTheme(){
  const html = document.documentElement;
  const isDark = html.classList.toggle('dark');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
}
document.querySelectorAll('[data-theme-toggle]').forEach(btn=>{
  btn.addEventListener('click', toggleTheme);
});
