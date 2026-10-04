(function () {
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var header = document.getElementById('site-header');
    var toggle = document.querySelector('.menu-toggle');
    var menu = document.getElementById('mobile-menu');

    // Header turns solid once the page is scrolled past the top of the hero.
    function updateHeader() {
        header.classList.toggle('is-solid', window.scrollY > 60);
    }
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });

    // Mobile menu.
    function setMenu(open) {
        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        document.body.classList.toggle('menu-open', open);
    }
    toggle.addEventListener('click', function () {
        setMenu(toggle.getAttribute('aria-expanded') !== 'true');
    });
    menu.addEventListener('click', function (event) {
        if (event.target.closest('a')) setMenu(false);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') setMenu(false);
    });

    // Reveal sections as they scroll into view.
    var reveals = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { observer.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('is-visible'); });
    }

    // Gallery slider: one photo centred, arrow buttons step one photo, click-and-drag.
    var slider = document.getElementById('slider');
    if (slider) {
        var gallerySlides = slider.querySelectorAll('.slide');
        var centreOf = function (slide) {
            return slide.offsetLeft - (slider.clientWidth - slide.offsetWidth) / 2;
        };
        var centredIndex = function () {
            var best = 0;
            gallerySlides.forEach(function (slide, i) {
                if (Math.abs(centreOf(slide) - slider.scrollLeft) < Math.abs(centreOf(gallerySlides[best]) - slider.scrollLeft)) best = i;
            });
            return best;
        };
        var showSlide = function (index, smooth) {
            var slide = gallerySlides[Math.max(0, Math.min(index, gallerySlides.length - 1))];
            if (slide) slider.scrollTo({ left: centreOf(slide), behavior: smooth && !reduceMotion ? 'smooth' : 'auto' });
        };
        // Start on the second photo so there is one peeking in on each side.
        var startSlider = function () { showSlide(1, false); };
        if (document.readyState === 'complete') startSlider(); else window.addEventListener('load', startSlider);

        document.querySelectorAll('[data-slide]').forEach(function (button) {
            button.addEventListener('click', function () {
                showSlide(centredIndex() + (button.dataset.slide === 'next' ? 1 : -1), true);
            });
        });

        var dragging = false, startX = 0, startScroll = 0;
        slider.addEventListener('pointerdown', function (event) {
            if (event.pointerType !== 'mouse') return;
            dragging = true;
            startX = event.clientX;
            startScroll = slider.scrollLeft;
            slider.classList.add('is-dragging');
        });
        window.addEventListener('pointermove', function (event) {
            if (dragging) slider.scrollLeft = startScroll - (event.clientX - startX);
        });
        window.addEventListener('pointerup', function () {
            dragging = false;
            slider.classList.remove('is-dragging');
        });
    }

    // Reviews slider: a page of reviews at a time (three on desktop, fewer on
    // smaller screens), with arrows, dots and gentle autoplay.
    var reviewSlider = document.getElementById('review-slider');
    if (reviewSlider) {
        var track = reviewSlider.querySelector('.review-track');
        var slides = track.querySelectorAll('.review-slide');
        var dotsWrap = reviewSlider.querySelector('.review-dots');
        var arrows = reviewSlider.querySelectorAll('[data-review]');
        var dots = [];
        var timer = null;

        // How many reviews fit in view, read from the CSS layout.
        var perPage = function () {
            var step = slides.length > 1 ? slides[1].offsetLeft - slides[0].offsetLeft : track.clientWidth;
            return Math.max(1, Math.round(track.clientWidth / step));
        };
        var pageCount = function () {
            return Math.ceil(slides.length / perPage());
        };
        var current = function () {
            var maxScroll = track.scrollWidth - track.clientWidth;
            if (track.scrollLeft >= maxScroll - 2) return pageCount() - 1;
            var step = slides.length > 1 ? slides[1].offsetLeft - slides[0].offsetLeft : track.clientWidth;
            return Math.round(track.scrollLeft / (step * perPage()));
        };
        var goTo = function (page) {
            var pages = pageCount();
            var target = (page + pages) % pages;
            var slide = slides[target * perPage()];
            track.scrollTo({ left: slide.offsetLeft - slides[0].offsetLeft, behavior: reduceMotion ? 'auto' : 'smooth' });
        };
        var refresh = function () {
            var active = current();
            dots.forEach(function (dot, i) { dot.classList.toggle('is-active', i === active); });
        };
        var stop = function () {
            clearInterval(timer);
            timer = null;
        };
        var start = function () {
            if (reduceMotion || pageCount() < 2 || timer) return;
            timer = setInterval(function () { goTo(current() + 1); }, 6000);
        };

        // One dot per page; rebuilt when the page size changes on resize.
        var buildDots = function () {
            var pages = pageCount();
            arrows.forEach(function (arrow) { arrow.hidden = pages < 2; });
            if (dots.length === pages || (pages < 2 && !dots.length)) return;
            dotsWrap.innerHTML = '';
            dots = [];
            if (pages < 2) return;
            for (var i = 0; i < pages; i++) {
                var dot = document.createElement('button');
                dot.type = 'button';
                dot.tabIndex = -1;
                dot.addEventListener('click', goTo.bind(null, i));
                dotsWrap.appendChild(dot);
                dots.push(dot);
            }
        };
        buildDots();
        window.addEventListener('resize', function () {
            buildDots();
            refresh();
        });

        arrows.forEach(function (arrow) {
            arrow.addEventListener('click', function () {
                goTo(current() + (arrow.dataset.review === 'next' ? 1 : -1));
            });
        });
        track.addEventListener('scroll', refresh, { passive: true });
        ['mouseenter', 'focusin', 'touchstart'].forEach(function (name) {
            reviewSlider.addEventListener(name, stop, { passive: true });
        });
        ['mouseleave', 'focusout'].forEach(function (name) {
            reviewSlider.addEventListener(name, start);
        });

        refresh();
        start();
    }

    // "Copy link" button on blog posts.
    var copyButton = document.querySelector('[data-copy-link]');
    if (copyButton) {
        copyButton.addEventListener('click', function () {
            var link = copyButton.getAttribute('data-copy-link');
            var status = document.querySelector('.article-share-status');
            var done = function () {
                if (status) {
                    status.hidden = false;
                    setTimeout(function () { status.hidden = true; }, 2500);
                }
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(link).then(done, function () { window.prompt('Copy this link', link); });
            } else {
                window.prompt('Copy this link', link);
            }
        });
    }

    // Stop double submissions of the enquiry form.
    var form = document.getElementById('contact-form');
    if (form) {
        form.addEventListener('submit', function () {
            var button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
            }
        });
    }
})();
