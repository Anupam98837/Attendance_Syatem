<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Sign In — Attendance System</title>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/media/images/web/favicon.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --primary:   #0ea5e9;
      --accent:    #2563eb;
      --ink:       #102a6a;
      --body-text: #10213f;
      --muted:     #5b6b8c;
      --bg:        #eef6ff;
      --surface:   #ffffff;
      --surface-2: #f3f8ff;
      --border:    #dbe9ff;
      --border-md: #bfd5f8;
      --shadow-1:  0 1px 3px rgba(30,58,138,.08), 0 1px 2px rgba(30,58,138,.04);
      --shadow-2:  0 4px 16px rgba(30,58,138,.10), 0 2px 6px rgba(30,58,138,.06);
      --shadow-3:  0 16px 40px rgba(15,23,42,.12), 0 4px 12px rgba(15,23,42,.06);
      --ring:      0 0 0 3px rgba(37,99,235,.18);
      --r-md:      14px;
      --r-lg:      20px;
      --r-xl:      26px;
    }

    html, body {
      height: 100%;
      font-family: 'Inter', system-ui, sans-serif;
      background: var(--bg);
      color: var(--body-text);
    }

    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
    }

    /* ── Card ───────────────────────────────────────────── */
    .auth-card {
      width: min(420px, 100%);
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--r-xl);
      box-shadow: var(--shadow-3);
      padding: 40px 36px 36px;
      animation: fadeUp .3s ease both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(14px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Brand header ───────────────────────────────────── */
    .auth-brand {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      margin-bottom: 32px;
    }

    .auth-logo {
      width: 72px;
      height: 72px;
      border-radius: 20px;
      background: #fff;
      border: 1px solid var(--border);
      display: grid;
      place-items: center;
      margin-bottom: 14px;
      box-shadow: 0 4px 16px rgba(30,58,138,.10);
      overflow: hidden;
    }

    .auth-logo img {
      width: 52px;
      height: 52px;
      object-fit: contain;
    }

    .auth-logo i {
      font-size: 26px;
      color: var(--accent);
    }

    .auth-app-name {
      font-family: 'Poppins', sans-serif;
      font-size: .78rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: var(--primary);
      margin-bottom: 6px;
    }

    .auth-title {
      font-family: 'Poppins', sans-serif;
      font-size: 1.45rem;
      font-weight: 700;
      color: var(--ink);
      letter-spacing: -.02em;
      line-height: 1.15;
    }

    .auth-sub {
      margin-top: 6px;
      font-size: .875rem;
      color: var(--muted);
      line-height: 1.55;
    }

    /* ── Alert ──────────────────────────────────────────── */
    .auth-alert {
      display: none;
      align-items: center;
      gap: 9px;
      padding: 11px 14px;
      border-radius: var(--r-md);
      font-size: .855rem;
      font-weight: 500;
      line-height: 1.45;
      margin-bottom: 20px;
    }
    .auth-alert.show { display: flex; }
    .auth-alert.err  { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
    .auth-alert.ok   { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }

    /* ── Fields ─────────────────────────────────────────── */
    .auth-field { margin-bottom: 16px; }

    .auth-label {
      display: block;
      font-size: .82rem;
      font-weight: 600;
      color: var(--ink);
      margin-bottom: 7px;
    }

    .auth-input-wrap { position: relative; }

    .auth-input-wrap .icon {
      position: absolute;
      left: 13px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--border-md);
      font-size: .85rem;
      pointer-events: none;
      transition: color .15s;
    }

    .auth-input-wrap:focus-within .icon {
      color: var(--accent);
    }

    .auth-input {
      width: 100%;
      height: 46px;
      border: 1px solid var(--border-md);
      border-radius: var(--r-md);
      background: var(--surface-2);
      color: var(--body-text);
      font-family: inherit;
      font-size: .92rem;
      padding: 0 14px 0 40px;
      transition: border-color .15s, box-shadow .15s, background .15s;
      -webkit-appearance: none;
    }

    .auth-input::placeholder { color: #a8bbd9; }

    .auth-input:focus {
      outline: none;
      border-color: var(--accent);
      background: var(--surface);
      box-shadow: var(--ring);
    }

    .auth-input.has-eye { padding-right: 44px; }

    .eye-btn {
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%);
      width: 32px;
      height: 32px;
      border: 0;
      background: transparent;
      color: var(--border-md);
      display: grid;
      place-items: center;
      border-radius: 8px;
      cursor: pointer;
      transition: color .15s;
    }
    .eye-btn:hover { color: var(--muted); }

    /* ── Row: remember + forgot ─────────────────────────── */
    .auth-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 4px 0 22px;
      gap: 8px;
    }

    .auth-check-label {
      display: flex;
      align-items: center;
      gap: 7px;
      font-size: .855rem;
      color: var(--muted);
      cursor: pointer;
      user-select: none;
    }

    .auth-check {
      width: 16px;
      height: 16px;
      accent-color: var(--accent);
      cursor: pointer;
    }

    .auth-link {
      font-size: .855rem;
      font-weight: 600;
      color: var(--accent);
      text-decoration: none;
    }
    .auth-link:hover { color: var(--primary); }

    /* ── Submit ─────────────────────────────────────────── */
    .auth-btn {
      width: 100%;
      height: 48px;
      border: 0;
      border-radius: var(--r-md);
      background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
      color: #fff;
      font-family: inherit;
      font-size: .94rem;
      font-weight: 600;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(37,99,235,.28);
      transition: transform .15s ease, box-shadow .15s ease, filter .15s;
      letter-spacing: .01em;
    }

    .auth-btn:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(37,99,235,.36);
      filter: brightness(1.05);
    }

    .auth-btn:active:not(:disabled) {
      transform: translateY(0);
      box-shadow: 0 4px 14px rgba(37,99,235,.28);
    }

    .auth-btn:disabled {
      opacity: .7;
      cursor: not-allowed;
    }

    /* ── Footer ─────────────────────────────────────────── */
    .auth-footer {
      margin-top: 22px;
      padding-top: 18px;
      border-top: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .auth-footer-item {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: .8rem;
      color: #a8bbd9;
    }

    .auth-footer-item i { font-size: .72rem; color: var(--primary); }

    /* ── Page footer ────────────────────────────────────── */
    .page-foot {
      margin-top: 20px;
      font-size: .8rem;
      color: #a8bbd9;
      text-align: center;
    }
    .page-foot a { color: var(--accent); text-decoration: none; font-weight: 500; }
    .page-foot a:hover { color: var(--primary); }

    @media (max-width: 480px) {
      .auth-card { padding: 28px 20px 24px; border-radius: var(--r-lg); }
    }
  </style>
</head>
<body>

  <div class="auth-card">

    <!-- Brand -->
    <div class="auth-brand">
      <div class="auth-logo">
      <img id="logo" src="{{ asset('/assets/media/images/web/logo.png') }}" alt="Attendance System">
    </div>
      <div class="auth-app-name">Attendance System</div>
      <div class="auth-title">Sign in to your account</div>
      <div class="auth-sub">Enter your credentials to continue</div>
    </div>

    <!-- Alert -->
    <div id="a_alert" class="auth-alert" role="alert">
      <i id="a_icon" class="fa-solid fa-circle-exclamation fa-sm"></i>
      <span id="a_msg"></span>
    </div>

    <!-- Form -->
    <form id="a_form" novalidate>
      @csrf

      <div class="auth-field">
        <label class="auth-label" for="a_id">Email or Phone Number</label>
        <div class="auth-input-wrap">
          <i class="icon fa-solid fa-at"></i>
          <input id="a_id" type="text" class="auth-input" name="identifier"
                 placeholder="you@example.com" autocomplete="username" required>
        </div>
      </div>

      <div class="auth-field">
        <label class="auth-label" for="a_pw">Password</label>
        <div class="auth-input-wrap">
          <i class="icon fa-solid fa-lock"></i>
          <input id="a_pw" type="password" class="auth-input has-eye" name="password"
                 placeholder="••••••••" minlength="8" autocomplete="current-password" required>
          <button type="button" class="eye-btn" id="a_eye" aria-label="Show password">
            <i class="fa-regular fa-eye-slash fa-sm"></i>
          </button>
        </div>
      </div>

      <div class="auth-row">
        <label class="auth-check-label">
          <input type="checkbox" class="auth-check" id="a_keep">
          Keep me signed in
        </label>
      </div>

      <button type="submit" class="auth-btn" id="a_btn">
        <i class="fa-solid fa-right-to-bracket me-1"></i> Sign In
      </button>
    </form>


  </div>

  <div class="page-foot">
   {{ date('Y') }} Attendance System
  </div>

<script>
(function () {
  const LOGIN_API = '/api/auth/login';
  const CHECK_API = '/api/auth/check';
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  const form    = document.getElementById('a_form');
  const idIn    = document.getElementById('a_id');
  const pwIn    = document.getElementById('a_pw');
  const keepCb  = document.getElementById('a_keep');
  const btn     = document.getElementById('a_btn');
  const alertEl = document.getElementById('a_alert');
  const alertMsg= document.getElementById('a_msg');
  const alertIco= document.getElementById('a_icon');
  const eyeBtn  = document.getElementById('a_eye');
  const redirect= new URLSearchParams(window.location.search).get('redirect') || '';

  function safePath(fallback) {
    if (!redirect || !redirect.startsWith('/') || redirect.startsWith('//')) return fallback;
    return redirect;
  }

  function setBusy(on) {
    btn.disabled = on;
    btn.innerHTML = on
      ? '<i class="fa-solid fa-spinner fa-spin me-1"></i> Signing in...'
      : '<i class="fa-solid fa-right-to-bracket me-1"></i> Sign In';
  }

  function showAlert(type, msg) {
    alertEl.className = 'auth-alert show ' + (type === 'error' ? 'err' : 'ok');
    alertIco.className = 'fa-solid fa-sm ' + (type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check');
    alertMsg.textContent = msg;
  }

  function clearAlert() {
    alertEl.className = 'auth-alert';
  }

  const store = {
    set(token, role, keep) {
      sessionStorage.setItem('token', token);
      sessionStorage.setItem('role', role);
      if (keep) { localStorage.setItem('token', token); localStorage.setItem('role', role); }
      else       { localStorage.removeItem('token');     localStorage.removeItem('role'); }
    },
    clear() {
      ['token','role'].forEach(k => { sessionStorage.removeItem(k); localStorage.removeItem(k); });
    },
    local() {
      return { token: localStorage.getItem('token'), role: localStorage.getItem('role') };
    }
  };

  function rolePath(role){
    const r = (role || '').toString().trim().toLowerCase();
    return r === 'employee' ? '/attendance/employee-dashboard' : '/dashboard';
  }

  // Eye toggle
  eyeBtn?.addEventListener('click', () => {
    const show = pwIn.type === 'password';
    pwIn.type = show ? 'text' : 'password';
    eyeBtn.querySelector('i').className = 'fa-regular fa-sm ' + (show ? 'fa-eye' : 'fa-eye-slash');
  });

  // Auto-login if token in localStorage
  async function tryAutoLogin() {
    const { token, role } = store.local();
    if (!token) return;
    try {
      const res  = await fetch(CHECK_API, { headers: { 'Authorization': 'Bearer ' + token } });
      const data = await res.json().catch(() => ({}));
      if (res.ok && data?.user) {
        const resolvedRole = (data.user.role || role || '').toLowerCase();
        store.set(token, resolvedRole, true);
        window.location.replace(safePath(rolePath(resolvedRole)));
      } else {
        store.clear();
      }
    } catch (_) {}
  }

  document.addEventListener('DOMContentLoaded', tryAutoLogin);

  // Submit
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearAlert();

    const identifier = idIn.value.trim();
    const password   = pwIn.value;
    const keep       = keepCb.checked;

    if (!identifier || !password) {
      showAlert('error', 'Please enter your email / phone and password.');
      return;
    }

    setBusy(true);
    try {
      const res  = await fetch(LOGIN_API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ login: identifier, password, remember: keep })
      });
      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        const msg = data?.message || data?.error
          || (data?.errors ? Object.values(data.errors).flat().join(', ') : 'Login failed. Please try again.');
        showAlert('error', msg);
        return;
      }

      const token = data?.access_token || data?.token || '';
      const role  = (data?.user?.role || 'employee').toLowerCase();

      if (!token) { showAlert('error', 'No token received from server.'); return; }

      store.set(token, role, keep);
      showAlert('success', 'Login successful — redirecting...');
      setTimeout(() => window.location.assign(safePath(rolePath(role))), 480);
    } catch (_) {
      showAlert('error', 'Network error. Please check your connection and try again.');
    } finally {
      setBusy(false);
    }
  });
})();
</script>
</body>
</html>
