// IIFE to avoid global scope pollution
(function () {
    // Initialize tracking variables
    let state = {
        totalTimeSpent: 0,
        lastActiveTime: Date.now(),
        maxScrollDepth: 0,
        isCurrentlyHidden: false,
        hasBeaconSupport: 'sendBeacon' in navigator,
        // hasBeaconSupport: false,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        isUnloading: false
    };

    console.log(state)

    // Throttle function to limit execution frequency
    function throttle(func, limit) {
        let inThrottle;
        return function (...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Calculate scroll depth
    function calculateScrollDepth() {
        const documentHeight = Math.max(
            document.documentElement.scrollHeight,
            document.documentElement.offsetHeight,
            document.documentElement.clientHeight
        );
        const windowHeight = window.innerHeight;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const trackLength = documentHeight - windowHeight;
        const scrollPercentage = Math.floor((scrollTop / trackLength) * 100);

        state.maxScrollDepth = Math.max(state.maxScrollDepth, Math.min(scrollPercentage, 100));
    }

    // Update time tracking when visibility changes
    function handleVisibilityChange() {
        if (document.visibilityState === 'hidden') {
            state.isCurrentlyHidden = true;
            state.totalTimeSpent += (Date.now() - state.lastActiveTime) / 1000;
        } else {
            state.isCurrentlyHidden = false;
            state.lastActiveTime = Date.now();
        }
    }

    // Send analytics data
    function sendAnalytics() {
        // Update final time if page is visible
        if (!state.isCurrentlyHidden) {
            state.totalTimeSpent += (Date.now() - state.lastActiveTime) / 1000;
            state.lastActiveTime = Date.now();
        }

        const analyticsData = {
            url: window.location.href,
            time_on_page: Math.round(state.totalTimeSpent),
            scroll_depth: state.maxScrollDepth,
            timestamp: Date.now(),
            referrer: document.referrer,
            viewport_width: window.innerWidth,
            viewport_height: window.innerHeight,
            is_final: true
        };

        const blob = new Blob([JSON.stringify(analyticsData)], {
            type: 'application/json'
        });

        // Use sendBeacon if available
        if (state.hasBeaconSupport) {
            navigator.sendBeacon('/api/an/store', blob);
            return;
        }

        // Fallback when sendBeacon isn't available
        try {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/api/an/store', false); // Synchronous
            xhr.setRequestHeader('Content-Type', 'application/json');
            if (state.csrfToken) {
                xhr.setRequestHeader('X-CSRF-Token', state.csrfToken);
            }
            xhr.send(JSON.stringify(analyticsData));
        } catch (e) {
            console.error('Failed to send final analytics:', e);
        }
    }

    // Handle page unload
    function handleUnload() {
        console.log('sending')
        if (state.isUnloading) return;
        state.isUnloading = true;
        sendAnalytics();
    }

    // Event listeners
    document.addEventListener('visibilitychange', handleVisibilityChange, { passive: true });
    window.addEventListener('scroll', throttle(calculateScrollDepth, 100), { passive: true });
    window.addEventListener('resize', throttle(calculateScrollDepth, 100), { passive: true });

    // Multiple unload handlers for better cross-browser support
    window.addEventListener('pagehide', handleUnload);
    window.addEventListener('beforeunload', handleUnload);
    window.addEventListener('unload', handleUnload);

    // Initial scroll depth calculation
    calculateScrollDepth();
})();