/* ============================================================
   KhanNet / Link3 — Main JavaScript
   ============================================================ */

/* ── Config (edit these values) ── */
const CONFIG = {
  whatsappNumber: '8801XXXXXXXXX', // Replace with your WhatsApp number (with country code, no +)
  web3formsKey:   'YOUR_WEB3FORMS_ACCESS_KEY', // Replace with your Web3Forms access key
  businessName:   'KhanNet / Link3',
  areas:          ['Shibrampur', 'Doarpar', 'Atharkhada'],
};

/* ── Sticky nav shadow ── */
const navbar = document.querySelector('.navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 20);
  }, { passive: true });
}

/* ── Mobile nav toggle ── */
const navToggle = document.querySelector('.nav-toggle');
const navDrawer = document.querySelector('.nav-drawer');

if (navToggle && navDrawer) {
  navToggle.addEventListener('click', () => {
    const isOpen = navDrawer.classList.toggle('open');
    navToggle.classList.toggle('open', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });

  /* close drawer when a link is tapped */
  navDrawer.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
      navDrawer.classList.remove('open');
      navToggle.classList.remove('open');
      document.body.style.overflow = '';
    });
  });
}

/* ── Active nav link ── */
(function markActiveLink() {
  const path = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-links a, .nav-drawer a').forEach(a => {
    const href = a.getAttribute('href');
    if (href === path || (path === '' && href === 'index.html')) {
      a.classList.add('active');
    }
  });
})();

/* ── Scroll-reveal ── */
(function initReveal() {
  const targets = document.querySelectorAll('.reveal, .reveal-stagger');
  if (!targets.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });

  targets.forEach(t => observer.observe(t));
})();

/* ── Plans tab switcher ── */
(function initPlansTabs() {
  const tabs    = document.querySelectorAll('.plans-tab');
  const grids   = document.querySelectorAll('.plans-grid');
  if (!tabs.length) return;

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const target = tab.dataset.tab;
      grids.forEach(grid => {
        grid.style.display = grid.dataset.grid === target ? 'grid' : 'none';
      });
    });
  });
})();

/* ── Area Availability Checker ── */
(function initAvailability() {
  const form   = document.getElementById('availability-form');
  const result = document.getElementById('availability-result');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const area = form.querySelector('select').value;
    result.className = 'availability-result';

    if (CONFIG.areas.includes(area)) {
      result.className = 'availability-result available';
      result.innerHTML = `✅ Great news! <strong>${area}</strong> is covered by ${CONFIG.businessName}. <a href="contact.html" style="color:inherit;text-decoration:underline;">Request your connection today →</a>`;
    } else {
      result.className = 'availability-result unavailable';
      result.textContent = `We're not yet in "${area}", but we're expanding! Contact us to be first when we arrive.`;
    }
  });
})();

/* ── FAQ Accordion ── */
(function initFAQ() {
  const items = document.querySelectorAll('.faq-item');
  if (!items.length) return;

  items.forEach(item => {
    const btn    = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');
    if (!btn || !answer) return;

    btn.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      /* close all */
      items.forEach(i => i.classList.remove('open'));
      /* open clicked (unless it was already open) */
      if (!isOpen) item.classList.add('open');
    });
  });
})();

/* ── Connection Request Form (Web3Forms + WhatsApp) ── */
(function initConnectionForm() {
  const form        = document.getElementById('connection-form');
  const successMsg  = document.getElementById('form-success');
  const waBtn       = document.getElementById('whatsapp-submit');
  if (!form) return;

  /* build WhatsApp message from current form values */
  function buildWhatsAppMessage() {
    const data = Object.fromEntries(new FormData(form));
    const msg  = [
      `*New Connection Request — ${CONFIG.businessName}*`,
      ``,
      `👤 Name:    ${data.name      || '—'}`,
      `📱 Mobile:  ${data.mobile    || '—'}`,
      `📍 Area:    ${data.area      || '—'}`,
      `🏠 Address: ${data.address   || '—'}`,
      `📦 Plan:    ${data.plan      || '—'}`,
    ].join('\n');
    return encodeURIComponent(msg);
  }

  /* update WA link whenever form values change */
  if (waBtn) {
    form.addEventListener('input', () => {
      waBtn.href = `https://wa.me/${CONFIG.whatsappNumber}?text=${buildWhatsAppMessage()}`;
    });

    waBtn.addEventListener('click', (e) => {
      const name   = form.querySelector('[name=name]')?.value?.trim();
      const mobile = form.querySelector('[name=mobile]')?.value?.trim();
      if (!name || !mobile) {
        e.preventDefault();
        alert('Please fill in at least your name and mobile number before using WhatsApp.');
        return;
      }
      waBtn.href = `https://wa.me/${CONFIG.whatsappNumber}?text=${buildWhatsAppMessage()}`;
    });
  }

  /* Web3Forms submission */
  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn  = form.querySelector('[type=submit]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled    = true;
    submitBtn.textContent = 'Sending…';

    const formData = new FormData(form);
    formData.set('access_key', CONFIG.web3formsKey);
    formData.set('subject',    `New Connection Request — ${form.querySelector('[name=area]')?.value}`);
    formData.set('from_name',  CONFIG.businessName);

    try {
      const res  = await fetch('https://api.web3forms.com/submit', {
        method: 'POST',
        body:   formData,
      });
      const json = await res.json();

      if (json.success) {
        form.style.display      = 'none';
        successMsg.classList.add('show');
      } else {
        throw new Error(json.message || 'Submission failed');
      }
    } catch (err) {
      alert(`Error sending form: ${err.message}\n\nYou can also reach us via WhatsApp.`);
      submitBtn.disabled    = false;
      submitBtn.textContent = originalText;
    }
  });
})();

/* ── Smooth counter animation (numbers section) ── */
(function initCounters() {
  const counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el     = e.target;
      const target = parseInt(el.dataset.count, 10);
      const suffix = el.dataset.suffix || '';
      let   start  = 0;
      const step   = Math.ceil(target / 60);
      const tick   = () => {
        start = Math.min(start + step, target);
        el.textContent = start.toLocaleString() + suffix;
        if (start < target) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });

  counters.forEach(c => observer.observe(c));
})();

/* ── Page fade-in ── */
document.body.classList.add('page-fade');
