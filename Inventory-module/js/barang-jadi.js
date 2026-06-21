import { db } from './firebase-config.js';
import { 
    collection, 
    doc, 
    setDoc, 
    updateDoc,
    getDoc,
    getDocs, 
    onSnapshot,
    increment
} from "https://www.gstatic.com/firebasejs/12.15.0/firebase-firestore.js";

// List produk barang jadi untuk dropdown dinamis
let fgProductsList = [];

// Load daftar barang jadi dari stok_barang
async function loadFgProducts() {
    try {
        const querySnapshot = await getDocs(collection(db, "stok_barang"));
        fgProductsList = [];
        querySnapshot.forEach((doc) => {
            const data = doc.data();
            if (data.kategori === "Finished Good") {
                fgProductsList.push(data);
            }
        });
        
        // Isi dropdown di formulir
        const selectEl = document.getElementById('fgProductSelect');
        if (selectEl) {
            selectEl.innerHTML = '<option value="">-- Pilih Produk Jadi --</option>';
            fgProductsList.forEach(prod => {
                selectEl.innerHTML += `<option value="${prod.kode_barang}">${prod.nama_barang} (${prod.satuan})</option>`;
            });
        }

        // Populate sidebar stok per produk secara dinamis
        const fgStockList = document.getElementById('fgStockList');
        if (fgStockList) {
            fgStockList.innerHTML = '';
            let stokAmanCount = 0;
            let totalNilaiValuation = 0;

            fgProductsList.forEach(prod => {
                const li = document.createElement('li');
                const isWarning = prod.stok <= prod.minimum;
                if (!isWarning) stokAmanCount++;

                const itemValuation = prod.stok * 100000; // Estimasi Rp 100,000 per unit/dus
                totalNilaiValuation += itemValuation;

                li.innerHTML = `
                    <span>${prod.nama_barang}</span>
                    <strong class="${isWarning ? 'text-warning' : ''}">${prod.stok.toLocaleString('id-ID')} <small>${prod.satuan}</small></strong>
                `;
                fgStockList.appendChild(li);
            });

            const elStokAman = document.getElementById('kpiStokAman');
            if (elStokAman) elStokAman.innerText = stokAmanCount;

            const elTotalNilai = document.getElementById('kpiTotalNilai');
            if (elTotalNilai) {
                if (totalNilaiValuation >= 1000000) {
                    elTotalNilai.innerText = (totalNilaiValuation / 1000000).toFixed(1) + "JT";
                } else {
                    elTotalNilai.innerText = totalNilaiValuation.toLocaleString('id-ID');
                }
            }
        }
    } catch (error) {
        console.error("Gagal memuat daftar produk jadi:", error);
    }
}

let allFgs = [];

function updateFgKpisAndSidebar(fgList) {
    const totalProduksi = fgList.length;
    let fgMasukTotal = 0;
    
    let qtyA1 = 0;
    let qtyA2 = 0;
    let qtyB1 = 0;

    fgList.forEach(fg => {
        fgMasukTotal += fg.qty;
        
        const gName = fg.gudang.toUpperCase();
        if (gName.includes("A1")) {
            qtyA1 += fg.qty;
        } else if (gName.includes("A2")) {
            qtyA2 += fg.qty;
        } else if (gName.includes("B1")) {
            qtyB1 += fg.qty;
        }
    });

    const elTotalProd = document.getElementById('kpiTotalProduksi');
    const elFgMasuk = document.getElementById('kpiFgMasuk');
    const elFgKeluar = document.getElementById('kpiFgKeluar');

    if (elTotalProd) elTotalProd.innerText = totalProduksi;
    if (elFgMasuk) elFgMasuk.innerText = fgMasukTotal.toLocaleString('id-ID');
    if (elFgKeluar) elFgKeluar.innerText = "0";

    const capA1 = Math.min(100, Math.round((qtyA1 / 10000) * 100));
    const capA2 = Math.min(100, Math.round((qtyA2 / 10000) * 100));
    const capB1 = Math.min(100, Math.round((qtyB1 / 10000) * 100));

    const elCapA1 = document.getElementById('capGudangA1');
    const elBarA1 = document.getElementById('barGudangA1');
    if (elCapA1 && elBarA1) {
        elCapA1.innerText = `${capA1}%`;
        elBarA1.style.width = `${capA1}%`;
    }

    const elCapA2 = document.getElementById('capGudangA2');
    const elBarA2 = document.getElementById('barGudangA2');
    if (elCapA2 && elBarA2) {
        elCapA2.innerText = `${capA2}%`;
        elBarA2.style.width = `${capA2}%`;
    }

    const elCapB1 = document.getElementById('capGudangB1');
    const elBarB1 = document.getElementById('barGudangB1');
    if (elCapB1 && elBarB1) {
        elCapB1.innerText = `${capB1}%`;
        elBarB1.style.width = `${capB1}%`;
    }
}

