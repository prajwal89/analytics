(function() {
  var r;
  let e = {
    totalTimeSpent: 0,
    lastActiveTime: Date.now(),
    maxScrollDepth: 0,
    isCurrentlyHidden: !1,
    hasBeaconSupport: "sendBeacon" in navigator,
    // hasBeaconSupport: false,
    csrfToken: (r = document.querySelector('meta[name="csrf-token"]')) == null ? void 0 : r.getAttribute("content"),
    isUnloading: !1
  };
  console.log(e);
  function l(n, i) {
    let t;
    return function(...s) {
      t || (n.apply(this, s), t = !0, setTimeout(() => t = !1, i));
    };
  }
  function o() {
    const n = Math.max(
      document.documentElement.scrollHeight,
      document.documentElement.offsetHeight,
      document.documentElement.clientHeight
    ), i = window.innerHeight, t = window.pageYOffset || document.documentElement.scrollTop, s = n - i, u = Math.floor(t / s * 100);
    e.maxScrollDepth = Math.max(e.maxScrollDepth, Math.min(u, 100));
  }
  function c() {
    document.visibilityState === "hidden" ? (e.isCurrentlyHidden = !0, e.totalTimeSpent += (Date.now() - e.lastActiveTime) / 1e3) : (e.isCurrentlyHidden = !1, e.lastActiveTime = Date.now());
  }
  function d() {
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
      navigator.sendBeacon("/api/an/store", i);
      return;
    }
    try {
      const t = new XMLHttpRequest();
      t.open("POST", "/api/an/store", !1), t.setRequestHeader("Content-Type", "application/json"), e.csrfToken && t.setRequestHeader("X-CSRF-Token", e.csrfToken), t.send(JSON.stringify(n));
    } catch (t) {
      console.error("Failed to send final analytics:", t);
    }
  }
  function a() {
    console.log("sending"), !e.isUnloading && (e.isUnloading = !0, d());
  }
  document.addEventListener("visibilitychange", c, { passive: !0 }), window.addEventListener("scroll", l(o, 100), { passive: !0 }), window.addEventListener("resize", l(o, 100), { passive: !0 }), window.addEventListener("pagehide", a), window.addEventListener("beforeunload", a), window.addEventListener("unload", a), o();
})();
