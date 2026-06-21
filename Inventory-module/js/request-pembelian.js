import { db } from './firebase-config.js';
import { 
    collection, 
    doc, 
    setDoc, 
    updateDoc,
    getDoc,
    getDocs, 
    onSnapshot 
} from "https://www.gstatic.com/firebasejs/12.15.0/firebase-firestore.js";

// List bahan baku untuk dropdown dinamis
let rawMaterialsList = [];

// Load daftar bahan baku dari stok_barang
async function loadRawMaterials() {
    try {
        const querySnapshot = await getDocs(collection(db, "stok_barang"));
        rawMaterialsList = [];
        querySnapshot.forEach((doc) => {
            const data = doc.data();
            if (data.kategori === "Raw Material") {
                rawMaterialsList.push(data);
            }
        });
    } catch (error) {
        console.error("Gagal memuat daftar bahan baku:", error);
    }
}

let allPrs = [];

function applyFilters() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
    const filterTanggalValue = document.getElementById('filterTanggal').value;
    const filterStatusValue = document.getElementById('filterStatus').value;
    const filterVendorValue = document.getElementById('filterVendor').value;

    const filtered = allPrs.filter(pr => {
        const noPrMatch = pr.no_pr.toLowerCase().includes(searchValue);
        const vendorMatch = pr.vendor.toLowerCase().includes(searchValue);
        const searchMatch = noPrMatch || vendorMatch;

        let dateMatch = true;
        if (filterTanggalValue) {
            const prDateStr = new Date(pr.tanggal).toISOString().split('T')[0];
            dateMatch = prDateStr === filterTanggalValue;
        }

        let statusMatch = true;
        if (filterStatusValue) {
            statusMatch = pr.status === filterStatusValue;
        }

        let vendorFilterMatch = true;
        if (filterVendorValue) {
            vendorFilterMatch = pr.vendor === filterVendorValue;
        }

        return searchMatch && dateMatch && statusMatch && vendorFilterMatch;
    });

    if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>';
        return;
    }

    filtered.forEach((pr) => {
        const tr = document.createElement('tr');
        const tanggalLokal = new Date(pr.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric'
        });

        let badgeStatus = 'badge-warning';
        if (pr.status === 'Disetujui') badgeStatus = 'badge-success';
        else if (pr.status === 'Ditolak') badgeStatus = 'badge-danger';

        tr.innerHTML = `
            <td class="fw-bold">${pr.no_pr}</td>
            <td>${tanggalLokal}</td>
            <td>${pr.vendor}</td>
            <td>${pr.items.length} Item</td>
            <td>-</td>
            <td><span class="badge ${badgeStatus}">${pr.status}</span></td>
            <td>${pr.dibuat_oleh}</td>
            <td class="action-icons">
                <i class="fas fa-eye text-blue" onclick="bukaDetailPr('${pr.no_pr}')"></i>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

window.resetFilters = function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterVendor').value = '';
    applyFilters();
};

// Render tabel Request Pembelian dari Firestore secara real-time
function listenToPr() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Memuat data...</td></tr>';

    onSnapshot(collection(db, "request_pembelian"), (querySnapshot) => {
        allPrs = [];
        let countTotal = 0;
        let countDisetujui = 0;
        let countMenunggu = 0;
        let countDitolak = 0;

        querySnapshot.forEach((docSnap) => {
            const pr = docSnap.data();
            allPrs.push(pr);
            
            countTotal++;
            if (pr.status === "Disetujui") countDisetujui++;
            else if (pr.status === "Menunggu") countMenunggu++;
            else if (pr.status === "Ditolak") countDitolak++;
        });

        // Urutkan berdasarkan tanggal terbaru
        allPrs.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

        // Populate filterVendor dynamically
        const filterVendor = document.getElementById('filterVendor');
        if (filterVendor) {
            const currentSelected = filterVendor.value;
            const uniqueVendors = [...new Set(allPrs.map(pr => pr.vendor))].sort();
            filterVendor.innerHTML = '<option value="">Semua Vendor</option>';
            uniqueVendors.forEach(vendor => {
                const opt = document.createElement('option');
                opt.value = vendor;
                opt.innerText = vendor;
                if (vendor === currentSelected) {
                    opt.selected = true;
                }
                filterVendor.appendChild(opt);
            });
        }

        updateKpis(countTotal, countDisetujui, countMenunggu, countDitolak);
        applyFilters();
    }, (error) => {
        console.error("Gagal memuat data PR:", error);
    });
}

function updateKpis(total, disetujui, menunggu, ditolak) {
    const elTotal = document.getElementById('kpiTotalPr');
    const elDisetujui = document.getElementById('kpiDisetujui');
    const elMenunggu = document.getElementById('kpiMenunggu');
    const elDitolak = document.getElementById('kpiDitolak');
    const elTotalPo = document.getElementById('kpiTotalPo');
    const elPoTerkirim = document.getElementById('kpiPoTerkirim');

    if (elTotal) elTotal.innerText = total;
    if (elDisetujui) elDisetujui.innerText = disetujui;
    if (elMenunggu) elMenunggu.innerText = menunggu;
    if (elDitolak) elDitolak.innerText = ditolak;
    
    if (elTotalPo) elTotalPo.innerText = disetujui;
    if (elPoTerkirim) elPoTerkirim.innerText = disetujui;
}

// Inisialisasi halaman
document.addEventListener("DOMContentLoaded", () => {
    loadRawMaterials().then(() => {
        listenToPr();
    });
    
    const inputTgl = document.getElementById('prTanggal');
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

    const filterStatus = document.getElementById('filterStatus');
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);

    const filterVendor = document.getElementById('filterVendor');
    if (filterVendor) filterVendor.addEventListener('change', applyFilters);
});

// Menambahkan baris item dinamis ke formulir
window.tambahBarisItem = function() {
    const container = document.getElementById('prItemsContainer');
    const rowId = 'row_' + Date.now();

    const itemOptions = rawMaterialsList.map(item => 
        `<option value="${item.kode_barang}">${item.nama_barang} (${item.satuan})</option>`
    ).join('');

    const rowHtml = `
        <div class="items-row" id="${rowId}">
            <select class="form-input flex-2 gr-item-select">
                <option value="">-- Pilih Barang --</option>
                ${itemOptions}
            </select>
            <input type="number" class="form-input flex-1 text-center gr-item-qty" placeholder="0" min="1">
            <div class="w-50px text-center">
                <i class="fas fa-trash text-red cursor-pointer" onclick="hapusBarisItem('${rowId}')"></i>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', rowHtml);
};

