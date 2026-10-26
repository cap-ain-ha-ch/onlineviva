// Small reusable client-side helper: show or hide password fields.
document.addEventListener('click', function (event) {
    const button = event.target.closest('[data-toggle-password]');
    if (!button) return;
    const selector = button.getAttribute('data-toggle-password');
    const input = document.querySelector(selector);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    button.textContent = isPassword ? 'Hide' : 'Show';
    button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
});
