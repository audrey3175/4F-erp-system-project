// 1. Import Koneksi Database dari file config-mu
import { db } from './firebase-config.js';
import { 
    collection, 
    doc, 
    setDoc, 
    getDocs, 
    getDoc,
    onSnapshot,
    deleteDoc
} from "https://www.gstatic.com/firebasejs/12.15.0/firebase-firestore.js";

// 2. Data Awal Seeding PT Indofood Tbk
const defaultItems = [
    { kode_barang: "RM-001", nama_barang: "Tepung Terigu Segitiga Biru", kategori: "Raw Material", satuan: "kg", stok: 320, minimum: 500, status: "Kritis", gudang: "Gudang Utama" },
    { kode_barang: "RM-002", nama_barang: "Minyak Goreng Bimoli", kategori: "Raw Material", satuan: "liter", stok: 150, minimum: 400, status: "Kritis", gudang: "Gudang Utama" },
    { kode_barang: "RM-003", nama_barang: "Bumbu Penyedap Indofood", kategori: "Raw Material", satuan: "kg", stok: 85, minimum: 100, status: "Rendah", gudang: "Gudang Utama" },
    { kode_barang: "RM-004", nama_barang: "Garam Gurih Indofood", kategori: "Raw Material", satuan: "kg", stok: 1200, minimum: 500, status: "Aman", gudang: "Gudang Utama" },
    { kode_barang: "RM-005", nama_barang: "Kemasan Sachet Indomie", kategori: "Raw Material", satuan: "pcs", stok: 8000, minimum: 10000, status: "Rendah", gudang: "Gudang Utama" },
    { kode_barang: "RM-006", nama_barang: "Kecap Manis Indofood", kategori: "Raw Material", satuan: "liter", stok: 450, minimum: 300, status: "Aman", gudang: "Gudang Utama" },
    { kode_barang: "RM-007", nama_barang: "Cabai Bubuk Indofood", kategori: "Raw Material", satuan: "kg", stok: 210, minimum: 250, status: "Rendah", gudang: "Gudang Utama" },
    { kode_barang: "RM-008", nama_barang: "Suku Kental Manis Indomilk", kategori: "Raw Material", satuan: "liter", stok: 300, minimum: 200, status: "Aman", gudang: "Gudang Utama" },
    { kode_barang: "RM-009", nama_barang: "Bawang Merah Crispy", kategori: "Raw Material", satuan: "kg", stok: 90, minimum: 150, status: "Rendah", gudang: "Gudang Utama" },
    { kode_barang: "RM-010", nama_barang: "Karton Box Indomie", kategori: "Raw Material", satuan: "pcs", stok: 5400, minimum: 3000, status: "Aman", gudang: "Gudang Utama" },
    { kode_barang: "RM-011", nama_barang: "Plastik Roll Kemasan", kategori: "Raw Material", satuan: "roll", stok: 12, minimum: 20, status: "Kritis", gudang: "Gudang Utama" },
    { kode_barang: "FG-001", nama_barang: "Indomie Goreng Spesial 75g", kategori: "Finished Good", satuan: "dus", stok: 450, minimum: 100, status: "Aman", gudang: "Gudang FG" },
    { kode_barang: "FG-002", nama_barang: "Indomie Rasa Soto Mie 75g", kategori: "Finished Good", satuan: "dus", stok: 320, minimum: 100, status: "Aman", gudang: "Gudang FG" },
    { kode_barang: "FG-003", nama_barang: "Supermi Rasa Ayam Bawang", kategori: "Finished Good", satuan: "dus", stok: 12, minimum: 50, status: "Kritis", gudang: "Gudang FG" },
    { kode_barang: "FG-004", nama_barang: "Indomie Rasa Kari Ayam 72g", kategori: "Finished Good", satuan: "dus", stok: 600, minimum: 100, status: "Aman", gudang: "Gudang FG" },
    { kode_barang: "FG-005", nama_barang: "Pop Mie Rasa Ayam 75g", kategori: "Finished Good", satuan: "dus", stok: 480, minimum: 100, status: "Aman", gudang: "Gudang FG" },
    { kode_barang: "FG-006", nama_barang: "Pop Mie Rasa Baso 75g", kategori: "Finished Good", satuan: "dus", stok: 150, minimum: 50, status: "Aman", gudang: "Gudang FG" },
    { kode_barang: "FG-007", nama_barang: "Sarimi Isi 2 Rasa Soto", kategori: "Finished Good", satuan: "dus", stok: 200, minimum: 50, status: "Aman", gudang: "Gudang FG" },
    { kode_barang: "FG-008", nama_barang: "Club Air Mineral 600ml", kategori: "Finished Good", satuan: "dus", stok: 80, minimum: 50, status: "Aman", gudang: "Gudang FG" }
];

