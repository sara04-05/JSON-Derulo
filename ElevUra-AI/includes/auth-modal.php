    <!-- Auth modal (opens from sidebar; no navigation) -->
    <div id="auth-modal-root" class="auth-modal" aria-hidden="true" hidden>
        <div class="auth-modal__backdrop" data-auth-close tabindex="-1" aria-hidden="true"></div>
        <div class="auth-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="auth-modal-heading">
            <button type="button" class="auth-modal__close" id="auth-modal-close" aria-label="Close">&times;</button>
            <div class="auth-modal__head">
                <h2 class="auth-modal__heading" id="auth-modal-heading">Elev<span class="hero-title-gradient">Ura</span></h2>
            </div>
            <p class="auth-modal__subtitle" id="auth-modal-subtitle">Sign in to continue</p>

            <div class="auth-modal__tabs" role="tablist" aria-label="Authentication">
                <button type="button" class="auth-modal__tab" role="tab" id="auth-tab-login" aria-selected="true" aria-controls="auth-panel-login" tabindex="0">
                    <span class="auth-modal__tab-highlight" aria-hidden="true"></span>
                    <span>Login</span>
                </button>
                <button type="button" class="auth-modal__tab" role="tab" id="auth-tab-signup" aria-selected="false" aria-controls="auth-panel-signup" tabindex="-1">
                    <span class="auth-modal__tab-highlight" aria-hidden="true"></span>
                    <span>Sign up</span>
                </button>
            </div>

            <div class="auth-modal__panels">
                <div class="auth-modal__panel is-active" role="tabpanel" id="auth-panel-login" aria-labelledby="auth-tab-login">
                    <form id="auth-form-login" novalidate>
                        <div class="auth-field">
                            <div class="auth-field-float" id="auth-wrap-login-identifier">
                                <input type="text" id="auth-login-identifier" name="identifier" placeholder=" " autocomplete="username" required aria-describedby="auth-login-identifier-msg">
                                <label for="auth-login-identifier">Email or username</label>
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <p class="auth-field-msg" id="auth-login-identifier-msg" role="status"></p>
                            </div>
                        </div>
                        <div class="auth-field">
                            <div class="auth-field-float" id="auth-wrap-login-password">
                                <input type="password" id="auth-login-password" name="password" placeholder=" " autocomplete="current-password" required minlength="6" aria-describedby="auth-login-password-msg">
                                <label for="auth-login-password">Password</label>
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <button type="button" class="auth-toggle-pass" id="auth-toggle-login-pass" aria-label="Show password" aria-pressed="false">
                                    <svg class="auth-icon-eye" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="auth-icon-eye-off" viewBox="0 0 24 24" style="display:none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                </button>
                                <p class="auth-field-msg" id="auth-login-password-msg" role="status"></p>
                            </div>
                        </div>
                        <div class="auth-row-between">
                            <label class="auth-checkbox">
                                <input type="checkbox" name="remember" id="auth-login-remember">
                                <span>Remember me</span>
                            </label>
                            <button type="button" class="auth-link" id="auth-forgot">Forgot password?</button>
                        </div>
                        <button type="submit" class="auth-btn-submit" id="auth-btn-login">
                            <span class="auth-spinner" aria-hidden="true"></span>
                            <span class="auth-btn-text">Sign in</span>
                        </button>
                    </form>
                </div>

                <div class="auth-modal__panel" role="tabpanel" id="auth-panel-signup" aria-labelledby="auth-tab-signup">
                    <form id="auth-form-signup" novalidate>
                        <div class="auth-field">
                            <div class="auth-field-float" id="auth-wrap-signup-username">
                                <input type="text" id="auth-signup-username" name="username" placeholder=" " autocomplete="username" required minlength="2" maxlength="50" pattern="[a-zA-Z0-9_]{2,50}" aria-describedby="auth-signup-username-msg">
                                <label for="auth-signup-username">Username</label>
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                </span>
                                <p class="auth-field-msg" id="auth-signup-username-msg" role="status"></p>
                            </div>
                        </div>
                        <div class="auth-field">
                            <div class="auth-field-float" id="auth-wrap-signup-email">
                                <input type="email" id="auth-signup-email" name="email" placeholder=" " autocomplete="email" required aria-describedby="auth-signup-email-msg">
                                <label for="auth-signup-email">Email</label>
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <p class="auth-field-msg" id="auth-signup-email-msg" role="status"></p>
                            </div>
                        </div>
                        <div class="auth-field">
                            <div class="auth-field-float" id="auth-wrap-signup-password">
                                <input type="password" id="auth-signup-password" name="password" placeholder=" " autocomplete="new-password" required minlength="8" aria-describedby="auth-signup-password-msg auth-strength-label">
                                <label for="auth-signup-password">Password</label>
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <button type="button" class="auth-toggle-pass" id="auth-toggle-signup-pass" aria-label="Show password" aria-pressed="false">
                                    <svg class="auth-icon-eye" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="auth-icon-eye-off" viewBox="0 0 24 24" style="display:none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                </button>
                                <p class="auth-field-msg" id="auth-signup-password-msg" role="status"></p>
                            </div>
                            <div aria-live="polite">
                                <div class="auth-strength-bars" id="auth-strength-bars">
                                    <span class="auth-strength-bar" data-bar="0"></span>
                                    <span class="auth-strength-bar" data-bar="1"></span>
                                    <span class="auth-strength-bar" data-bar="2"></span>
                                    <span class="auth-strength-bar" data-bar="3"></span>
                                </div>
                                <p class="auth-strength-label" id="auth-strength-label">Password strength: enter a password</p>
                            </div>
                        </div>
                        <div class="auth-field">
                            <div class="auth-field-float" id="auth-wrap-signup-confirm">
                                <input type="password" id="auth-signup-confirm" name="confirm" placeholder=" " autocomplete="new-password" required aria-describedby="auth-signup-confirm-msg">
                                <label for="auth-signup-confirm">Confirm password</label>
                                <span class="auth-input-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <button type="button" class="auth-toggle-pass" id="auth-toggle-signup-confirm" aria-label="Show confirm password" aria-pressed="false">
                                    <svg class="auth-icon-eye" viewBox="0 0 24 24"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg class="auth-icon-eye-off" viewBox="0 0 24 24" style="display:none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                </button>
                                <p class="auth-field-msg" id="auth-signup-confirm-msg" role="status"></p>
                            </div>
                        </div>
                        <label class="auth-checkbox auth-terms-row">
                            <input type="checkbox" id="auth-signup-terms" required aria-describedby="auth-terms-msg">
                            <span>I agree to the Terms and Privacy Policy</span>
                        </label>
                        <p class="auth-field-msg auth-terms-msg" id="auth-terms-msg" role="status"></p>
                        <button type="submit" class="auth-btn-submit" id="auth-btn-signup">
                            <span class="auth-spinner" aria-hidden="true"></span>
                            <span class="auth-btn-text">Create account</span>
                        </button>
                    </form>
                </div>
            </div>
            <p class="auth-modal__flash" id="auth-modal-flash" role="status" aria-live="polite"></p>
        </div>
    </div>
