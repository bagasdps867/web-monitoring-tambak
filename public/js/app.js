// Toggle Tema - Bootstrap 5.3+ Compatible
document.addEventListener('DOMContentLoaded', function () {
    const themeSwitch = document.getElementById('themeSwitch');
    const mobileThemeBtn = document.getElementById('mobileThemeSwitch');
    const htmlTag = document.documentElement;

    // Cek tema tersimpan di localStorage
    let savedTheme = localStorage.getItem('theme');

    // Jika tidak ada tema tersimpan, ikuti preferensi sistem
    if (!savedTheme) {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        savedTheme = prefersDark ? 'dark' : 'light';
        localStorage.setItem('theme', savedTheme);
    }

    // Set tema awal ke <html>
    htmlTag.setAttribute('data-bs-theme', savedTheme);

    // Set posisi toggle desktop (jika ada)
    if (themeSwitch) {
        themeSwitch.checked = savedTheme === 'dark';

        themeSwitch.addEventListener('change', function () {
            const newTheme = this.checked ? 'dark' : 'light';
            htmlTag.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }

    // Set toggle mobile (jika ada)
    if (mobileThemeBtn) {
        mobileThemeBtn.addEventListener('click', function () {
            const currentTheme = htmlTag.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            htmlTag.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            if (themeSwitch) {
                themeSwitch.checked = newTheme === 'dark';
            }
        });
    }
});