function applyFilters() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
    const filterTanggalValue = document.getElementById('filterTanggal').value;
    const filterGudangValue = document.getElementById('filterGudang').value;

    const filtered = allFgs.filter(fg => {
        const noFgMatch = fg.no_fg.toLowerCase().includes(searchValue);
        const produkMatch = fg.nama_produk.toLowerCase().includes(searchValue);
        const searchMatch = noFgMatch || produkMatch;

        let dateMatch = true;
        if (filterTanggalValue) {
            const fgDateStr = new Date(fg.tanggal).toISOString().split('T')[0];
            dateMatch = fgDateStr === filterTanggalValue;
        }

        let gudangFilterMatch = true;
        if (filterGudangValue) {
            gudangFilterMatch = fg.gudang === filterGudangValue;
        }

        return searchMatch && dateMatch && gudangFilterMatch;
    });

    if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>';
        return;
    }

    filtered.forEach((fg) => {
        const tr = document.createElement('tr');
        const tanggalLokal = new Date(fg.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric'
        });

        tr.innerHTML = `
            <td class="fw-bold">${fg.no_fg}</td>
            <td>${tanggalLokal}</td>
            <td>${fg.nama_produk}</td>
            <td>${fg.batch}</td>
            <td>${fg.qty.toLocaleString('id-ID')}</td>
            <td><span class="badge badge-success">Tersedia</span></td>
            <td>${fg.gudang}</td>
            <td class="action-icons">
                <i class="fas fa-eye text-blue" onclick="bukaDetailFg('${fg.no_fg}')"></i>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

window.resetFilters = function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterGudang').value = '';
    applyFilters();
};

// Render tabel Barang Jadi dari Firestore secara real-time
function listenToFg() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Memuat data...</td></tr>';

    onSnapshot(collection(db, "barang_jadi"), (querySnapshot) => {
        allFgs = [];
        querySnapshot.forEach((docSnap) => {
            allFgs.push(docSnap.data());
        });

        // Urutkan berdasarkan tanggal terbaru
        allFgs.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

        updateFgKpisAndSidebar(allFgs);
        applyFilters();
    }, (error) => {
        console.error("Gagal memuat data FG:", error);
    });
}

// Inisialisasi halaman
document.addEventListener("DOMContentLoaded", () => {
    loadFgProducts().then(() => {
        listenToFg();
    });
    
    // Set default tanggal hari ini
    const inputTgl = document.getElementById('fgTanggal');
    if (inputTgl) {
        inputTgl.value = new Date().toISOString().split('T')[0];
    }

    const filterTgl = document.getElementById('filterTanggal');
    if (filterTgl) {
        filterTgl.value = '';
    }

    // Pasang listener untuk filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.addEventListener('input', applyFilters);

    const filterTanggal = document.getElementById('filterTanggal');
    if (filterTanggal) filterTanggal.addEventListener('change', applyFilters);

    const filterGudang = document.getElementById('filterGudang');
    if (filterGudang) filterGudang.addEventListener('change', applyFilters);
});

// Menyimpan data Finished Goods
window.simpanFg = async function() {
    const productSelect = document.getElementById('fgProductSelect');
    const kode_barang = productSelect.value;
    const qty = parseInt(document.getElementById('fgQty').value);
    const batch = document.getElementById('fgBatch').value.trim();
    const tanggal = document.getElementById('fgTanggal').value;
    const gudang = document.getElementById('fgGudang').value;
    const catatan = document.getElementById('fgCatatan').value.trim();

    if (!kode_barang || isNaN(qty) || qty <= 0 || !batch || !tanggal || !gudang) {
        alert("Mohon lengkapi semua isian data dengan benar!");
        return;
    }

    const selectedProduct = fgProductsList.find(p => p.kode_barang === kode_barang);
    const nama_produk = selectedProduct ? selectedProduct.nama_barang : "Barang Jadi";

    // Generate Nomor FG unik
    const randomSuffix = Math.floor(100 + Math.random() * 900);
    const dateStr = tanggal.replace(/-/g, '').substring(2);
    const noFg = `FG-${dateStr}-${randomSuffix}`;

    const dataFg = {
        no_fg: noFg,
        kode_barang: kode_barang,
        nama_produk: nama_produk,
        qty: qty,
        batch: batch,
        tanggal: new Date(tanggal).toISOString(),
        gudang: gudang,
        catatan: catatan
    };

    try {
        // 1. Simpan dokumen Barang Jadi ke Firestore
        await setDoc(doc(db, "barang_jadi", noFg), dataFg);

        // 2. Tambah stok barang jadi di stok_barang
        const stokRef = doc(db, "stok_barang", kode_barang);
        await updateDoc(stokRef, {
            stok: increment(qty)
        });

        // 3. Tulis log mutasi (Masuk)
        const mutationId = `MT-${Date.now()}-${Math.floor(100 + Math.random() * 900)}`;
        const dataMutasi = {
            no_mutasi: mutationId,
            tanggal: new Date(tanggal).toISOString(),
            jenis: "Masuk",
            kode_barang: kode_barang,
            nama_barang: nama_produk,
            dari: "Divisi Produksi",
            ke: gudang,
            qty: qty,
            no_referensi: noFg
        };
        await setDoc(doc(db, "mutasi", mutationId), dataMutasi);

        alert(`Hasil produksi ${noFg} berhasil dimasukkan ke gudang!`);
        closeModalForm();
        // Reset form
        document.getElementById('fgQty').value = '';
        document.getElementById('fgBatch').value = '';
        document.getElementById('fgCatatan').value = '';
        productSelect.value = '';

    } catch (error) {
        console.error("Gagal menyimpan barang jadi:", error);
        alert("Terjadi kesalahan saat memproses data.");
    }
};

// Menampilkan Detail Barang Jadi
window.bukaDetailFg = async function(fgId) {
    try {
        const docSnap = await getDoc(doc(db, "barang_jadi", fgId));
        if (!docSnap.exists()) return;

        const fg = docSnap.data();
        
        // Isi header detail
        document.getElementById('detFgNo').innerText = fg.no_fg;
        document.getElementById('detFgTanggal').innerText = new Date(fg.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
        document.getElementById('detFgGudang').innerText = fg.gudang;

        // Render tabel detail
        document.getElementById('detFgNamaProduk').innerText = fg.nama_produk;
        document.getElementById('detFgBatch').innerText = fg.batch;
        document.getElementById('detFgQty').innerText = fg.qty.toLocaleString('id-ID');

        openModalDetail();
    } catch (error) {
        console.error("Gagal memuat detail FG:", error);
    }
};

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