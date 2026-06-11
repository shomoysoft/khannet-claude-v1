/* ============================================================
   KhanNet / Link3 — Main JavaScript
   Reads business data from js/config.js (SITE object)
   ============================================================ */

/* ── Apply config to DOM ─────────────────────────────────────
   Runs once on page load.
   1. Auto-patches all tel:, wa.me, mailto: hrefs
   2. Fills [data-config-text] spans with the right value
   3. Populates [data-config-areas] selects from SITE.areas
   4. Populates [data-config-plans] selects from SITE.plans
─────────────────────────────────────────────────────────── */
(function applyConfig() {

  /* 1. Auto-patch hrefs — no HTML changes needed */
  document.querySelectorAll('a[href^="tel:"]').forEach(a => {
    a.href = `tel:+${SITE.phoneIntl}`;
  });

  document.querySelectorAll('a[href^="https://wa.me/"]').forEach(a => {
    /* preserve any ?text= query string already on the link */
    const qs = a.href.includes('?') ? a.href.slice(a.href.indexOf('?')) : '';
    a.href = `https://wa.me/${SITE.whatsapp}${qs}`;
  });

  document.querySelectorAll('a[href^="mailto:info@"]').forEach(a => {
    a.href = `mailto:${SITE.email}`;
  });

  document.querySelectorAll('a[href^="mailto:support@"]').forEach(a => {
    a.href = `mailto:${SITE.supportEmail}`;
  });

  /* 2. Text injection via data-config-text attribute */
  const textMap = {
    phone:        SITE.phone,
    phoneIntl:    SITE.phoneIntl,
    email:        SITE.email,
    supportEmail: SITE.supportEmail,
    address:      SITE.address.full,
    companyName:  SITE.fullName,
    areas:        SITE.areas.join(', '),
  };

  document.querySelectorAll('[data-config-text]').forEach(el => {
    const val = textMap[el.dataset.configText];
    if (val !== undefined) el.textContent = val;
  });

  /* 3. Populate area <select> elements */
  document.querySelectorAll('[data-config-areas]').forEach(select => {
    const placeholder = select.options[0]; // keep the "-- Select --" option
    select.innerHTML  = '';
    select.appendChild(placeholder);

    SITE.areas.forEach(area => {
      const opt = document.createElement('option');
      opt.value       = area;
      opt.textContent = area;
      select.appendChild(opt);
    });

    /* add "Other" only if the select explicitly requests it */
    if (select.dataset.configAreasOther !== undefined) {
      const other = document.createElement('option');
      other.value       = 'Other';
      other.textContent = 'Other area…';
      select.appendChild(other);
    }
  });

  /* 4. Populate plan <select> element */
  document.querySelectorAll('[data-config-plans]').forEach(select => {
    const placeholder = select.options[0];
    select.innerHTML  = '';
    select.appendChild(placeholder);

    ['home', 'business'].forEach(type => {
      const group       = document.createElement('optgroup');
      group.label       = type === 'home' ? 'Home Plans' : 'Business Plans';

      SITE.plans[type].forEach(plan => {
        const opt       = document.createElement('option');
        const price     = plan.price.toLocaleString();
        opt.value       = `${plan.name} (${plan.speed} Mbps — ৳${price}/mo)`;
        opt.textContent = `${plan.name} — ${plan.speed} Mbps — ৳${price}/mo`;
        group.appendChild(opt);
      });

      select.appendChild(group);
    });

    const other       = document.createElement('option');
    other.value       = 'Not sure — need advice';
    other.textContent = 'Not sure — need advice';
    select.appendChild(other);
  });

})();

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
  const tabs  = document.querySelectorAll('.plans-tab');
  const grids = document.querySelectorAll('.plans-grid');
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

    if (SITE.areas.includes(area)) {
      result.className = 'availability-result available';
      result.innerHTML = `✅ Great news! <strong>${area}</strong> is covered by ${SITE.fullName}. <a href="contact.html" style="color:inherit;text-decoration:underline;">Request your connection today →</a>`;
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
      items.forEach(i => i.classList.remove('open'));
      if (!isOpen) item.classList.add('open');
    });
  });
})();

/* ── Connection Request Form (Web3Forms + WhatsApp) ── */
(function initConnectionForm() {
  const form       = document.getElementById('connection-form');
  const successMsg = document.getElementById('form-success');
  const waBtn      = document.getElementById('whatsapp-submit');
  if (!form) return;

  function buildWhatsAppMessage() {
    const data = Object.fromEntries(new FormData(form));
    const msg  = [
      `*New Connection Request — ${SITE.fullName}*`,
      ``,
      `👤 Name:    ${data.name    || '—'}`,
      `📱 Mobile:  ${data.mobile  || '—'}`,
      `📍 Area:    ${data.area    || '—'}`,
      `🏠 Address: ${data.address || '—'}`,
      `📦 Plan:    ${data.plan    || '—'}`,
    ].join('\n');
    return encodeURIComponent(msg);
  }

  if (waBtn) {
    form.addEventListener('input', () => {
      waBtn.href = `https://wa.me/${SITE.whatsapp}?text=${buildWhatsAppMessage()}`;
    });

    waBtn.addEventListener('click', (e) => {
      const name   = form.querySelector('[name=name]')?.value?.trim();
      const mobile = form.querySelector('[name=mobile]')?.value?.trim();
      if (!name || !mobile) {
        e.preventDefault();
        alert('Please fill in at least your name and mobile number before using WhatsApp.');
        return;
      }
      waBtn.href = `https://wa.me/${SITE.whatsapp}?text=${buildWhatsAppMessage()}`;
    });
  }

  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn     = form.querySelector('[type=submit]');
    const originalText  = submitBtn.textContent;
    submitBtn.disabled    = true;
    submitBtn.textContent = 'Sending…';

    try {
      const res  = await fetch('/api/submit', {
        method: 'POST',
        body:   new FormData(form),
      });
      const json = await res.json();

      if (json.success) {
        form.style.display = 'none';
        successMsg.classList.add('show');
      } else {
        throw new Error(json.message || 'Submission failed');
      }
    } catch (err) {
      alert('Could not send your request: ' + err.message + '\n\nPlease use WhatsApp instead.');
      submitBtn.disabled    = false;
      submitBtn.textContent = originalText;
    }
  });
})();

