(function() {
  let e = {
    totalTimeSpent: 0,
    lastActiveTime: Date.now(),
    maxScrollDepth: 0,
    isCurrentlyHidden: !1,
    hasBeaconSupport: "sendBeacon" in navigator,
    // hasBeaconSupport: false,
    _token: document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
    isUnloading: !1
  };
  function r(n, i) {
    let t;
    return function(...l) {
      t || (n.apply(this, l), t = !0, setTimeout(() => t = !1, i));
    };
  }
  function o() {
    const n = Math.max(
      document.documentElement.scrollHeight,
      document.documentElement.offsetHeight,
      document.documentElement.clientHeight
    ), i = window.innerHeight, t = window.pageYOffset || document.documentElement.scrollTop, l = n - i, d = Math.floor(t / l * 100);
    e.maxScrollDepth = Math.max(e.maxScrollDepth, Math.min(d, 100));
  }
  function s() {
    document.visibilityState === "hidden" ? (e.isCurrentlyHidden = !0, e.totalTimeSpent += (Date.now() - e.lastActiveTime) / 1e3) : (e.isCurrentlyHidden = !1, e.lastActiveTime = Date.now());
  }
  function c() {
    e.isCurrentlyHidden || (e.totalTimeSpent += (Date.now() - e.lastActiveTime) / 1e3, e.lastActiveTime = Date.now());
    const n = {
      url: window.location.href,
      time_on_page: Math.round(e.totalTimeSpent),
      scroll_depth: e.maxScrollDepth,
      timestamp: Date.now(),
      referrer: document.referrer,
      viewport_width: window.innerWidth,
      viewport_height: window.innerHeight,
      is_final: !0
    }, i = new Blob([JSON.stringify(n)], {
      type: "application/json"
    });
    if (e.hasBeaconSupport) {
      navigator.sendBeacon("/api/an", i);
      return;
    }
    try {
      const t = new XMLHttpRequest();
      t.open("POST", "/api/an", !1), t.setRequestHeader("Content-Type", "application/json"), t.send(JSON.stringify(n));
    } catch (t) {
      console.error("Failed to send final analytics:", t);
    }
  }
  function a() {
    e.isUnloading || (e.isUnloading = !0, c());
  }
  document.addEventListener("visibilitychange", s, { passive: !0 }), window.addEventListener("scroll", r(o, 100), { passive: !0 }), window.addEventListener("resize", r(o, 100), { passive: !0 }), window.addEventListener("pagehide", a), window.addEventListener("beforeunload", a), window.addEventListener("unload", a), o();
})();
