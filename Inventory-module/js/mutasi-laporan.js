import { db } from './firebase-config.js';
import { 
    collection, 
    doc, 
    setDoc, 
    updateDoc, 
    getDocs, 
    getDoc,
    onSnapshot 
} from "https://www.gstatic.com/firebasejs/12.15.0/firebase-firestore.js";

// Daftar harga simulasi untuk Valuasi Persediaan
const simulatedPrices = {
    "RM-001": 12000,   // Tepung Terigu Segitiga Biru (kg)
    "RM-002": 15000,   // Minyak Goreng Bimoli (liter)
    "RM-003": 30000,   // Bumbu Penyedap Indofood (kg)
    "RM-004": 5000,    // Garam Gurih Indofood (kg)
    "RM-005": 500,     // Kemasan Sachet Indomie (pcs)
    "RM-006": 18000,   // Kecap Manis Indofood (liter)
    "RM-007": 25000,   // Cabai Bubuk Indofood (kg)
    "RM-008": 35000,   // Susu Kental Manis Indomilk (liter)
    "RM-009": 28000,   // Bawang Merah Crispy (kg)
    "RM-010": 4000,    // Karton Box Indomie (pcs)
    "RM-011": 120000,  // Plastik Roll Kemasan (roll)
    "FG-001": 100000,  // Indomie Goreng Spesial 75g (dus)
    "FG-002": 100000,  // Indomie Rasa Soto Mie 75g (dus)
    "FG-003": 95000,   // Supermi Rasa Ayam Bawang (dus)
    "FG-004": 98000,   // Indomie Rasa Kari Ayam 72g (dus)
    "FG-005": 97000,   // Pop Mie Rasa Ayam 75g (dus)
    "FG-006": 110000,  // Pop Mie Rasa Baso 75g (dus)
    "FG-007": 110000,  // Sarimi Isi 2 Rasa Soto (dus)
    "FG-008": 85000    // Club Air Mineral 600ml (dus)
};

let allMutations = [];
let allStokItems = [];

// Load daftar barang dari stok_barang
async function loadStokBarangOptions() {
    try {
        const querySnapshot = await getDocs(collection(db, "stok_barang"));
        allStokItems = [];
        
        const mutBarangSelect = document.getElementById('mutBarang');
        if (mutBarangSelect) {
            mutBarangSelect.innerHTML = '<option value="">Pilih Barang</option>';
        }

        querySnapshot.forEach((docSnap) => {
            const data = docSnap.data();
            allStokItems.push(data);
            if (mutBarangSelect) {
                mutBarangSelect.innerHTML += `<option value="${data.kode_barang}">${data.nama_barang} (${data.kode_barang})</option>`;
            }
        });
    } catch (error) {
        console.error("Gagal memuat opsi stok barang:", error);
    }
}

function updateMutasiKpis() {
    const totalMutasi = allMutations.length;
    let totalIn = 0;
    let totalOut = 0;

    allMutations.forEach(mut => {
        if (mut.jenis === 'Masuk') {
            totalIn += mut.qty;
        } else if (mut.jenis === 'Keluar') {
            totalOut += mut.qty;
        }
    });

    // Hitung jumlah item di bawah stok minimum
    let minimumAlertCount = 0;
    let totalValuation = 0;
    allStokItems.forEach(item => {
        if (item.stok <= item.minimum) {
            minimumAlertCount++;
        }
        const price = simulatedPrices[item.kode_barang] || 10000;
        totalValuation += item.stok * price;
    });

    const elTotal = document.getElementById('kpiTotalMutasi');
    const elIn = document.getElementById('kpiTotalIn');
    const elOut = document.getElementById('kpiTotalOut');
    const elMinimum = document.getElementById('kpiMinimum');
    const elValNilai = document.getElementById('kpiTotalNilai');

    if (elTotal) elTotal.innerText = totalMutasi.toLocaleString('id-ID');
    if (elIn) elIn.innerText = totalIn.toLocaleString('id-ID');
    if (elOut) elOut.innerText = totalOut.toLocaleString('id-ID');
    if (elMinimum) elMinimum.innerText = minimumAlertCount.toLocaleString('id-ID');
    
    if (elValNilai) {
        if (totalValuation >= 1000000000) {
            elValNilai.innerText = `Rp ${(totalValuation / 1000000000).toFixed(2)}M`;
        } else if (totalValuation >= 1000000) {
            elValNilai.innerText = `Rp ${(totalValuation / 1000000).toFixed(1)}JT`;
        } else {
            elValNilai.innerText = `Rp ${totalValuation.toLocaleString('id-ID')}`;
        }
    }
}