/* ── Smooth counter animation ── */
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

/* ── Custom styled selects ── */
(function initCustomSelects() {
  document.querySelectorAll('select').forEach(function(select) {

    var wrapper = document.createElement('div');
    wrapper.className = 'custom-select-wrapper';
    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(select);

    var trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'custom-select-trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');

    var valueSpan = document.createElement('span');
    valueSpan.className = 'custom-select-value placeholder';

    var arrowSpan = document.createElement('span');
    arrowSpan.className = 'custom-select-arrow';
    arrowSpan.innerHTML = '&#9660;';

    trigger.appendChild(valueSpan);
    trigger.appendChild(arrowSpan);
    wrapper.appendChild(trigger);

    var panel = document.createElement('div');
    panel.className = 'custom-select-panel';
    panel.setAttribute('role', 'listbox');
    wrapper.appendChild(panel);

    function makeOption(opt) {
      if (opt.disabled) return;
      var div = document.createElement('div');
      div.className = 'custom-select-option';
      div.dataset.value = opt.value;
      div.textContent = opt.textContent;
      div.setAttribute('role', 'option');
      div.addEventListener('click', function() {
        select.value = opt.value;
        select.dispatchEvent(new Event('change', { bubbles: true }));
        updateDisplay();
        closePanel();
      });
      panel.appendChild(div);
    }

    function buildPanel() {
      panel.innerHTML = '';
      Array.from(select.children).forEach(function(child) {
        if (child.tagName === 'OPTGROUP') {
          var lbl = document.createElement('div');
          lbl.className = 'custom-select-group-label';
          lbl.textContent = child.label;
          panel.appendChild(lbl);
          Array.from(child.children).forEach(makeOption);
        } else {
          makeOption(child);
        }
      });
    }

    function updateDisplay() {
      var sel = select.options[select.selectedIndex];
      if (!sel || sel.disabled || !sel.value) {
        valueSpan.textContent = sel ? sel.textContent : '—';
        valueSpan.className = 'custom-select-value placeholder';
      } else {
        valueSpan.textContent = sel.textContent;
        valueSpan.className = 'custom-select-value';
      }
      panel.querySelectorAll('.custom-select-option').forEach(function(opt) {
        opt.classList.toggle('selected', opt.dataset.value === select.value);
      });
    }

    function openPanel() {
      document.querySelectorAll('.custom-select-wrapper.open').forEach(function(w) {
        if (w !== wrapper) {
          w.classList.remove('open');
          var t = w.querySelector('.custom-select-trigger');
          if (t) t.setAttribute('aria-expanded', 'false');
        }
      });
      wrapper.classList.add('open');
      trigger.setAttribute('aria-expanded', 'true');
      var sel = panel.querySelector('.selected');
      if (sel) sel.scrollIntoView({ block: 'nearest' });
    }

    function closePanel() {
      wrapper.classList.remove('open');
      trigger.setAttribute('aria-expanded', 'false');
    }

    trigger.addEventListener('click', function(e) {
      e.stopPropagation();
      wrapper.classList.contains('open') ? closePanel() : openPanel();
    });

    document.addEventListener('click', function(e) {
      if (!wrapper.contains(e.target)) closePanel();
    });

    /* sync display when value changes programmatically (e.g. plan pre-fill) */
    select.addEventListener('change', updateDisplay);

    buildPanel();
    updateDisplay();

    /* rebuild if applyConfig changes options later */
    new MutationObserver(function() {
      buildPanel();
      updateDisplay();
    }).observe(select, { childList: true, subtree: true });
  });
})();

/* ── Wire plan cards → contact page with plan pre-selected ── */
(function initPlanLinks() {
  document.querySelectorAll('.plan-card .btn[href*="contact.html"]').forEach(function(btn) {
    var card   = btn.closest('.plan-card');
    var nameEl = card && card.querySelector('.plan-name');
    if (!nameEl) return;
    try {
      var url = new URL(btn.href, window.location.href);
      url.searchParams.set('plan', nameEl.textContent.trim());
      url.hash = 'connection-form';
      btn.href = url.toString();
    } catch (_) {}
  });
})();

/* ── Pre-fill plan dropdown from ?plan= URL param (contact.html) ── */
(function initPlanPreFill() {
  var planSelect = document.getElementById('plan');
  if (!planSelect) return;

  var urlPlan = new URLSearchParams(window.location.search).get('plan');
  if (!urlPlan) return;

  var target = urlPlan.toLowerCase().trim();
  var matched = null;

  Array.from(planSelect.options).forEach(function(opt) {
    if (!matched && opt.value.toLowerCase().startsWith(target)) {
      matched = opt;
    }
  });

  if (matched) {
    planSelect.value = matched.value;
    planSelect.dispatchEvent(new Event('change', { bubbles: true }));
    var formEl = document.getElementById('connection-form');
    if (formEl) setTimeout(function() {
      formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
  }
})();

/* ── Page fade-in ── */
document.body.classList.add('page-fade');
