/* 
  SkinsLove.gg - Custom Javascript Core
  Handles dynamic statistics counters, password toggling, and Bootstrap Toast confirmations.
*/

// 1. Animasi Counter Statistik pada Halaman Beranda
document.addEventListener('DOMContentLoaded', function () {
    const counters = document.querySelectorAll('.stat-counter');
    counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target') || '0');
        const duration = 1500; // Durasi total animasi dalam milidetik
        const stepTime = 15; // Interval update per langkah
        const steps = duration / stepTime; // Jumlah total langkah
        const isFloat = counter.getAttribute('data-is-float') === 'true';
        let current = 0;
        const increment = target / steps;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                counter.innerText = isFloat ? target.toFixed(1) : Math.round(target).toLocaleString('id-ID');
            } else {
                counter.innerText = isFloat ? current.toFixed(1) : Math.round(current).toLocaleString('id-ID');
            }
        }, stepTime);
    });
});

// 2. Intersepsi Form "Jual Kembali" Menggunakan Bootstrap Toast Kustom
document.addEventListener('DOMContentLoaded', function () {
    const sellForms = document.querySelectorAll('.sell-back-form');
    const toastEl = document.getElementById('confirmSellToast');
    const toastConfirmBtn = document.getElementById('toastConfirmBtn');
    const toastPromptText = document.getElementById('toastPromptText');

    if (sellForms.length > 0 && toastEl && toastConfirmBtn && toastPromptText) {
        // Inisialisasi object Toast Bootstrap
        const bsToast = new bootstrap.Toast(toastEl);
        let activeFormToSubmit = null;

        sellForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                // Mencegah form agar tidak langsung berpindah halaman (submit default)
                e.preventDefault();
                activeFormToSubmit = form; // Simpan referensi form yang sedang diproses

                // Ambil atribut metadata dari tombol form terkait
                const skinName = form.getAttribute('data-skin-name') || 'Skin';
                const skinPrice = form.getAttribute('data-skin-price') || '0.00';

                // Tulis instruksi konfirmasi dinamis ke dalam badan Toast
                toastPromptText.innerHTML = `Apakah Anda yakin ingin menjual kembali skin <strong>${skinName}</strong> seharga <strong>$${skinPrice}</strong>? Saldo Anda akan langsung bertambah saat ini juga.`;

                // Tampilkan jendela dialog Toast di sudut kanan bawah
                bsToast.show();
            });
        });

        // Eksekusi pengiriman data aslinya ketika tombol setuju diklik di dalam Toast
        toastConfirmBtn.addEventListener('click', function () {
            if (activeFormToSubmit) {
                activeFormToSubmit.submit();
            }
        });
    }
});

// 3. Kontrol Sembunyikan & Tampilkan Kata Sandi
document.addEventListener('DOMContentLoaded', function () {
    // Toggler untuk kolom kata sandi utama
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', function () {
            // Tukar tipe input untuk menyamarkan atau mengekspos karakter sandi
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            if (type === 'password') {
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            } else {
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            }
        });
    }

    // Toggler untuk kolom konfirmasi kata sandi kedua (registrasi)
    const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
    const confirmPasswordInput = document.querySelector('#confirm_password');
    const eyeIconConfirm = document.querySelector('#eyeIconConfirm');

    if (toggleConfirmPassword && confirmPasswordInput && eyeIconConfirm) {
        toggleConfirmPassword.addEventListener('click', function () {
            // Tukar tipe input konfirmasi sandi
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            if (type === 'password') {
                eyeIconConfirm.classList.remove('bi-eye-slash');
                eyeIconConfirm.classList.add('bi-eye');
            } else {
                eyeIconConfirm.classList.remove('bi-eye');
                eyeIconConfirm.classList.add('bi-eye-slash');
            }
        });
    }
});
