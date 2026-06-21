<!-- Letakkan script ini tepat di atas tag penutup </body> -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mengambil elemen-elemen dari DOM (Document Object Model)
            const loginForm = document.querySelector('form');
            const emailInput = document.querySelector('input[type="email"]');
            const passwordInput = document.querySelector('input[type="password"]');
            const rememberCheckbox = document.querySelector('input[type="checkbox"]');
            const forgotPasswordLink = document.querySelector('.text-forgot');
            const registerBtn = document.querySelector('.btn-register');

            // 1. Menangani event saat form disubmit (Tombol Sign In diklik)
            loginForm.addEventListener('submit', function(e) {
                // Mencegah halaman me-reload secara default
                e.preventDefault();

                // Mengambil nilai dari input dan menghilangkan spasi berlebih
                const emailValue = emailInput.value.trim();
                const passwordValue = passwordInput.value.trim();
                const isRemembered = rememberCheckbox.checked;

                // Validasi Dasar: Cek apakah field kosong
                if (!emailValue) {
                    alert('Mohon masukkan email Anda.');
                    emailInput.focus();
                    return;
                }

                if (!passwordValue) {
                    alert('Mohon masukkan password Anda.');
                    passwordInput.focus();
                    return;
                }

                // Validasi Format Email Sederhana menggunakan Regex
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailValue)) {
                    alert('Format email tidak valid. Pastikan menggunakan @ dan domain.');
                    emailInput.focus();
                    return;
                }

                // Jika semua validasi lolos, simulasikan proses pengiriman data
                console.log('Mencoba Login dengan data:');
                console.log({
                    email: emailValue,
                    password: passwordValue, // Dalam real-case, password tidak boleh di-log
                    rememberMe: isRemembered
                });

                alert('Login Berhasil! Selamat datang.');
                
                // Nantinya logika ini bisa diarahkan ke dashboard, contoh:
                // window.location.href = 'dashboard.html';
            });

            // 2. Menangani klik pada tombol Register
            registerBtn.addEventListener('click', function() {
                alert('Mengarahkan ke halaman pembuatan akun baru...');
                // Logika pindah halaman:
                // window.location.href = 'register.html';
            });

            // 3. Menangani klik pada link Forgot Password
            forgotPasswordLink.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Ambil email jika user sudah sempat mengetiknya
                const currentEmail = emailInput.value.trim();
                if (currentEmail) {
                    alert(`Instruksi pemulihan password akan dikirim ke ${currentEmail} (Simulasi)`);
                } else {
                    alert('Mengarahkan ke halaman pemulihan password...');
                }
            });
        });
    </script>
</body>
</html>