async function checkAndSeedDatabase() {
    try {
        const querySnapshot = await getDocs(collection(db, "stok_barang"));
        if (querySnapshot.empty) {
            console.log("Firestore kosong. Melakukan seeding data awal...");
            for (const item of defaultItems) {
                await setDoc(doc(db, "stok_barang", item.kode_barang), item);
            }
            console.log("Seeding selesai!");
            return;
        }

        // Cek jika ada item default baru yang belum masuk ke database
        for (const item of defaultItems) {
            const docRef = doc(db, "stok_barang", item.kode_barang);
            const docSnap = await getDoc(docRef);
            if (!docSnap.exists()) {
                console.log(`Mendeteksi item default baru: ${item.kode_barang} (${item.nama_barang}). Melakukan seeding...`);
                await setDoc(docRef, item);
            } else {
                // Pastikan nama barang relevan PT Indofood (update nama jika berbeda dari default)
                const currentData = docSnap.data();
                if (currentData.nama_barang !== item.nama_barang) {
                    console.log(`Mengupdate nama barang agar relevan PT Indofood: ${item.kode_barang} -> ${item.nama_barang}`);
                    await updateDoc(docRef, { nama_barang: item.nama_barang });
                }
            }
        }

        // Cek jika ada dokumen yang ID-nya acak (bukan kode_barang), jika ada lakukan migrasi
        let needsMigration = false;
        querySnapshot.forEach((docSnap) => {
            const data = docSnap.data();
            if (docSnap.id !== data.kode_barang) {
                needsMigration = true;
            }
        });

        if (needsMigration) {
            console.log("Mendeteksi ID dokumen acak di Firestore. Menjalankan auto-migrasi ID...");
            const oldDocs = [];
            querySnapshot.forEach((docSnap) => {
                oldDocs.push({ id: docSnap.id, data: docSnap.data() });
            });

            for (const oldDoc of oldDocs) {
                const correctId = oldDoc.data.kode_barang;
                if (correctId) {
                    await setDoc(doc(db, "stok_barang", correctId), oldDoc.data);
                    if (oldDoc.id !== correctId) {
                        await deleteDoc(doc(db, "stok_barang", oldDoc.id));
                    }
                }
            }
            console.log("Auto-migrasi ID dokumen selesai!");
        }
    } catch (error) {
        console.error("Gagal melakukan seeding/migrasi:", error);
    }
}

// 3. Fungsi Utama untuk Memantau Data dari Firestore (Real-time dengan onSnapshot)
let allStokItems = [];
let currentPage = 1;
const itemsPerPage = 8; // Menampilkan 8 baris per halaman