window.hapusBarisItem = function(rowId) {
    const row = document.getElementById(rowId);
    if (row) row.remove();
};

// Menyimpan data Purchase Request ke Firestore
window.simpanPr = async function() {
    const vendor = document.getElementById('prVendor').value;
    const tanggal = document.getElementById('prTanggal').value;
    const catatan = document.getElementById('prCatatan').value.trim();
    const container = document.getElementById('prItemsContainer');
    const rows = container.getElementsByClassName('items-row');

    if (!vendor || !tanggal || rows.length === 0) {
        alert("Mohon lengkapi semua data dan tambahkan minimal 1 item!");
        return;
    }

    const items = [];
    for (let row of rows) {
        const select = row.querySelector('.gr-item-select');
        const qtyInput = row.querySelector('.gr-item-qty');

        const kode_barang = select.value;
        const qty = parseInt(qtyInput.value);

        if (!kode_barang || isNaN(qty) || qty <= 0) {
            alert("Harap pilih barang dan masukkan jumlah Qty yang valid!");
            return;
        }

        const material = rawMaterialsList.find(m => m.kode_barang === kode_barang);
        items.push({
            kode_barang,
            nama_barang: material ? material.nama_barang : "Bahan Baku",
            qty
        });
    }

    // Generate Nomor PR unik
    const randomSuffix = Math.floor(100 + Math.random() * 900);
    const dateStr = tanggal.replace(/-/g, '').substring(2);
    const noPr = `PR-${dateStr}-${randomSuffix}`;

    const activeUser = JSON.parse(localStorage.getItem('foodsync_active_user')) || {};
    const namaUser = activeUser.firstName ? `${activeUser.firstName} ${activeUser.lastName}` : "Staff Gudang";

    const dataPr = {
        no_pr: noPr,
        vendor: vendor,
        tanggal: new Date(tanggal).toISOString(),
        catatan: catatan,
        items: items,
        status: "Menunggu", // Status default request baru
        dibuat_oleh: namaUser
    };

    try {
        // Simpan dokumen Purchase Request ke Firestore
        await setDoc(doc(db, "request_pembelian", noPr), dataPr);

        alert(`Request pembelian ${noPr} berhasil disimpan!`);
        closeModal();
        // Reset form
        document.getElementById('prCatatan').value = '';
        container.innerHTML = '';

    } catch (error) {
        console.error("Gagal menyimpan PR:", error);
        alert("Terjadi kesalahan saat memproses data.");
    }
};

