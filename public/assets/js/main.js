/**
 * Main JavaScript
 * Handles navigation interactions, mobile menu toggle, dropdowns, and footer utilities.
 */
(function () {
  'use strict';

  // Header Mobile Navigation
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navPanel = document.querySelector('.nav-panel');
  const menuButtons = document.querySelectorAll('.has-menu > .nav-link');
  const isDesktop = window.matchMedia('(min-width: 992px)'); // Laptop+ (see AGENTS.md breakpoints)

  if (mobileToggle && navPanel) {
    mobileToggle.addEventListener('click', function () {
      const isOpen = navPanel.classList.toggle('is-open');
      mobileToggle.setAttribute('aria-expanded', String(isOpen));
      document.body.classList.toggle('nav-open', isOpen);
    });

    // Close mobile nav when clicking outside
    document.addEventListener('click', function (e) {
      if (
        !isDesktop.matches &&
        navPanel.classList.contains('is-open') &&
        !navPanel.contains(e.target) &&
        !mobileToggle.contains(e.target)
      ) {
        navPanel.classList.remove('is-open');
        mobileToggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('nav-open');
      }
    });

    // Close mobile nav on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && navPanel.classList.contains('is-open')) {
        navPanel.classList.remove('is-open');
        mobileToggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('nav-open');
      }
    });
  }

  // Mobile Submenu / Mega-Menu Accordion
  menuButtons.forEach(function (button) {
    button.addEventListener('click', function (e) {
      const parentItem = button.parentElement;
      if (!isDesktop.matches) {
        e.preventDefault();
        // Close other open submenus if desired
        document.querySelectorAll('.has-menu.is-open').forEach(function (openItem) {
          if (openItem !== parentItem) {
            openItem.classList.remove('is-open');
            var openBtn = openItem.querySelector('.nav-link');
            if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
          }
        });
        var nowOpen = parentItem.classList.toggle('is-open');
        button.setAttribute('aria-expanded', String(nowOpen));
      }
    });
  });

  // Footer Dynamic Copyright Year
  const yearHolders = document.querySelectorAll('[data-current-year]');
  if (yearHolders.length) {
    const currentYear = String(new Date().getFullYear());
    yearHolders.forEach(function (el) {
      el.textContent = currentYear;
    });
  }

  // ==========================================================================
  // Domain Search component (homepage + domain pages) — [data-domain-search]
  // ==========================================================================
  var CART_URL = 'https://robot.hostdivine.com/cart.php';

  var cleanDomain = function (value) {
    return value.trim().toLowerCase()
      .replace(/^https?:\/\//, '')
      .replace(/^www\./, '')
      .replace(/\/.*$/, '')
      .replace(/[^a-z0-9.\-¡-￿]/g, '');
  };

  document.querySelectorAll('[data-domain-search]').forEach(function (root) {
    var form = root.querySelector('[data-ds-form]');
    var input = root.querySelector('[data-ds-input]');
    var select = root.querySelector('[data-ds-tld]');
    var action = root.querySelector('[data-ds-action]');
    var label = root.querySelector('[data-ds-label]');
    var modes = root.querySelectorAll('[data-ds-mode]');
    var chips = root.querySelectorAll('.ds-tld');
    var preview = root.querySelector('[data-ds-preview]');
    var previewName = root.querySelector('[data-ds-name]');
    var previewList = root.querySelector('[data-ds-preview-list]');
    if (!form || !input) return;

    var mode = action ? action.value : 'register';

    var setTld = function (tld) {
      if (select && tld) {
        var found = Array.prototype.some.call(select.options, function (opt) {
          if (opt.value === tld) { select.value = tld; return true; }
          return false;
        });
        if (!found) select.add(new Option(tld, tld, true, true));
      }
      chips.forEach(function (chip) {
        chip.classList.toggle('is-active', chip.getAttribute('data-tld') === tld);
      });
    };

    var cartLink = function (domain) {
      return CART_URL + '?a=add&domain=' + mode + '&query=' + encodeURIComponent(domain);
    };

    // Live preview: the typed name with popular extensions and prices
    var renderPreview = function () {
      if (!preview || !previewList) return;
      var name = cleanDomain(input.value).split('.')[0];
      if (!name || mode !== 'register') {
        preview.hidden = true;
        return;
      }
      previewName.textContent = name;
      previewList.innerHTML = '';
      Array.prototype.slice.call(chips, 0, 4).forEach(function (chip) {
        var tld = chip.getAttribute('data-tld');
        var link = document.createElement('a');
        link.className = 'ds-preview-item';
        link.href = cartLink(name + tld);
        var text = document.createElement('span');
        text.textContent = name + tld;
        var price = document.createElement('b');
        var taka = document.createElement('span');
        taka.className = 'tk';
        taka.textContent = '৳';
        price.appendChild(taka);
        price.appendChild(document.createTextNode(chip.getAttribute('data-price') + '/yr'));
        var arrow = document.createElement('i');
        arrow.className = 'fa-solid fa-cart-plus';
        arrow.setAttribute('aria-hidden', 'true');
        link.appendChild(text);
        link.appendChild(price);
        link.appendChild(arrow);
        previewList.appendChild(link);
      });
      preview.hidden = false;
    };

    modes.forEach(function (btn) {
      btn.addEventListener('click', function () {
        mode = btn.getAttribute('data-ds-mode');
        modes.forEach(function (b) {
          var on = b === btn;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-checked', String(on));
        });
        if (action) action.value = mode;
        if (label) label.textContent = mode === 'transfer' ? 'Transfer' : 'Search';
        input.placeholder = mode === 'transfer' ? 'Enter the domain you want to transfer' : 'Find your perfect domain name…';
        renderPreview();
        input.focus();
      });
    });

    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        setTld(chip.getAttribute('data-tld'));
        input.focus();
      });
    });

    if (select) {
      select.addEventListener('change', function () { setTld(select.value); });
    }

    input.addEventListener('input', renderPreview);

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var domain = cleanDomain(input.value);
      if (!domain) {
        input.focus();
        return;
      }
      if (!/\.[a-z0-9\-¡-￿]{2,}$/.test(domain)) {
        domain += select ? select.value : '.com';
      }
      window.location.href = cartLink(domain);
    });
  });

  // ==========================================================================
  // Domain price table filter — [data-tld-filter] / [data-tld-table]
  // ==========================================================================
  var tldTable = document.querySelector('[data-tld-table]');
  if (tldTable) {
    var tldRows = Array.prototype.slice.call(tldTable.querySelectorAll('tbody tr'));
    var tldFilter = document.querySelector('[data-tld-filter]');
    var tldCount = document.querySelector('[data-tld-count]');
    var tldEmpty = document.querySelector('[data-tld-empty]');
    var tldMore = document.querySelector('[data-tld-more]');
    var limit = parseInt(tldTable.getAttribute('data-tld-limit'), 10) || 0;
    var expanded = !limit;

    var applyTldFilter = function () {
      var q = tldFilter ? tldFilter.value.trim().toLowerCase().replace(/^\.?/, '.') : '';
      var searching = q.length > 1;
      var shown = 0;
      var matches = 0;
      tldRows.forEach(function (row) {
        var tld = row.getAttribute('data-tld') || '';
        var match = !searching || tld.indexOf(q) === 0 || tld.indexOf(q.slice(1)) > -1;
        if (match) matches += 1;
        var visible = match && (searching || expanded || shown < limit);
        row.hidden = !visible;
        if (visible) shown += 1;
      });
      if (tldCount) tldCount.textContent = searching ? matches + ' matching extensions' : tldRows.length + ' extensions';
      if (tldEmpty) tldEmpty.hidden = matches > 0;
      if (tldMore) tldMore.hidden = expanded || searching;
    };

    if (tldFilter) tldFilter.addEventListener('input', applyTldFilter);
    if (tldMore) {
      tldMore.addEventListener('click', function () {
        expanded = true;
        applyTldFilter();
      });
    }
    applyTldFilter();
  }

  // ==========================================================================
  // Contact form — posts to the Laravel backend (falls back to a normal
  // form submit when fetch is unavailable)
  // ==========================================================================
  var contactForm = document.querySelector('[data-contact-form]');
  if (contactForm && window.fetch && window.FormData) {
    contactForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var status = contactForm.querySelector('[data-form-status]');
      var submit = contactForm.querySelector('[type="submit"]');
      var show = function (text) {
        if (!status) return;
        status.textContent = text;
        status.hidden = false;
      };
      if (submit) submit.disabled = true;

      fetch(contactForm.action, {
        method: 'POST',
        body: new FormData(contactForm),
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      })
        .then(function (response) {
          return response.json().catch(function () { return {}; }).then(function (body) {
            return { ok: response.ok, body: body };
          });
        })
        .then(function (result) {
          if (result.ok) {
            show(result.body.message || 'Thank you! Your message has been received.');
            contactForm.reset();
            return;
          }
          var errors = result.body.errors || {};
          var first = Object.keys(errors)[0];
          show(first ? errors[first][0] : (result.body.message || 'Something went wrong. Please try again.'));
        })
        .catch(function () {
          show('Network error. Please try again or contact us on WhatsApp.');
        })
        .then(function () {
          if (submit) submit.disabled = false;
        });
    });
  }
})();


