/**
 * ElevUra — auth state (PHP sessions + MySQL backend)
 */
(function (global) {
  const API_BASE = 'backend/';
  let currentUser = null;
  let dashboardCache = null;

  async function apiRequest(endpoint, options = {}) {
    const url = API_BASE + endpoint;
    const res = await fetch(url, {
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      ...options,
    });
    let data = {};
    try {
      data = await res.json();
    } catch {
      throw new Error('Invalid server response.');
    }
    if (!res.ok || data.success === false) {
      throw new Error(data.message || 'Request failed.');
    }
    return data;
  }

  function normalizeUser(raw) {
    if (!raw) return null;
    return {
      id: raw.id,
      username: raw.username || 'User',
      email: raw.email || '',
      tier: raw.tier || raw.membership_tier || 'Free',
      avatar: raw.avatar || '',
      loggedIn: true,
    };
  }

  function dispatchChange() {
    global.dispatchEvent(
      new CustomEvent('elevura:auth-change', {
        detail: { user: currentUser, dashboard: dashboardCache },
      })
    );
  }

  function setUser(user) {
    currentUser = normalizeUser(user);
    dispatchChange();
    return currentUser;
  }

  function clearUser() {
    currentUser = null;
    dashboardCache = null;
    dispatchChange();
  }

  function getUser() {
    return currentUser;
  }

  function isLoggedIn() {
    return !!currentUser;
  }

  function getDashboardData() {
    return dashboardCache;
  }

  async function refreshSession() {
    try {
      const data = await apiRequest('get_user_data.php', { method: 'GET' });
      if (data.logged_in && data.user) {
        currentUser = normalizeUser(data.user);
        dashboardCache = {
          cvs: data.cvs || [],
          applied_jobs: data.applied_jobs || [],
          courses: data.courses || [],
          mock_interviews: data.mock_interviews || [],
          analytics: data.analytics || {},
        };
      } else {
        currentUser = null;
        dashboardCache = null;
      }
      dispatchChange();
      return { user: currentUser, dashboard: dashboardCache };
    } catch {
      currentUser = null;
      dashboardCache = null;
      dispatchChange();
      return { user: null, dashboard: null };
    }
  }

  async function login(identifier, password) {
    const data = await apiRequest('login.php', {
      method: 'POST',
      body: JSON.stringify({ identifier, password }),
    });
    setUser(data.user);
    await refreshSession();
    return currentUser;
  }

  async function signup(username, email, password, confirmPassword) {
    const data = await apiRequest('signup.php', {
      method: 'POST',
      body: JSON.stringify({
        username,
        email,
        password,
        confirm_password: confirmPassword,
      }),
    });
    setUser(data.user);
    await refreshSession();
    return currentUser;
  }

  async function logout() {
    try {
      await apiRequest('logout.php', { method: 'POST', body: '{}' });
    } catch {
      /* session may already be gone */
    }
    clearUser();
  }

  global.ElevUraAuth = {
    API_BASE,
    getUser,
    setUser,
    logout,
    isLoggedIn,
    refreshSession,
    login,
    signup,
    getDashboardData,
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => refreshSession());
  } else {
    refreshSession();
  }
})(window);
