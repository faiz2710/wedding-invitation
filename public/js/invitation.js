/* =====================================================================
   ALIF & INDY — INVITATION SCRIPT (vanilla JS)
   Opening amplop → hero ayat → section undangan. Setiap fitur
   dibungkus safe() agar satu error tidak merusak fitur lain.
   ===================================================================== */

(function () {
    "use strict";

    // Tanda untuk jaring pengaman di Blade (jika file ini gagal dimuat).
    window.__invitationReady = true;

    var $ = function (selector, context) {
        return (context || document).querySelector(selector);
    };

    var $$ = function (selector, context) {
        return Array.prototype.slice.call((context || document).querySelectorAll(selector));
    };

    var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");

    function safe(fn) {
        try {
            fn();
        } catch (error) {
            if (window.console) {
                console.error("[invitation]", error);
            }
        }
    }

    /* -----------------------------------------------------------------
       1. PERSONALISASI NAMA TAMU
       Contoh: /invitation?to=Zia%20Kirana
       ----------------------------------------------------------------- */

    function setupGuest() {
        var params = new URLSearchParams(window.location.search);
        var guestName = (params.get("to") || "").trim().slice(0, 100);

        if (!guestName) {
            return;
        }

        var openingGuest = $("#openingGuest");

        if (openingGuest) {
            openingGuest.textContent = guestName;
        }
    }

    /* -----------------------------------------------------------------
       2. MUSIK (hanya dimulai setelah tindakan pengguna)
       Aktif hanya jika $musicSrc diisi di Blade.
       ----------------------------------------------------------------- */

    var audio = null;

    function setupMusic() {
        audio = $("#bgMusic");
        var toggle = $("#musicToggle");

        if (audio) {
            audio.volume = 0.6;
        }

        if (!audio || !toggle) {
            return;
        }

        function sync() {
            var playing = !audio.paused;

            toggle.setAttribute("aria-pressed", playing ? "true" : "false");
            toggle.setAttribute("aria-label", playing ? "Jeda musik" : "Putar musik");
        }

        toggle.addEventListener("click", function () {
            if (audio.paused) {
                startMusic();
            } else {
                audio.pause();
            }

            sync();
        });

        audio.addEventListener("play", sync);
        audio.addEventListener("pause", sync);

        sync();
    }

    function startMusic() {
        if (!audio) {
            return;
        }

        var attempt = audio.play();
        if (attempt && attempt.catch) {
            attempt.catch(function () {});
        }
    }

    /* -----------------------------------------------------------------
       3. OPENING AMPLOP
       - Dilewati otomatis bila halaman dimuat ulang setelah RSVP.
       - Tombol "Lewati animasi" dan tombol Escape langsung membuka.
       ----------------------------------------------------------------- */

    function setupOpening() {
        var body = document.body;
        var opening = $("#opening");
        var main = $("#main");
        var rsvpState = body.getAttribute("data-rsvp-state") || "none";
        var lockTargets = [main].filter(Boolean);

        function setInert(value) {
            lockTargets.forEach(function (element) {
                element.inert = value;
            });
        }

        function enterSite() {
            body.classList.remove("is-locked");
            setInert(false);
            body.classList.add("is-entered");

            var hash = window.location.hash;
            var target = hash && hash.length > 1 ? document.getElementById(hash.slice(1)) : null;

            if (target) {
                target.scrollIntoView({ block: "start" });
            } else if (rsvpState === "none") {
                window.scrollTo(0, 0);
                var title = $("#heroTitle");
                if (title) {
                    title.focus({ preventScroll: true });
                }
            }
        }

        // Tanpa opening, atau kembali dari pengiriman RSVP: langsung masuk.
        if (!opening || rsvpState !== "none") {
            if (opening) {
                opening.classList.add("is-gone");
            }
            enterSite();
            return;
        }

        var openButton = $("#openInvitation");
        var skipButton = $("#skipOpening");
        var seal = $("#sealButton");
        var envelope = $("#envelope");
        var started = false;

        body.classList.add("is-locked");
        setInert(true);

        if (openButton) {
            openButton.focus({ preventScroll: true });
        }

        function leave() {
            opening.classList.add("is-leaving");
            enterSite();

            window.setTimeout(function () {
                opening.classList.add("is-gone");
            }, 900);
        }

        function begin(animated) {
            if (started) {
                return;
            }

            started = true;
            startMusic();

            if (!animated || reducedMotion.matches || !envelope) {
                leave();
                return;
            }

            opening.classList.add("is-opening");
            envelope.classList.add("is-open");

            window.setTimeout(leave, 1750);
        }

        if (openButton) {
            openButton.addEventListener("click", function () {
                begin(true);
            });
        }

        if (seal) {
            seal.addEventListener("click", function () {
                begin(true);
            });
        }

        if (skipButton) {
            skipButton.addEventListener("click", function () {
                begin(false);
            });
        }

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape" && !started) {
                begin(false);
            }
        });
    }

    /* -----------------------------------------------------------------
       4. REVEAL SAAT SCROLL (IntersectionObserver, sekali per elemen)
       ----------------------------------------------------------------- */

    function setupReveal() {
        var items = $$("[data-reveal]");

        if (!items.length) {
            return;
        }

        items.forEach(function (element) {
            var delay = element.getAttribute("data-delay");

            if (delay) {
                element.style.setProperty("--d", delay + "ms");
            }
        });

        if (reducedMotion.matches || !("IntersectionObserver" in window)) {
            items.forEach(function (element) {
                element.classList.add("is-visible");
            });

            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.15, rootMargin: "0px 0px -6% 0px" }
        );

        items.forEach(function (element) {
            observer.observe(element);
        });
    }

    /* -----------------------------------------------------------------
       5. NAVIGASI: ANCHOR HALUS + PENANDA SECTION AKTIF
       ----------------------------------------------------------------- */

    function setupAnchors() {
        $$('a[href^="#"]').forEach(function (link) {
            link.addEventListener("click", function (event) {
                var targetId = link.getAttribute("href");

                if (!targetId || targetId === "#") {
                    return;
                }

                var target = document.querySelector(targetId);

                if (!target) {
                    return;
                }

                event.preventDefault();

                target.scrollIntoView({
                    behavior: reducedMotion.matches ? "auto" : "smooth",
                    block: "start"
                });

                if (window.history.replaceState) {
                    window.history.replaceState(null, "", targetId);
                }
            });
        });
    }

    function setupDockActive() {
        var links = $$("[data-dock]");

        if (!links.length || !("IntersectionObserver" in window)) {
            return;
        }

        var map = {};

        links.forEach(function (link) {
            map[link.getAttribute("data-dock")] = link;
        });

        function activate(id) {
            links.forEach(function (link) {
                if (link.getAttribute("data-dock") === id) {
                    link.setAttribute("aria-current", "true");
                } else {
                    link.removeAttribute("aria-current");
                }
            });
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && map[entry.target.id]) {
                        activate(entry.target.id);
                    }
                });
            },
            { rootMargin: "-45% 0px -50% 0px" }
        );

        Object.keys(map).forEach(function (id) {
            var section = document.getElementById(id);

            if (section) {
                observer.observe(section);
            }
        });

        activate("ayat");
    }

    /* -----------------------------------------------------------------
       6. COUNTDOWN — target dibaca dari data-deadline di Blade
       ----------------------------------------------------------------- */

    function setupCountdown() {
        var countdown = $("#countdownTimer");

        if (!countdown) {
            return;
        }

        var deadline = new Date(countdown.getAttribute("data-deadline") || "").getTime();

        if (isNaN(deadline)) {
            return;
        }

        var days = $("#days");
        var hours = $("#hours");
        var minutes = $("#minutes");
        var seconds = $("#seconds");
        var timer = null;

        function setValue(element, value) {
            if (element && element.textContent !== value) {
                element.textContent = value;
            }
        }

        function update() {
            var difference = deadline - Date.now();

            if (difference <= 0) {
                setValue(days, "000");
                setValue(hours, "00");
                setValue(minutes, "00");
                setValue(seconds, "00");

                countdown.setAttribute("aria-label", "Hari pernikahan telah tiba");

                if (timer) {
                    window.clearInterval(timer);
                }

                return;
            }

            setValue(days, String(Math.floor(difference / 86400000)).padStart(3, "0"));
            setValue(hours, String(Math.floor((difference / 3600000) % 24)).padStart(2, "0"));
            setValue(minutes, String(Math.floor((difference / 60000) % 60)).padStart(2, "0"));
            setValue(seconds, String(Math.floor((difference / 1000) % 60)).padStart(2, "0"));
        }

        update();
        timer = window.setInterval(update, 1000);
    }

    /* -----------------------------------------------------------------
       7. SIMPAN KE KALENDER (.ics dibuat di browser dari kartu acara)
       ----------------------------------------------------------------- */

    function setupCalendar() {
        var button = $("#addToCalendar");
        var cards = $$("[data-event]");

        if (!button || !cards.length) {
            return;
        }

        function stamp(date) {
            return date.toISOString().replace(/[-:]/g, "").replace(/\.\d{3}/, "");
        }

        function escapeText(value) {
            return String(value)
                .replace(/\\/g, "\\\\")
                .replace(/([,;])/g, "\\$1")
                .replace(/\r?\n/g, "\\n");
        }

        button.addEventListener("click", function () {
            var now = stamp(new Date());

            var lines = [
                "BEGIN:VCALENDAR",
                "VERSION:2.0",
                "PRODID:-//Alif & Indy//Wedding Invitation//ID",
                "CALSCALE:GREGORIAN"
            ];

            cards.forEach(function (card) {
                var start = new Date(card.getAttribute("data-start"));
                var end = new Date(card.getAttribute("data-end"));

                if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                    return;
                }

                lines.push(
                    "BEGIN:VEVENT",
                    "UID:" + card.getAttribute("data-event") + "-" + stamp(start) + "@wedding-alif-indy",
                    "DTSTAMP:" + now,
                    "DTSTART:" + stamp(start),
                    "DTEND:" + stamp(end),
                    "SUMMARY:" + escapeText(card.getAttribute("data-title") || "Pernikahan Alif & Indy"),
                    "END:VEVENT"
                );
            });

            lines.push("END:VCALENDAR");

            var blob = new Blob([lines.join("\r\n")], { type: "text/calendar;charset=utf-8" });
            var url = URL.createObjectURL(blob);
            var link = document.createElement("a");

            link.href = url;
            link.download = "alif-indy-wedding.ics";
            document.body.appendChild(link);
            link.click();
            link.remove();

            window.setTimeout(function () {
                URL.revokeObjectURL(url);
            }, 1500);
        });
    }

    /* -----------------------------------------------------------------
        7b. SALIN NOMOR REKENING ([data-copy])
        ----------------------------------------------------------------- */

    function setupCopy() {
        var buttons = $$("[data-copy]");

        if (!buttons.length) {
            return;
        }

        buttons.forEach(function (button) {
            button.addEventListener("click", function () {
                var text = button.getAttribute("data-copy");
                var label = button.innerHTML;

                function done() {
                    button.textContent = "Tersalin!";
                    button.classList.add("is-copied");

                    window.setTimeout(function () {
                        button.innerHTML = label;
                        button.classList.remove("is-copied");
                    }, 1600);
                }

                function fallback() {
                    var input = document.createElement("textarea");

                    input.value = text;
                    input.style.position = "fixed";
                    input.style.opacity = "0";

                    document.body.appendChild(input);
                    input.select();
                    document.execCommand("copy");
                    input.remove();

                    done();
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(done, fallback);
                } else {
                    fallback();
                }
            });
        });
    }

    /* -----------------------------------------------------------------
        7c. SLIDESHOW MOMEN (#momentSlides)
        Auto berganti tiap 4,5 detik, ada tombol prev/next & dots.
        ----------------------------------------------------------------- */

    function setupSlideshow() {
        var container = $("#momentSlides");
        var prev = $("#slidesPrev");
        var next = $("#slidesNext");
        var dots = $("#slidesDots");

        if (!container || !container.children.length) {
            return;
        }

        var slides = $$(".moment-slide", container);
        var index = 0;
        var timer = null;
        var delay = 4500;

        // Titik navigasi dibuat mengikuti jumlah slide
        var dotElements = slides.map(function (_, i) {
            var dot = document.createElement("button");

            dot.type = "button";
            dot.className = "moments__dot";
            dot.setAttribute("aria-label", "Foto " + (i + 1));

            dot.addEventListener("click", function () {
                goTo(i);
            });

            dots.appendChild(dot);

            return dot;
        });

        function render() {
            slides.forEach(function (slide, i) {
                slide.classList.toggle("is-active", i === index);

                // hidden tidak dipakai agar transisi fade berjalan mulus
                slide.style.pointerEvents = i === index ? "auto" : "none";
                slide.setAttribute("aria-hidden", i === index ? "false" : "true");
            });

            dotElements.forEach(function (dot, i) {
                dot.classList.toggle("is-active", i === index);
            });
        }

        function goTo(target) {
            index = (target + slides.length) % slides.length;
            render();
            restart();
        }

        function restart() {
            if (timer) {
                window.clearInterval(timer);
            }

            if (!reducedMotion.matches) {
                timer = window.setInterval(function () {
                    goTo(index + 1);
                }, delay);
            }
        }

        if (prev) {
            prev.addEventListener("click", function () {
                goTo(index - 1);
            });
        }

        if (next) {
            next.addEventListener("click", function () {
                goTo(index + 1);
            });
        }

        // Hentikan auto-play saat pengguna sedang melihat tab lain
        document.addEventListener("visibilitychange", function () {
            if (document.hidden) {
                if (timer) {
                    window.clearInterval(timer);
                    timer = null;
                }
            } else {
                restart();
            }
        });

        render();
        restart();
    }

    /* -----------------------------------------------------------------
       8. FORM (RSVP & WISHES) — stepper, lock kuota, status kirim
       Nama field, endpoint, method, dan @csrf tidak disentuh.
       ----------------------------------------------------------------- */

    function setupForms() {
        // Stepper jumlah orang (_RSVP)
        var attendance = $("#attendance");
        var guests = $("#guests");
        var hint = $("#guestsHint");
        var stepButtons = $$(".stepper__btn");
        var defaultHint = hint ? hint.textContent : "";

        if (guests) {
            stepButtons.forEach(function (button) {
                button.addEventListener("click", function () {
                    if (guests.readOnly) {
                        return;
                    }

                    var step = parseInt(button.getAttribute("data-step"), 10) || 0;
                    var min = parseInt(guests.min, 10) || 1;
                    var max = parseInt(guests.max, 10) || 10;
                    var current = parseInt(guests.value, 10);

                    if (isNaN(current)) {
                        current = min;
                    }

                    guests.value = Math.min(max, Math.max(min, current + step));
                });
            });
        }

        // Jika tidak hadir, jumlah orang dikunci di 1 (sesuai validasi backend).
        function syncAttendance() {
            if (!attendance || !guests) {
                return;
            }

            var absent = attendance.value === "tidak_hadir";

            if (absent) {
                guests.value = "1";
            }

            guests.readOnly = absent;

            stepButtons.forEach(function (button) {
                button.disabled = absent;
            });

            if (hint) {
                hint.textContent = absent
                    ? "Cukup 1 orang untuk konfirmasi ketidakhadiran."
                    : defaultHint;
            }
        }

        if (attendance) {
            attendance.addEventListener("change", syncAttendance);
            syncAttendance();
        }

        // Umpan balik saat mengirim (untuk kedua form)
        $$(".invite-form").forEach(function (form) {
            var submit = form.querySelector(".button--submit");
            var label = submit ? submit.querySelector(".button__label") : null;

            if (!submit) {
                return;
            }

            var originalLabel = submit.getAttribute("data-label") || (label ? label.textContent : "");

            form.addEventListener("submit", function () {
                if (!form.checkValidity()) {
                    return;
                }

                submit.classList.add("is-loading");
                submit.setAttribute("aria-busy", "true");

                if (label) {
                    label.textContent = "Mengirim...";
                }
            });

            // Tombol "Kembali" di browser (bfcache) tidak boleh
            // menyisakan status loading.
            window.addEventListener("pageshow", function (event) {
                if (event.persisted) {
                    submit.classList.remove("is-loading");
                    submit.removeAttribute("aria-busy");

                    if (label) {
                        label.textContent = originalLabel;
                    }
                }
            });
        });
    }

    /* -----------------------------------------------------------------
       9. FEEDBACK SETELAH KIRIM — bawa tamu ke hasilnya
       ----------------------------------------------------------------- */

function setupFormFeedback() {
        var pageState = document.body.getAttribute("data-rsvp-state");

        if (pageState !== "rsvp" && pageState !== "wishes") {
            return;
        }

        var feedback = $('[data-feedback="' + pageState + '"]');
        var target = feedback || document.getElementById(pageState);

        if (!target) {
            return;
        }

        target.scrollIntoView({ block: feedback ? "center" : "start" });

        if (feedback) {
            feedback.focus({ preventScroll: true });
        }
    }

    /* -----------------------------------------------------------------
       JALANKAN
       ----------------------------------------------------------------- */

    function init() {
        safe(setupGuest);
        safe(setupMusic);
        safe(setupOpening);
        safe(setupReveal);
        safe(setupAnchors);
        safe(setupDockActive);
        safe(setupCountdown);
        safe(setupCalendar);
        safe(setupCopy);
        safe(setupSlideshow);
        safe(setupForms);
        safe(setupFormFeedback);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
