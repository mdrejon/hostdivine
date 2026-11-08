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
          }
        });
        parentItem.classList.toggle('is-open');
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
  // Domain Search & Pick Up Area Interactions
  // ==========================================================================
  const domainForm = document.getElementById('domainSearchForm');
  const domainInput = document.getElementById('domainSearchInput');
  const tldSelect = document.getElementById('domainTldSelect');
  const tldCards = document.querySelectorAll('.tld-price-card');
  const tldTags = document.querySelectorAll('.tld-tag');
  const viewMoreBtn = document.getElementById('viewMoreTldsBtn');
  const extraTlds = document.getElementById('extraTlds');

  // Helper to sync TLD across select dropdown and cards
  function setSelectedTld(tld) {
    if (!tld) return;
    const cleanTld = tld.startsWith('.') ? tld : '.' + tld;

    // Update select dropdown if option exists, or add it
    if (tldSelect) {
      let optionExists = false;
      for (let i = 0; i < tldSelect.options.length; i++) {
        if (tldSelect.options[i].value.toLowerCase() === cleanTld.toLowerCase()) {
          tldSelect.selectedIndex = i;
          optionExists = true;
          break;
        }
      }
      if (!optionExists) {
        const newOpt = new Option(cleanTld, cleanTld, true, true);
        tldSelect.add(newOpt);
      }
    }

    // Update active state on price cards
    tldCards.forEach(function (card) {
      const cardTld = card.getAttribute('data-tld');
      card.classList.toggle('active', cardTld && cardTld.toLowerCase() === cleanTld.toLowerCase());
    });

    // Update active highlight on tags
    tldTags.forEach(function (tag) {
      const tagTld = tag.getAttribute('data-tld');
      tag.classList.toggle('selected', tagTld && tagTld.toLowerCase() === cleanTld.toLowerCase());
    });

    // Focus input so user can continue typing
    if (domainInput) {
      domainInput.focus();
    }
  }

  // Handle TLD Price Cards Click (Image 2 Feature)
  tldCards.forEach(function (card) {
    card.addEventListener('click', function () {
      const tld = card.getAttribute('data-tld');
      setSelectedTld(tld);
    });
  });

  // Handle Extension Tags Click (Image 1 Feature)
  tldTags.forEach(function (tag) {
    tag.addEventListener('click', function () {
      const tld = tag.getAttribute('data-tld');
      setSelectedTld(tld);
    });
  });

  // Handle Dropdown Change
  if (tldSelect) {
    tldSelect.addEventListener('change', function () {
      setSelectedTld(tldSelect.value);
    });
  }

  // Handle "View More" Toggle for Extra TLD Tags (Image 1 Feature)
  if (viewMoreBtn && extraTlds) {
    viewMoreBtn.addEventListener('click', function () {
      const isHidden = extraTlds.hasAttribute('hidden');
      if (isHidden) {
        extraTlds.removeAttribute('hidden');
        viewMoreBtn.setAttribute('aria-expanded', 'true');
        viewMoreBtn.innerHTML = '<span>View Less</span> <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>';
      } else {
        extraTlds.setAttribute('hidden', '');
        viewMoreBtn.setAttribute('aria-expanded', 'false');
        viewMoreBtn.innerHTML = '<span>View More</span> <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>';
      }
    });
  }

  // Domain Search Form Submission Handler
  if (domainForm && domainInput) {
    domainForm.addEventListener('submit', function (e) {
      e.preventDefault();
      let query = domainInput.value.trim().toLowerCase();
      if (!query) {
        domainInput.focus();
        return;
      }

      // Strip http://, https://, www., and trailing slashes
      query = query.replace(/^https?:\/\//i, '').replace(/^www\./i, '').replace(/\/+$/, '');

      // Check if user already typed an extension (e.g. example.com or example.co.uk)
      const selectedTld = tldSelect ? tldSelect.value : '.com';
      const hasExtension = /\.[a-z0-9\-]{2,}$/i.test(query);

      let finalDomain = query;
      if (!hasExtension) {
        finalDomain = query + (selectedTld.startsWith('.') ? selectedTld : '.' + selectedTld);
      }

      const registerUrl = 'https://robot.hostdivine.com/cart.php?a=add&domain=register&query=' + encodeURIComponent(finalDomain);
      window.location.href = registerUrl;
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

  // Floating dock: back-to-top button visibility
  var dockTop = document.querySelector('.dock-top');
  if (dockTop) {
    var checkDockScroll = function () {
      dockTop.classList.toggle('is-visible', window.scrollY > 350);
    };
    window.addEventListener('scroll', checkDockScroll, { passive: true });
    checkDockScroll();
  }
})();