function applyFilters() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
    const filterKategoriValue = document.getElementById('filterKategori').value;
    const filterStatusValue = document.getElementById('filterStatus').value;

    const filtered = allStokItems.filter(item => {
        const kodeMatch = item.kode_barang.toLowerCase().includes(searchValue);
        const namaMatch = item.nama_barang.toLowerCase().includes(searchValue);
        const searchMatch = kodeMatch || namaMatch;

        let kategoriMatch = true;
        if (filterKategoriValue) {
            kategoriMatch = item.kategori === filterKategoriValue;
        }

        let statusMatch = true;
        if (filterStatusValue) {
            let status = "Aman";
            if (item.stok < item.minimum) {
                status = "Kritis";
            } else if (item.stok === item.minimum) {
                status = "Rendah";
            }
            statusMatch = status === filterStatusValue;
        }

        return searchMatch && kategoriMatch && statusMatch;
    });

    if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Barang tidak ditemukan.</td></tr>';
        
        const elPageInfo = document.getElementById('pageInfo');
        if (elPageInfo) elPageInfo.innerText = "Halaman 1 dari 1";
        
        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        if (btnPrev) btnPrev.disabled = true;
        if (btnNext) btnNext.disabled = true;
        return;
    }

    // Pagination Logic
    const totalItems = filtered.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const elPageInfo = document.getElementById('pageInfo');
    if (elPageInfo) elPageInfo.innerText = `Halaman ${currentPage} dari ${totalPages}`;

    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    if (btnPrev) btnPrev.disabled = currentPage === 1;
    if (btnNext) btnNext.disabled = currentPage === totalPages;

    const startIdx = (currentPage - 1) * itemsPerPage;
    const endIdx = startIdx + itemsPerPage;
    const pageItems = filtered.slice(startIdx, endIdx);

    pageItems.forEach((data) => {
        let status = "Aman";
        if (data.stok < data.minimum) {
            status = "Kritis";
        } else if (data.stok === data.minimum) {
            status = "Rendah";
        }

        let badgeKategori = data.kategori === 'Raw Material' ? 'badge-blue' : (data.kategori === 'Finished Good' ? 'badge-green' : 'badge-orange');
        let textWarnaStok = data.stok <= data.minimum ? 'text-danger' : 'text-green';
        let badgeStatus = status === 'Kritis' ? 'badge-danger' : (status === 'Rendah' ? 'badge-warning' : 'badge-success');

        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.onclick = () => showDetail(data.kode_barang, data.nama_barang, data.kategori, data.stok, data.minimum, status, data.satuan);

        tr.innerHTML = `
            <td>${data.kode_barang}</td>
            <td class="fw-bold">${data.nama_barang}</td>
            <td><span class="badge ${badgeKategori}">${data.kategori}</span></td>
            <td>${data.satuan}</td>
            <td class="${textWarnaStok}">${data.stok.toLocaleString('id-ID')}</td>
            <td>${data.minimum.toLocaleString('id-ID')}</td>
            <td><span class="badge ${badgeStatus}">${status}</span></td>
            <td class="action-icons">
                <i class="fas fa-eye text-blue"></i>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

window.changePage = function(dir) {
    currentPage += dir;
    applyFilters();
};

window.resetFilters = function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterKategori').value = '';
    document.getElementById('filterStatus').value = '';
    currentPage = 1;
    applyFilters();
};

function tampilkanDataStok() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Memuat data...</td></tr>'; 

    onSnapshot(collection(db, "stok_barang"), (querySnapshot) => {
        allStokItems = [];
        let hitungTotalItem = 0;
        let hitungTotalStok = 0;
        let hitungStokMin = 0;
        let hitungBawahMin = 0;

        querySnapshot.forEach((docSnap) => {
            const data = docSnap.data(); 
            allStokItems.push(data);

            let status = "Aman";
            if (data.stok < data.minimum) {
                status = "Kritis";
                hitungBawahMin += 1;
            } else if (data.stok === data.minimum) {
                status = "Rendah";
                hitungStokMin += 1;
            }

            hitungTotalItem += 1;
            hitungTotalStok += data.stok;
        });

        // Urutkan alfabetis berdasarkan kode_barang
        allStokItems.sort((a, b) => a.kode_barang.localeCompare(b.kode_barang));

        const elTotalItem = document.getElementById('kpiTotalItem');
        const elTotalStok = document.getElementById('kpiTotalStok');
        const elStokMin = document.getElementById('kpiStokMin');
        const elBawahMin = document.getElementById('kpiBawahMin');

        if (elTotalItem) elTotalItem.innerText = hitungTotalItem;
        if (elTotalStok) elTotalStok.innerText = hitungTotalStok.toLocaleString('id-ID');
        if (elStokMin) elStokMin.innerText = hitungStokMin;
        if (elBawahMin) elBawahMin.innerText = hitungBawahMin;

        applyFilters();
    }, (error) => {
        console.error("Gagal menarik data dari Firebase: ", error);
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Gagal memuat data. Periksa koneksi atau console log.</td></tr>`;
    });
}

// Jalankan Seeding, lalu jalankan pemantauan data & daftarkan listener
checkAndSeedDatabase().then(() => {
    tampilkanDataStok();
});

document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentPage = 1;
            applyFilters();
        });
    }

    const filterKategori = document.getElementById('filterKategori');
    if (filterKategori) {
        filterKategori.addEventListener('change', () => {
            currentPage = 1;
            applyFilters();
        });
    }

    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) {
        filterStatus.addEventListener('change', () => {
            currentPage = 1;
            applyFilters();
        });
    }
});

// ==========================================
// FUNGSI DETAIL PANEL
// ==========================================
window.showDetail = function(kode, nama, kategori, stok, minimum, status, satuan) {
    document.getElementById('detailCard').style.display = 'block';
    document.getElementById('detKode').innerText = kode;
    document.getElementById('detNama').innerText = nama;
    
    const elKat = document.getElementById('detKat');
    const elKatText = document.getElementById('detKatText');
    elKat.innerText = kategori;
    elKatText.innerText = kategori;
    
    if (kategori === 'Raw Material') elKat.className = 'badge badge-blue mt-1';
    else if (kategori === 'Finished Good') elKat.className = 'badge badge-green mt-1';
    else elKat.className = 'badge badge-orange mt-1';

    document.getElementById('detStok').innerText = `${stok.toLocaleString('id-ID')} ${satuan}`;
    document.getElementById('detMin').innerText = `${minimum.toLocaleString('id-ID')} ${satuan}`;

    const elStatus = document.getElementById('detStatus');
    elStatus.innerText = status;
    
    if (status === 'Kritis') elStatus.className = 'badge badge-danger';
    else if (status === 'Rendah') elStatus.className = 'badge badge-warning';
    else elStatus.className = 'badge badge-success';
};

window.hideDetail = function() {
    document.getElementById('detailCard').style.display = 'none';
};