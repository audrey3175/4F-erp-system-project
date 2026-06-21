// 1. Import Firebase Inti
import { initializeApp } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-app.js";

// 2. Import Fitur Login dan Firestore (Kembali ke Firestore ya!)
import { getAuth } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/12.15.0/firebase-firestore.js";

// 3. Kunci Konfigurasi Firebase Project-mu
const firebaseConfig = {
    apiKey: "AIzaSyCA4dmSAoHZBqzRRS99t1m53323DiqadkI",
    authDomain: "foodsync-f802d.firebaseapp.com",
    databaseURL: "https://foodsync-f802d-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "foodsync-f802d",
    storageBucket: "foodsync-f802d.firebasestorage.app",
    messagingSenderId: "416577966500",
    appId: "1:416577966500:web:291c3806c8345429d6f131"
};

// 4. Nyalakan Mesin Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

// 5. Export mesin ini
export { app, auth, db };