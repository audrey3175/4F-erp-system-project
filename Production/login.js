<script>
        import { app } from './firebase.js';

        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 1. Fitur Log Out dengan Konfirmasi ---
            const logoutBtn = document.querySelector('.btn-logout');
            if (logoutBtn) {
                // Menghapus atribut onclick bawaan HTML jika masih ada
                logoutBtn.removeAttribute('onclick'); 
                
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    
                    // Memunculkan pop-up konfirmasi standar browser
                    const confirmLogout = confirm('Apakah Anda yakin ingin keluar dari sistem ERP PT Indofood?');
                    
                    if (confirmLogout) {
                        // Simulasi membersihkan data sesi (jika ada)
                        console.log('Sesi diakhiri oleh pengguna.');
                        // Mengarahkan kembali ke halaman Sign In
                        window.location.href = 'index.html';
                    }
                });
            }

            // --- 2. Sistem Navigasi Modul Terpusat ---
            const moduleCards = document.querySelectorAll('.module-card');

            moduleCards.forEach(function(card) {
                // Menghapus atribut onclick bawaan HTML jika masih ada
                card.removeAttribute('onclick');

                card.addEventListener('click', function() {
                    // Mengambil teks judul modul dari dalam kartu (misal: "Production")
                    const moduleTitle = this.querySelector('.module-title').innerText.replace(/\n/g, ' ');

                    // Logika Routing (Pengalihan Halaman)
                    if (moduleTitle.includes('Production')) {
                        console.log('Mengarahkan ke ruang kerja Production...');
                        window.location.href = 'production.html';
                    } 
                    else if (moduleTitle.includes('Sales')) {
                        alert('Modul Sales and Marketing sedang dalam tahap integrasi database. Silakan akses modul Production.');
                    }
                    else {
                        // Respon default untuk modul yang belum aktif
                        alert(`Akses ke modul "${moduleTitle}" saat ini dibatasi untuk peran Anda, atau modul sedang dalam pemeliharaan.`);
                    }
                });
            });

            // --- 3. Animasi Staggered Masuk (Fade-in & Slide-up) ---
            // Menyembunyikan semua kartu di awal
            moduleCards.forEach((card, index) => {
                // Setel state awal sebelum animasi
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                // Setel transisi CSS via JavaScript
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';

                // Gunakan setTimeout untuk memunculkan kartu satu per satu (efek domino)
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                    
                    // Kembalikan efek hover CSS bawaan setelah animasi selesai
                    setTimeout(() => {
                        card.style.transition = 'transform 0.3s ease, background 0.3s ease, box-shadow 0.3s ease';
                    }, 600);
                    
                }, 150 * (index + 1)); // Jeda 150ms antar kartu
            });

        });
    </script>