// Menampilkan Detail PR
window.bukaDetailPr = async function(prId) {
    try {
        const docSnap = await getDoc(doc(db, "request_pembelian", prId));
        if (!docSnap.exists()) return;

        const pr = docSnap.data();
        
        // Isi header detail
        document.getElementById('detPrNo').innerText = pr.no_pr;
        document.getElementById('detPrTanggal').innerText = new Date(pr.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
        document.getElementById('detPrVendor').innerText = pr.vendor;

        // Render tabel detail
        const tableBody = document.getElementById('detPrTableBody');
        tableBody.innerHTML = '';

        pr.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold">${item.nama_barang} (${item.kode_barang})</td>
                <td class="text-center text-blue fw-bold">${item.qty}</td>
            `;
            tableBody.appendChild(tr);
        });

        // Tampilkan tombol approval jika statusnya Menunggu
        const approvalActions = document.getElementById('approvalActions');
        if (approvalActions) {
            if (pr.status === "Menunggu") {
                approvalActions.innerHTML = `
                    <button type="button" class="btn-primary" style="background-color: #10b981; color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.2s;" onclick="accPr('${prId}', 'Disetujui')"><i class="fas fa-check"></i> Setujui</button>
                    <button type="button" class="btn-outline" style="background-color: #ef4444; color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 14px; transition: all 0.2s;" onclick="accPr('${prId}', 'Ditolak')"><i class="fas fa-times"></i> Tolak</button>
                `;
            } else {
                const badgeClass = pr.status === 'Disetujui' ? 'badge-success' : 'badge-danger';
                approvalActions.innerHTML = `
                    <span class="badge ${badgeClass}" style="padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 14px;">Status: ${pr.status}</span>
                `;
            }
        }

        openModalDetail();
    } catch (error) {
        console.error("Gagal memuat detail PR:", error);
    }
};

window.accPr = async function(prId, statusBaru) {
    try {
        const prRef = doc(db, "request_pembelian", prId);
        await updateDoc(prRef, {
            status: statusBaru
        });
        alert(`Status Request Pembelian ${prId} berhasil diubah menjadi: ${statusBaru}`);
        closeModalDetail();
    } catch (error) {
        console.error("Gagal memperbarui status PR:", error);
        alert("Terjadi kesalahan saat memproses persetujuan.");
    }
};

// Eksis modal control dari HTML
window.openModal = function() {
    document.getElementById('prModal').style.display = 'flex';
    const container = document.getElementById('prItemsContainer');
    if (container.children.length === 0) {
        tambahBarisItem();
    }
};
window.closeModal = function() {
    document.getElementById('prModal').style.display = 'none';
};
window.openModalDetail = function() {
    document.getElementById('detailModal').style.display = 'flex';
};
window.closeModalDetail = function() {
    document.getElementById('detailModal').style.display = 'none';
};