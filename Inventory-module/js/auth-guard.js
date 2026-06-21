import { auth } from './firebase-config.js';
import { onAuthStateChanged, signOut } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-auth.js";

// 1. Sembunyikan halaman terlebih dahulu untuk mencegah visual flash sebelum status auth diketahui
document.documentElement.style.display = 'none';

// 2. Pantau status login user secara real-time
onAuthStateChanged(auth, (user) => {
    if (user) {
        // User telah login
        document.documentElement.style.display = ''; // Tampilkan kembali halaman
        
        // Simpan data ke localStorage untuk kompabilitas dengan komponen lain
        const nameParts = (user.displayName || "User").split(" ");
        const firstName = nameParts[0] || "";
        const lastName = nameParts.slice(1).join(" ") || "";
        
        const activeUserData = {
            firstName: firstName,
            lastName: lastName,
            email: user.email
        };
        localStorage.setItem('foodsync_active_user', JSON.stringify(activeUserData));
        
        // Update nama di topbar secara dinamis jika elemennya ada
        const topbarNameElement = document.getElementById('topbarUserName');
        if (topbarNameElement) {
            topbarNameElement.textContent = user.displayName || user.email;
        }
    } else {
        // User belum login atau sudah logout
        localStorage.removeItem('foodsync_active_user');
        window.location.href = 'login.html';
    }
});

// 3. Fungsi logout global
window.logoutUser = function() {
    signOut(auth).then(() => {
        localStorage.removeItem('foodsync_active_user');
        window.location.href = 'login.html';
    }).catch((error) => {
        console.error("Gagal melakukan sign out:", error);
    });
};
