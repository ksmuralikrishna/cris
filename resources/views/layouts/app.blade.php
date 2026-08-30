<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <link rel="manifest" href="/manifest.json">

    {{-- Prevent zooming and pinching on tablet --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">

    {{-- Tell browser to run as standalone app (no chrome UI when added to home screen) --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-fullscreen">

    <title>@yield('title', 'Mall Registration')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981',
                    }
                }
            }
        }
    </script>


    <style>
        /* ── Prevent text selection everywhere except inputs ── */
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;  /* disables iOS long-press menu */
            -webkit-tap-highlight-color: transparent; /* removes tap flash on Android */
        }
        input, textarea, select {
            -webkit-user-select: text;
            -moz-user-select: text;
            user-select: text;
        }

        /* ── Prevent pull-to-refresh and overscroll bounce ── */
        html, body {
            overscroll-behavior: none;
            overflow: hidden;           /* no scroll on the body itself */
            height: 100%;
            width: 100%;
        }

        /* ── Allow scrolling only inside the main content area ── */
        main {
            overflow-y: auto;
            height: 100vh;
            width: 100%;
        }

        /* ── Disable drag on images and links ── */
        img, a {
            -webkit-user-drag: none;
            user-drag: none;
        }

        /* ── Hide scrollbar visually but keep it functional ── */
        main::-webkit-scrollbar { display: none; }
        main { -ms-overflow-style: none; scrollbar-width: none; }

        /* ── Inactivity warning overlay ── */
        #kiosk-warning {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        #kiosk-warning.show {
            display: flex;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800 antialiased font-sans flex flex-col min-h-screen">

    {{-- ── Inactivity warning overlay ──────────────────────────────────────── --}}
    <div id="kiosk-warning" role="alertdialog" aria-modal="true" aria-labelledby="warning-title">
        <div class="bg-white rounded-2xl shadow-2xl p-10 mx-6 max-w-md w-full text-center">
            <div class="text-6xl mb-4">⏱️</div>
            <h2 id="warning-title" class="text-2xl font-bold text-gray-800 mb-2">Are you still there?</h2>
            <p class="text-gray-500 mb-6">This session will reset in</p>
            <div id="kiosk-countdown" class="text-6xl font-bold text-red-500 mb-8">30</div>
            <button
                onclick="kioskResetTimer()"
                class="bg-primary text-white font-bold py-4 px-10 rounded-xl text-xl hover:bg-emerald-600 transition w-full">
                Continue Registration
            </button>
        </div>
    </div>

    {{-- ── Page content ─────────────────────────────────────────────────────── --}}
    <main class="flex-grow flex items-center justify-center p-4">
        @yield('content')
    </main>

    {{-- ── Kiosk lockdown script ────────────────────────────────────────────── --}}
    <script>
    (function () {

        // ── Configuration ─────────────────────────────────────────────────────
        const INACTIVITY_LIMIT   = 60;   // seconds idle before warning appears
        const COUNTDOWN_DURATION = 30;   // seconds customer has to respond
        const RESET_URL          = '/register'; // where to go on timeout

        // ── State ─────────────────────────────────────────────────────────────
        let inactivityTimer  = null;
        let countdownTimer   = null;
        let countdownSeconds = COUNTDOWN_DURATION;
        let warningVisible   = false;

        // ── Block right-click context menu ────────────────────────────────────
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        // ── Block keyboard shortcuts ──────────────────────────────────────────
        document.addEventListener('keydown', function (e) {
            const blocked = [
                e.key === 'F5',                          // refresh
                e.key === 'F11',                         // fullscreen toggle
                e.key === 'F12',                         // dev tools
                e.ctrlKey && e.key === 'r',              // refresh
                e.ctrlKey && e.key === 'R',              // refresh
                e.ctrlKey && e.key === 'w',              // close tab
                e.ctrlKey && e.key === 't',              // new tab
                e.ctrlKey && e.key === 'n',              // new window
                e.ctrlKey && e.key === 'l',              // focus address bar
                e.ctrlKey && e.key === 'u',              // view source
                e.ctrlKey && e.shiftKey && e.key === 'i', // dev tools
                e.ctrlKey && e.shiftKey && e.key === 'j', // dev tools
                e.ctrlKey && e.shiftKey && e.key === 'c', // inspect element
                e.altKey  && e.key === 'F4',             // close window
                e.altKey  && e.key === 'ArrowLeft',      // browser back
                e.altKey  && e.key === 'ArrowRight',     // browser forward
            ];

            if (blocked.some(Boolean)) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true); // capture phase so it fires before anything else

        // ── Block swipe-back / swipe-forward edge gestures ────────────────────
        let touchStartX = 0;
        let touchStartY = 0;

        document.addEventListener('touchstart', function (e) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;

            // Block touches originating from the very edge of the screen
            // (Android swipe-back gesture zone)
            if (touchStartX < 20 || touchStartX > window.innerWidth - 20) {
                e.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('touchmove', function (e) {
            const dx = e.touches[0].clientX - touchStartX;
            const dy = e.touches[0].clientY - touchStartY;

            // Block mostly-horizontal swipes (back/forward gesture)
            if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 30) {
                e.preventDefault();
            }
        }, { passive: false });

        // ── Block browser history navigation ──────────────────────────────────
        // Push a dummy state so the back button has nowhere to go
        history.pushState(null, '', window.location.href);
        window.addEventListener('popstate', function () {
            history.pushState(null, '', window.location.href);
        });

        // ── Inactivity timer ──────────────────────────────────────────────────
        function kioskStartInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(showWarning, INACTIVITY_LIMIT * 1000);
        }

        function showWarning() {
            warningVisible   = true;
            countdownSeconds = COUNTDOWN_DURATION;
            document.getElementById('kiosk-countdown').textContent = countdownSeconds;
            document.getElementById('kiosk-warning').classList.add('show');
            runCountdown();
        }

        function runCountdown() {
            clearInterval(countdownTimer);
            countdownTimer = setInterval(function () {
                countdownSeconds--;
                document.getElementById('kiosk-countdown').textContent = countdownSeconds;

                if (countdownSeconds <= 0) {
                    clearInterval(countdownTimer);
                    window.location.href = RESET_URL;
                }
            }, 1000);
        }

        // Exposed globally so the "Continue" button can call it
        window.kioskResetTimer = function () {
            clearInterval(countdownTimer);
            warningVisible = false;
            document.getElementById('kiosk-warning').classList.remove('show');
            kioskStartInactivityTimer();
        };

        // Reset timer on any customer interaction
        const activityEvents = ['click', 'touchstart', 'touchend', 'keydown', 'mousemove', 'scroll'];
        activityEvents.forEach(function (event) {
            document.addEventListener(event, function () {
                if (!warningVisible) {
                    kioskStartInactivityTimer();
                }
            }, { passive: true });
        });

        // ── Request fullscreen on first tap (browsers require a user gesture) ─
        let fullscreenRequested = false;
        document.addEventListener('touchstart', function requestFullscreen() {
            if (fullscreenRequested) return;
            fullscreenRequested = true;

            const el = document.documentElement;
            if (el.requestFullscreen) {
                el.requestFullscreen().catch(() => {});
            } else if (el.webkitRequestFullscreen) {
                el.webkitRequestFullscreen();
            }
        }, { once: false, passive: true });

        // If customer somehow exits fullscreen, re-request it on next tap
        document.addEventListener('fullscreenchange', function () {
            if (!document.fullscreenElement) {
                fullscreenRequested = false;
            }
        });

        // ── Start the inactivity timer on load ────────────────────────────────
        kioskStartInactivityTimer();

    })();
    </script>

</body>
</html>