function updateRecentActivity() {
    const recentActivityList = document.getElementById('recentActivityList');
    if (!recentActivityList) return;

    recentActivityList.innerHTML = '';
    const top3 = allMutations.slice(0, 3);
    
    if (top3.length === 0) {
        recentActivityList.innerHTML = '<li class="text-center py-3 text-muted">Belum ada aktivitas mutasi</li>';
        return;
    }

    top3.forEach(mut => {
        const li = document.createElement('li');
        let iconClass = 'text-blue';
        let actType = 'Mutasi Gudang';
        let qtyPrefix = '';

        if (mut.jenis === 'Masuk') {
            iconClass = 'text-green';
            actType = 'Stok Masuk';
            qtyPrefix = '+';
        } else if (mut.jenis === 'Keluar') {
            iconClass = 'text-red';
            actType = 'Stok Keluar';
            qtyPrefix = '-';
        }

        li.innerHTML = `
            <i class="fas fa-circle ${iconClass}"></i>
            <div><strong>${actType}</strong><small>${mut.nama_barang} ${qtyPrefix}${mut.qty.toLocaleString('id-ID')}</small></div>
        `;
        recentActivityList.appendChild(li);
    });
}

function applyFilters() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
    const filterTanggalValue = document.getElementById('filterTanggal').value;
    const filterJenisValue = document.getElementById('filterJenis').value;
    const filterGudangValue = document.getElementById('filterGudang').value;

    const filtered = allMutations.filter(mut => {
        const noMutMatch = mut.no_mutasi.toLowerCase().includes(searchValue);
        const barangMatch = mut.nama_barang.toLowerCase().includes(searchValue);
        const searchMatch = noMutMatch || barangMatch;

        let dateMatch = true;
        if (filterTanggalValue) {
            const mutDateStr = new Date(mut.tanggal).toISOString().split('T')[0];
            dateMatch = mutDateStr === filterTanggalValue;
        }

        let jenisMatch = true;
        if (filterJenisValue) {
            jenisMatch = mut.jenis === filterJenisValue;
        }

        let gudangMatch = true;
        if (filterGudangValue) {
            gudangMatch = mut.dari === filterGudangValue || mut.ke === filterGudangValue;
        }

        return searchMatch && dateMatch && jenisMatch && gudangMatch;
    });

    if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>';
        return;
    }

    filtered.forEach((mut) => {
        const tr = document.createElement('tr');
        const tanggalLokal = new Date(mut.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric'
        });

        let badgeJenis = 'badge-blue';
        if (mut.jenis === 'Masuk') badgeJenis = 'badge-green';
        else if (mut.jenis === 'Keluar') badgeJenis = 'badge-danger';

        tr.innerHTML = `
            <td class="fw-bold">${mut.no_mutasi}</td>
            <td>${tanggalLokal}</td>
            <td><span class="badge ${badgeJenis}">${mut.jenis}</span></td>
            <td class="fw-bold">${mut.nama_barang} (${mut.kode_barang})</td>
            <td>${mut.dari}</td>
            <td>${mut.ke}</td>
            <td class="fw-bold">${mut.qty}</td>
            <td><span class="badge badge-success">Selesai</span></td>
        `;
        tableBody.appendChild(tr);
    });
}

window.resetFilters = function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterJenis').value = '';
    document.getElementById('filterGudang').value = '';
    applyFilters();
};

// Render log mutasi dari Firestore secara real-time
function listenToMutations() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Memuat data...</td></tr>';

    onSnapshot(collection(db, "mutasi"), (querySnapshot) => {
        allMutations = [];
        querySnapshot.forEach((docSnap) => {
            allMutations.push(docSnap.data());
        });
        
        allMutations.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

        updateMutasiKpis();
        updateRecentActivity();
        applyFilters();
    }, (error) => {
        console.error("Gagal memuat log mutasi:", error);
    });
}

