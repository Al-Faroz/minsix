document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('siteNavToggle');
  const nav = document.getElementById('siteNav');
  if (!toggle || !nav) return;

  const close = () => {
    nav.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
  };

  toggle.addEventListener('click', () => {
    const open = !nav.classList.contains('open');
    nav.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });

  nav.querySelectorAll('a').forEach(link => link.addEventListener('click', close));
  window.addEventListener('resize', () => {
    if (window.innerWidth > 980) close();
  });
});


document.addEventListener('DOMContentLoaded', () => {
  const lightbox = document.getElementById('siteLightbox');
  if (!lightbox) return;

  const image = lightbox.querySelector('img');
  const closeButton = lightbox.querySelector('.lightbox-close');

  const closeLightbox = () => {
    lightbox.hidden = true;
    image.src = '';
    image.alt = '';
    document.body.style.overflow = '';
  };

  document.querySelectorAll('[data-lightbox-src]').forEach(item => {
    item.addEventListener('click', () => {
      image.src = item.getAttribute('data-lightbox-src') || '';
      image.alt = item.getAttribute('data-lightbox-alt') || '';
      lightbox.hidden = false;
      document.body.style.overflow = 'hidden';
      closeButton?.focus();
    });
  });

  closeButton?.addEventListener('click', closeLightbox);
  lightbox.addEventListener('click', event => {
    if (event.target === lightbox) closeLightbox();
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !lightbox.hidden) closeLightbox();
  });
});