/**
 * Homepage interactions
 * Hero slider · card sliders · billing period switch · reseller tabs ·
 * scroll reveal · stat counters
 */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // ==========================================================================
  // Hero Slider (autoplay is driven by the progress-bar animation, so pausing
  // the animation pauses the slider in perfect sync)
  // ==========================================================================
  var hero = document.querySelector('.hero-slider');

  if (hero) {
    var slides = hero.querySelectorAll('.hero-slide');
    var tabs = hero.querySelectorAll('[data-hero-goto]');
    var bgImages = hero.querySelectorAll('.hero-bg-image');
    var viewport = hero.querySelector('[data-hero-viewport]');
    var current = 0;
    var autoplay = !reduceMotion && slides.length > 1;

    var goTo = function (index) {
      var total = slides.length;
      current = (index + total) % total;

      hero.setAttribute('data-active-slide', String(current));

      slides.forEach(function (slide, i) {
        var active = i === current;
        slide.classList.toggle('is-active', active);
        slide.setAttribute('aria-hidden', String(!active));
        if ('inert' in slide) slide.inert = !active;
      });

      bgImages.forEach(function (bg, i) {
        bg.classList.toggle('is-active', i === current);
      });

      tabs.forEach(function (tab, i) {
        var active = i === current;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', String(active));
      });
    };

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        goTo(parseInt(tab.getAttribute('data-hero-goto'), 10));
      });
      // Advance when the active tab's progress bar finishes
      var bar = tab.querySelector('.hero-progress');
      if (bar) {
        bar.addEventListener('animationend', function () {
          if (autoplay && tab.classList.contains('is-active')) goTo(current + 1);
        });
      }
    });

    var prevBtn = hero.querySelector('[data-hero-prev]');
    var nextBtn = hero.querySelector('[data-hero-next]');
    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(current - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(current + 1); });

    // Keyboard arrows while focus is inside the slider
    hero.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') goTo(current - 1);
      if (e.key === 'ArrowRight') goTo(current + 1);
    });

    // Swipe support
    if (viewport) {
      var startX = null;
      var startY = null;
      viewport.addEventListener('pointerdown', function (e) {
        startX = e.clientX;
        startY = e.clientY;
      });
      viewport.addEventListener('pointerup', function (e) {
        if (startX === null) return;
        var dx = e.clientX - startX;
        var dy = e.clientY - startY;
        if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
          goTo(current + (dx < 0 ? 1 : -1));
        }
        startX = null;
      });
      viewport.addEventListener('pointercancel', function () { startX = null; });
    }

    // Pause on hover / focus / hidden tab
    var setPaused = function (paused) {
      hero.classList.toggle('is-paused', paused);
    };
    hero.addEventListener('mouseenter', function () { setPaused(true); });
    hero.addEventListener('mouseleave', function () { setPaused(false); });
    hero.addEventListener('focusin', function () { setPaused(true); });
    hero.addEventListener('focusout', function (e) {
      if (!hero.contains(e.relatedTarget)) setPaused(false);
    });
    document.addEventListener('visibilitychange', function () {
      setPaused(document.hidden);
    });

    goTo(0);
    if (autoplay) hero.classList.add('is-playing');

    // Fit the hero to the device height (desktop only — see style.css)
    var siteHeader = document.querySelector('.site-header');
    var desktopMq = window.matchMedia('(min-width: 992px)');
    var MIN_SCALE = 0.7;
    var MAX_HERO_H = 1000;

    var fitHero = function () {
      hero.classList.remove('is-compact');
      if (!desktopMq.matches) {
        hero.style.removeProperty('--hero-scale');
        hero.style.removeProperty('--hero-min');
        return;
      }

      var pageZoom = parseFloat(window.getComputedStyle(document.documentElement).zoom) || 1;
      var headerH = siteHeader ? siteHeader.offsetHeight : 0;
      var avail = Math.round(window.innerHeight / pageZoom - headerH);

      // Measure the natural content height (no min-height) at a given scale
      hero.style.setProperty('--hero-min', 'auto');
      var heightAt = function (scale) {
        hero.style.setProperty('--hero-scale', String(scale));
        return hero.offsetHeight;
      };

      var scale = 1;
      var h = heightAt(1);
      if (h > avail) {
        // Text reflows when zoomed, so refine the first estimate once
        scale = Math.max(MIN_SCALE, avail / h);
        h = heightAt(scale);
        var refined = Math.min(1, scale * avail / h);
        if (refined > scale) {
          var h2 = heightAt(refined);
          if (h2 <= avail) { scale = refined; h = h2; } else { h = heightAt(scale); }
        }
        if (h > avail) {
          hero.classList.add('is-compact');
          h = heightAt(scale);
        }
      }

      hero.style.setProperty('--hero-scale', scale.toFixed(3));
      hero.style.setProperty('--hero-min', Math.min(avail, MAX_HERO_H) + 'px');
    };

    var fitFrame;
    var scheduleFit = function () {
      window.cancelAnimationFrame(fitFrame);
      fitFrame = window.requestAnimationFrame(fitHero);
    };

    fitHero();
    window.addEventListener('resize', scheduleFit);
    window.addEventListener('load', scheduleFit);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(scheduleFit);
  }

  // ==========================================================================
  // Card Sliders (scroll-snap track + arrows + dots)
  // ==========================================================================
  var sliders = [];

  document.querySelectorAll('[data-slider]').forEach(function (slider) {
    var track = slider.querySelector('[data-slider-track]');
    var prev = slider.querySelector('[data-slider-prev]');
    var next = slider.querySelector('[data-slider-next]');
    var dotsWrap = slider.querySelector('[data-slider-dots]');
    if (!track) return;

    var items = Array.prototype.slice.call(track.children);
    var dots = [];

    var step = function () {
      if (items.length < 2) return track.clientWidth;
      return items[1].offsetLeft - items[0].offsetLeft;
    };

    var maxScroll = function () {
      return track.scrollWidth - track.clientWidth;
    };

    var pageCount = function () {
      var s = step();
      if (!s) return 1;
      return Math.max(1, Math.ceil(maxScroll() / s - 0.05) + 1);
    };

    var buildDots = function () {
      if (!dotsWrap) return;
      var count = pageCount();
      if (dots.length === count) return;
      dotsWrap.innerHTML = '';
      dots = [];
      for (var i = 0; i < count; i++) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        (function (index) {
          dot.addEventListener('click', function () {
            track.scrollTo({ left: Math.min(index * step(), maxScroll()) });
          });
        })(i);
        dotsWrap.appendChild(dot);
        dots.push(dot);
      }
    };

    var update = function () {
      // Hidden (e.g. inactive tab) — skip until visible
      if (!track.clientWidth) return;
      var overflow = maxScroll() > 2;
      slider.classList.toggle('is-static', !overflow);
      buildDots();

      var left = track.scrollLeft;
      var max = maxScroll();
      if (prev) prev.disabled = left <= 2;
      if (next) next.disabled = left >= max - 2;

      var active = left >= max - 2 ? dots.length - 1 : Math.round(left / (step() || 1));
      dots.forEach(function (dot, i) {
        dot.classList.toggle('is-active', i === active);
        dot.setAttribute('aria-current', i === active ? 'true' : 'false');
      });
    };

    if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -step() }); });
    if (next) next.addEventListener('click', function () { track.scrollBy({ left: step() }); });

    track.addEventListener('keydown', function (e) {
      if (e.target !== track) return;
      if (e.key === 'ArrowRight') { e.preventDefault(); track.scrollBy({ left: step() }); }
      if (e.key === 'ArrowLeft') { e.preventDefault(); track.scrollBy({ left: -step() }); }
    });

    var ticking = false;
    track.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(function () {
        update();
        ticking = false;
      });
    }, { passive: true });

    // reset() re-measures (dot count can change with breakpoint / visibility)
    sliders.push({ reset: function () { dots = []; update(); } });
    update();
  });

  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      sliders.forEach(function (s) { s.reset(); });
    }, 120);
  });
  window.addEventListener('load', function () {
    sliders.forEach(function (s) { s.reset(); });
  });

  // ==========================================================================
  // Billing Period Switch (Web Hosting Packages)
  // ==========================================================================
  var periodSwitch = document.querySelector('[data-period-switch]');
  var cycles = { 1: 'annually', 2: 'biennially', 3: 'triennially' };

  var money = function (value) {
    return value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  };

  var applyPeriod = function (years) {
    document.querySelectorAll('[data-plan]').forEach(function (card) {
      var base = parseFloat(card.getAttribute('data-price-1y'));
      var perYear = parseFloat(card.getAttribute('data-price-' + years + 'y')) || base;
      var amount = card.querySelector('[data-amount]');
      var billed = card.querySelector('[data-billed]');
      var order = card.querySelector('[data-order]');
      var priceBox = card.querySelector('.plan-price');

      if (amount) amount.textContent = money(perYear);

      if (billed) {
        var total = perYear * years;
        if (years === 1) {
          billed.textContent = 'Billed $' + money(total) + ' every year';
        } else {
          var save = Math.round((1 - perYear / base) * 100);
          billed.innerHTML = 'Billed $' + money(total) + ' every ' + years + ' years' +
            (save > 0 ? ' · <b>Save ' + save + '%</b>' : '');
        }
      }

      if (order) {
        var url = order.getAttribute('data-order-base') || order.getAttribute('href');
        order.setAttribute('data-order-base', url);
        order.setAttribute('href', url + (url.indexOf('?') > -1 ? '&' : '?') + 'billingcycle=' + cycles[years]);
      }

      if (priceBox && !reduceMotion) {
        priceBox.classList.remove('is-changing');
        void priceBox.offsetWidth; // restart animation
        priceBox.classList.add('is-changing');
      }
    });
  };

  if (periodSwitch) {
    var periodBtns = periodSwitch.querySelectorAll('[data-period]');
    periodBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        periodBtns.forEach(function (b) {
          var active = b === btn;
          b.classList.toggle('is-active', active);
          b.setAttribute('aria-checked', String(active));
        });
        applyPeriod(parseInt(btn.getAttribute('data-period'), 10));
      });
    });
    applyPeriod(1);
  }

  // ==========================================================================
  // Tabs (Reseller / Master Reseller)
  // ==========================================================================
  document.querySelectorAll('[data-tabs]').forEach(function (tablist) {
    var tabBtns = Array.prototype.slice.call(tablist.querySelectorAll('[role="tab"]'));

    var activate = function (btn, focus) {
      tabBtns.forEach(function (b) {
        var active = b === btn;
        var panel = document.getElementById(b.getAttribute('aria-controls'));
        b.classList.toggle('is-active', active);
        b.setAttribute('aria-selected', String(active));
        b.tabIndex = active ? 0 : -1;
        if (panel) {
          panel.hidden = !active;
          panel.classList.toggle('is-active', active);
        }
      });
      if (focus) btn.focus();
      // Sliders inside a newly shown panel need measuring
      sliders.forEach(function (s) { s.reset(); });
    };

    tabBtns.forEach(function (btn, i) {
      btn.addEventListener('click', function () { activate(btn); });
      btn.addEventListener('keydown', function (e) {
        var dir = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
        if (!dir) return;
        e.preventDefault();
        activate(tabBtns[(i + dir + tabBtns.length) % tabBtns.length], true);
      });
    });

    // Links elsewhere on the page can open a specific tab
    document.querySelectorAll('[data-open-tab]').forEach(function (link) {
      link.addEventListener('click', function () {
        var target = document.getElementById(link.getAttribute('data-open-tab'));
        if (target && tabBtns.indexOf(target) > -1) activate(target);
      });
    });
  });

  // ==========================================================================
  // Scroll Reveal & Stat Counters
  // ==========================================================================
  var revealEls = document.querySelectorAll('.reveal');

  var finishReveal = function (el) {
    el.classList.add('is-visible');
    // Remove reveal classes afterwards so hover transforms work normally
    setTimeout(function () {
      el.classList.remove('reveal', 'is-visible');
      el.style.transitionDelay = '';
    }, 900 + (parseInt(el.style.transitionDelay, 10) || 0));
  };

  if ('IntersectionObserver' in window && !reduceMotion) {
    // Stagger siblings inside the same grid
    revealEls.forEach(function (el) {
      var siblings = Array.prototype.filter.call(el.parentElement.children, function (c) {
        return c.classList.contains('reveal');
      });
      var index = siblings.indexOf(el);
      if (index > 0) el.style.transitionDelay = (index % 4) * 90 + 'ms';
    });

    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          finishReveal(entry.target);
          revealObserver.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

    revealEls.forEach(function (el) { revealObserver.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.remove('reveal'); });
  }

  var counters = document.querySelectorAll('[data-count]');
  var runCounter = function (el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    var suffix = el.getAttribute('data-suffix') || '';
    var duration = 1600;
    var start = null;
    var frame = function (ts) {
      if (!start) start = ts;
      var p = Math.min((ts - start) / duration, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(target * eased).toLocaleString('en-US') + suffix;
      if (p < 1) window.requestAnimationFrame(frame);
    };
    window.requestAnimationFrame(frame);
  };

  if (counters.length && 'IntersectionObserver' in window && !reduceMotion) {
    var countObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          runCounter(entry.target);
          countObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    counters.forEach(function (el) { countObserver.observe(el); });
  }

  // Floating dock: back-to-top button
  var dockTops = document.querySelectorAll('.dock-top');
  if (dockTops.length) {
    var checkDockScroll = function () {
      var scrolled = (window.pageYOffset || document.documentElement.scrollTop || 0) > 350;
      dockTops.forEach(function (btn) {
        btn.classList.toggle('is-visible', scrolled);
        btn.tabIndex = scrolled ? 0 : -1;
        btn.setAttribute('aria-hidden', String(!scrolled));
      });
    };
    window.addEventListener('scroll', checkDockScroll, { passive: true });
    checkDockScroll();

    var scrollToTop = function (e) {
      if (e) e.preventDefault();
      try {
        window.scrollTo({
          top: 0,
          left: 0,
          behavior: reduceMotion ? 'auto' : 'smooth'
        });
      } catch (err) {
        window.scrollTo(0, 0);
      }
    };

    dockTops.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        scrollToTop(e);
        btn.blur();
      });
    });

    // Also support any global anchor link pointing to #top
    document.querySelectorAll('a[href="#top"]').forEach(function (link) {
      if (!link.classList.contains('dock-top')) {
        link.addEventListener('click', scrollToTop);
      }
    });
  }
})();
