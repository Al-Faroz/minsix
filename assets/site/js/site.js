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


document.addEventListener('DOMContentLoaded', () => {
  const track = document.querySelector('[data-instagram-carousel]');
  const prev = document.querySelector('[data-instagram-prev]');
  const next = document.querySelector('[data-instagram-next]');

  if (!track || !prev || !next) return;

  const step = () => Math.max(track.clientWidth * 0.82, 240);

  prev.addEventListener('click', () => {
    track.scrollBy({ left: -step(), behavior: 'smooth' });
  });

  next.addEventListener('click', () => {
    track.scrollBy({ left: step(), behavior: 'smooth' });
  });
});


document.addEventListener('DOMContentLoaded', () => {
  const selectors = [
    '.section-heading',
    '.split-editorial',
    '.habit-layout',
    '.program-row',
    '.achievement-card',
    '.story-card',
    '.news-card',
    '.agenda-panel',
    '.instagram-card',
    '.headmaster-grid',
    '.spmb-grid',
    '.contact-grid',
    '.profile-block',
    '.program-group',
    '.gtk-card',
    '.public-card',
    '.event-row',
    '.gallery-public-card',
    '.article-header',
    '.article-cover',
    '.article-body',
    '.spmb-public-intro',
    '.requirement-list',
    '.faq-public'
  ];

  const items = document.querySelectorAll(selectors.join(','));
  if (!items.length) return;

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
    items.forEach(item => item.classList.add('is-visible'));
    return;
  }

  items.forEach(item => item.classList.add('rival-reveal'));

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -5% 0px' });

  items.forEach(item => observer.observe(item));
});