// Menyimpan mutasi transfer barang baru
window.simpanMutasi = async function() {
    const kode_barang = document.getElementById('mutBarang').value;
    const tanggal = document.getElementById('mutTanggal').value;
    const dari = document.getElementById('mutDari').value;
    const ke = document.getElementById('mutKe').value;
    const qty = parseInt(document.getElementById('mutQty').value);
    const catatan = document.getElementById('mutCatatan').value.trim();

    if (!kode_barang || !tanggal || !dari || !ke || isNaN(qty) || qty <= 0) {
        alert("Mohon lengkapi semua isian data dengan benar!");
        return;
    }

    if (dari === ke) {
        alert("Gudang asal dan gudang tujuan tidak boleh sama!");
        return;
    }

    const item = allStokItems.find(i => i.kode_barang === kode_barang);
    if (!item) {
        alert("Barang tidak valid!");
        return;
    }

    // Pengecekan stok di gudang asal
    if (qty > item.stok) {
        alert(`Stok tidak mencukupi! Stok saat ini: ${item.stok} ${item.satuan}.`);
        return;
    }

    const randomSuffix = Math.floor(100 + Math.random() * 900);
    const dateStr = tanggal.replace(/-/g, '').substring(2);
    const mutationId = `MT-${dateStr}-${randomSuffix}`;

    const dataMutasi = {
        no_mutasi: mutationId,
        tanggal: new Date(tanggal).toISOString(),
        jenis: "Transfer",
        kode_barang: kode_barang,
        nama_barang: item.nama_barang,
        dari: dari,
        ke: ke,
        qty: qty,
        no_referensi: mutationId,
        catatan: catatan
    };

    try {
        // 1. Simpan dokumen mutasi log
        await setDoc(doc(db, "mutasi", mutationId), dataMutasi);

        // 2. Perbarui gudang barang di stok_barang (bila dipindahkan penuh, update gudang. Di program sederhana ini kita catat mutasinya & pindah gudangnya di record barang)
        const stokRef = doc(db, "stok_barang", kode_barang);
        await updateDoc(stokRef, {
            gudang: ke
        });

        alert(`Mutasi transfer barang ${mutationId} berhasil diproses!`);
        closeModalForm();
        
        // Reset form
        document.getElementById('mutBarang').value = '';
        document.getElementById('mutDari').value = '';
        document.getElementById('mutKe').value = '';
        document.getElementById('mutQty').value = '';
        document.getElementById('mutCatatan').value = '';

        // Muat ulang opsi
        await loadStokBarangOptions();

    } catch (error) {
        console.error("Gagal memproses mutasi:", error);
        alert("Terjadi kesalahan saat memproses data.");
    }
};

// Membuka modal Laporan Valuasi / Stok Opname secara dinamis
window.bukaValuasiModal = async function() {
    try {
        const querySnapshot = await getDocs(collection(db, "stok_barang"));
        const items = [];
        let totalValuation = 0;

        querySnapshot.forEach(doc => {
            items.push(doc.data());
        });

        // Urutkan berdasarkan kode_barang
        items.sort((a, b) => a.kode_barang.localeCompare(b.kode_barang));

        const detailModal = document.getElementById('detailModal');
        if (!detailModal) return;

        const tableBody = detailModal.querySelector('tbody');
        tableBody.innerHTML = '';

        items.forEach(item => {
            const tr = document.createElement('tr');
            const price = simulatedPrices[item.kode_barang] || 10000;
            const valuation = item.stok * price;
            totalValuation += valuation;

            tr.innerHTML = `
                <td class="fw-bold">${item.nama_barang} (${item.kode_barang})</td>
                <td class="text-center">${item.stok.toLocaleString('id-ID')}</td>
                <td class="text-center">Rp ${price.toLocaleString('id-ID')}</td>
                <td class="text-center fw-bold text-green">Rp ${valuation.toLocaleString('id-ID')}</td>
                <td class="text-center">${item.gudang}</td>
                <td>${item.satuan}</td>
            `;
            tableBody.appendChild(tr);
        });

        // Set total harga di footer modal
        const footerTitle = detailModal.querySelector('.modal-footer h3');
        if (footerTitle) {
            footerTitle.innerText = `Total Valuasi: Rp ${totalValuation.toLocaleString('id-ID')}`;
        }

        openModalDetail();
    } catch (error) {
        console.error("Gagal memuat laporan valuasi:", error);
    }
};

// Inisialisasi halaman
document.addEventListener("DOMContentLoaded", () => {
    loadStokBarangOptions();
    listenToMutations();

    // Set default tanggal hari ini
    const inputTgl = document.getElementById('mutTanggal');
    if (inputTgl) {
        inputTgl.value = new Date().toISOString().split('T')[0];
    }

    const filterTgl = document.getElementById('filterTanggal');
    if (filterTgl) {
        filterTgl.value = '';
    }

    // Pasang listener filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.addEventListener('input', applyFilters);

    const filterTanggal = document.getElementById('filterTanggal');
    if (filterTanggal) filterTanggal.addEventListener('change', applyFilters);

    const filterJenis = document.getElementById('filterJenis');
    if (filterJenis) filterJenis.addEventListener('change', applyFilters);

    const filterGudang = document.getElementById('filterGudang');
    if (filterGudang) filterGudang.addEventListener('change', applyFilters);
});

// Eksis modal control dari HTML
window.openModalForm = function() {
    document.getElementById('formModal').style.display = 'flex';
};
window.closeModalForm = function() {
    document.getElementById('formModal').style.display = 'none';
};
window.openModalDetail = function() {
    document.getElementById('detailModal').style.display = 'flex';
};
window.closeModalDetail = function() {
    document.getElementById('detailModal').style.display = 'none';
};