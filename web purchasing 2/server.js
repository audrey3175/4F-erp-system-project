const express = require('express');
const app = express();
const PORT = 3000;

// Middleware agar backend bisa membaca data JSON yang dikirim oleh frontend nanti
app.use(express.json());

// DATABASE SEMENTARA (Disimpan di memori server, berbasis data dari data-mock.js kamu)
let databasePR = [
    { id: 'PR-2026-001', date: '2026-06-01', dept: 'Produksi', requester: 'Budi', item: 'Minyak Goreng Sawit', qty: '500 L', status: 'Disetujui' },
    { id: 'PR-2026-002', date: '2026-06-03', dept: 'QA', requester: 'Siti', item: 'Lab Reagent Pack', qty: '12 Unit', status: 'Diproses' },
    { id: 'PR-2026-003', date: '2026-06-05', dept: 'Gudang', requester: 'Andi', item: 'Pallet Kayu H1', qty: '50 Pcs', status: 'Tertunda' }
];

// ==========================================
// 1. ENDPOINT GET: Mengirimkan data ke Frontend
// ==========================================
app.get('/api/pr', (req, res) => {
    res.json(databasePR);
});

// ==========================================
// 2. ENDPOINT POST: Menerima data PR baru dari Form Frontend
// ==========================================
app.post('/api/pr', (req, res) => {
    const prBaru = req.body;

    // Validasi dasar agar data tidak kosong
    if (!prBaru.item || !prBaru.qty) {
        return res.status(400).json({ success: false, message: "Nama item dan jumlah wajib diisi!" });
    }

    // Berikan ID otomatis & tanggal otomatis ala server
    const formatPR = {
        id: `PR-2026-0${databasePR.length + 1}`,
        date: new Date().toISOString().split('T')[0],
        dept: prBaru.dept || 'Purchasing',
        requester: prBaru.requester || 'Nadya',
        item: prBaru.item,
        qty: prBaru.qty,
        status: 'Tertunda'
    };

    databasePR.unshift(formatPR); // Masukkan ke urutan paling atas
    res.status(201).json({ success: true, message: "Sukses menyimpan PR baru di server!", data: formatPR });
});

// Jalankan Server
app.listen(PORT, () => {
    console.log(`====================================================`);
    console.log(`🚀 Server Back-End FoodSync sukses berjalan!`);
    console.log(`🔗 Akses API data PR di: http://localhost:${PORT}/api/pr`);
    console.log(`====================================================`);
});