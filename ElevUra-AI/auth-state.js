/**
 * ElevUra — frontend-only auth state (localStorage)
 */
(function (global) {
  const STORAGE_KEY = 'elevura_auth_user';

  const DEFAULT_AVATARS = [
    'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=128&h=128&fit=crop&crop=faces',
    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=128&h=128&fit=crop&crop=faces',
    'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=128&h=128&fit=crop&crop=faces',
  ];

  function pickAvatar(seed) {
    const n = Math.abs(
      String(seed || '')
        .split('')
        .reduce((a, c) => a + c.charCodeAt(0), 0)
    );
    return DEFAULT_AVATARS[n % DEFAULT_AVATARS.length];
  }

  function normalizeUser(raw) {
    if (!raw || !raw.loggedIn) return null;
    return {
      username: raw.username || 'User',
      email: raw.email || '',
      tier: raw.tier || 'Pro',
      avatar: raw.avatar || pickAvatar(raw.username),
      loggedIn: true,
    };
  }

  function getUser() {
    try {
      const data = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
      return normalizeUser(data);
    } catch {
      return null;
    }
  }

  function setUser(user) {
    const payload = normalizeUser({ ...user, loggedIn: true });
    if (!payload) return null;
    localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    global.dispatchEvent(new CustomEvent('elevura:auth-change', { detail: { user: payload } }));
    return payload;
  }

  function logout() {
    localStorage.removeItem(STORAGE_KEY);
    global.dispatchEvent(new CustomEvent('elevura:auth-change', { detail: { user: null } }));
  }

  function isLoggedIn() {
    return !!getUser();
  }

  function userFromLogin(email) {
    const local = (email || '').split('@')[0] || 'User';
    const username = local.charAt(0).toUpperCase() + local.slice(1);
    return {
      username,
      email: email || '',
      tier: 'Pro',
      avatar: pickAvatar(email),
      loggedIn: true,
    };
  }

  function userFromSignup(name, email) {
    const username = (name || 'User').trim().split(/\s+/)[0] || 'User';
    return {
      username,
      email: email || '',
      tier: 'Pro',
      avatar: pickAvatar(name || email),
      loggedIn: true,
    };
  }

  global.ElevUraAuth = {
    STORAGE_KEY,
    getUser,
    setUser,
    logout,
    isLoggedIn,
    userFromLogin,
    userFromSignup,
    pickAvatar,
  };
})(window);
