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

// List bahan baku untuk dropdown dinamis & pengecekan stok
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

// Render tabel Pengeluaran Produksi dari Firestore secara real-time
let allIssues = [];

function updateIssueKpisAndSidebar(issuesList) {
    const totalIssue = issuesList.length;
    let disetujuiCount = totalIssue;
    let menungguCount = 0;
    let ditolakCount = 0;
    let totalMaterialQty = 0;

    let qtyMie = 0;
    let qtyBumbu = 0;
    let qtyPacking = 0;

    issuesList.forEach(iss => {
        const issQty = iss.items.reduce((sum, i) => sum + i.qty, 0);
        totalMaterialQty += issQty;

        const deptLower = iss.departemen.toLowerCase();
        if (deptLower.includes("mie")) {
            qtyMie += issQty;
        } else if (deptLower.includes("bumbu")) {
            qtyBumbu += issQty;
        } else if (deptLower.includes("packing") || deptLower.includes("pack")) {
            qtyPacking += issQty;
        }
    });

    const elTotal = document.getElementById('kpiTotalIssue');
    const elDisetujui = document.getElementById('kpiDisetujui');
    const elMenunggu = document.getElementById('kpiMenunggu');
    const elDitolak = document.getElementById('kpiDitolak');
    const elTotalMat = document.getElementById('kpiTotalMaterial');

    if (elTotal) elTotal.innerText = totalIssue;
    if (elDisetujui) elDisetujui.innerText = disetujuiCount;
    if (elMenunggu) elMenunggu.innerText = menungguCount;
    if (elDitolak) elDitolak.innerText = ditolakCount;
    
    if (elTotalMat) {
        if (totalMaterialQty >= 1000000) {
            elTotalMat.innerText = (totalMaterialQty / 1000000).toFixed(1) + "JT";
        } else {
            elTotalMat.innerText = totalMaterialQty.toLocaleString('id-ID');
        }
    }

    const elSummaryTotal = document.getElementById('summaryTotalIssue');
    if (elSummaryTotal) elSummaryTotal.innerText = totalIssue;
    const elSummaryDalamProses = document.getElementById('summaryDalamProses');
    if (elSummaryDalamProses) elSummaryDalamProses.innerText = "0";
    const elSummaryMenunggu = document.getElementById('summaryMenunggu');
    if (elSummaryMenunggu) elSummaryMenunggu.innerText = "0";

    const totalDepQty = qtyMie + qtyBumbu + qtyPacking;
    const pctMie = totalDepQty > 0 ? Math.round((qtyMie / totalDepQty) * 100) : 0;
    const pctBumbu = totalDepQty > 0 ? Math.round((qtyBumbu / totalDepQty) * 100) : 0;
    const pctPacking = totalDepQty > 0 ? Math.round((qtyPacking / totalDepQty) * 100) : 0;

    const elValMie = document.getElementById('valMie');
    const elBarMie = document.getElementById('barMie');
    if (elValMie && elBarMie) {
        elValMie.innerText = `${pctMie}%`;
        elBarMie.style.width = `${pctMie}%`;
    }

    const elValBumbu = document.getElementById('valBumbu');
    const elBarBumbu = document.getElementById('barBumbu');
    if (elValBumbu && elBarBumbu) {
        elValBumbu.innerText = `${pctBumbu}%`;
        elBarBumbu.style.width = `${pctBumbu}%`;
    }

    const elValPacking = document.getElementById('valPacking');
    const elBarPacking = document.getElementById('barPacking');
    if (elValPacking && elBarPacking) {
        elValPacking.innerText = `${pctPacking}%`;
        elBarPacking.style.width = `${pctPacking}%`;
    }
}

function applyFilters() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
    const filterTanggalValue = document.getElementById('filterTanggal').value;
    const filterDeptValue = document.getElementById('filterDept').value;

    const filtered = allIssues.filter(issue => {
        const noIssueMatch = issue.no_issue.toLowerCase().includes(searchValue);
        const deptMatch = issue.departemen.toLowerCase().includes(searchValue);
        const searchMatch = noIssueMatch || deptMatch;

        let dateMatch = true;
        if (filterTanggalValue) {
            const issueDateStr = new Date(issue.tanggal).toISOString().split('T')[0];
            dateMatch = issueDateStr === filterTanggalValue;
        }

        let deptFilterMatch = true;
        if (filterDeptValue) {
            deptFilterMatch = issue.departemen === filterDeptValue;
        }

        return searchMatch && dateMatch && deptFilterMatch;
    });

    if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>';
        return;
    }

    filtered.forEach((issue) => {
        const tr = document.createElement('tr');
        const tanggalLokal = new Date(issue.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric'
        });
        const totalQty = issue.items.reduce((sum, item) => sum + item.qty, 0);

        tr.innerHTML = `
            <td class="fw-bold">${issue.no_issue}</td>
            <td>${tanggalLokal}</td>
            <td>${issue.departemen}</td>
            <td>${issue.items.length} Item</td>
            <td>${totalQty.toLocaleString('id-ID')}</td>
            <td><span class="badge badge-success">Selesai</span></td>
            <td>${issue.dibuat_oleh || "Staff Gudang"}</td>
            <td class="action-icons">
                <i class="fas fa-eye text-blue" onclick="bukaDetailIssue('${issue.no_issue}')"></i>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

window.resetFilters = function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterDept').value = '';
    applyFilters();
};

function listenToIssues() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Memuat data...</td></tr>';

    onSnapshot(collection(db, "pengeluaran_produksi"), (querySnapshot) => {
        allIssues = [];
        querySnapshot.forEach((docSnap) => {
            allIssues.push(docSnap.data());
        });

        // Urutkan berdasarkan tanggal terbaru
        allIssues.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

        // Populate filterDept dropdown dynamically
        const filterDept = document.getElementById('filterDept');
        if (filterDept) {
            const currentSelected = filterDept.value;
            const uniqueDepts = [...new Set(allIssues.map(iss => iss.departemen))].sort();
            filterDept.innerHTML = '<option value="">Semua Departemen</option>';
            uniqueDepts.forEach(dept => {
                const opt = document.createElement('option');
                opt.value = dept;
                opt.innerText = dept;
                if (dept === currentSelected) {
                    opt.selected = true;
                }
                filterDept.appendChild(opt);
            });
        }

        updateIssueKpisAndSidebar(allIssues);
        applyFilters();
    }, (error) => {
        console.error("Gagal memuat data Issue:", error);
    });
}

// Inisialisasi halaman
document.addEventListener("DOMContentLoaded", () => {
    loadRawMaterials().then(() => {
        listenToIssues();
    });
    
    // Set default tanggal hari ini
    const inputTgl = document.getElementById('issTanggal');
    if (inputTgl) {
        inputTgl.value = new Date().toISOString().split('T')[0];
    }

    const filterTgl = document.getElementById('filterTanggal');
    if (filterTgl) {
        filterTgl.value = ''; // Biarkan kosong agar default menampilkan semua tanggal
    }

    // Pasang listener untuk filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.addEventListener('input', applyFilters);

    const filterTanggal = document.getElementById('filterTanggal');
    if (filterTanggal) filterTanggal.addEventListener('change', applyFilters);

    const filterDept = document.getElementById('filterDept');
    if (filterDept) filterDept.addEventListener('change', applyFilters);
});

// Menambahkan baris item dinamis ke formulir
window.tambahBarisItem = function() {
    const container = document.getElementById('issItemsContainer');
    const rowId = 'row_' + Date.now();

    const itemOptions = rawMaterialsList.map(item => 
        `<option value="${item.kode_barang}">${item.nama_barang} (Stok: ${item.stok})</option>`
    ).join('');

    const rowHtml = `
        <div class="items-row" id="${rowId}">
            <select class="form-input flex-2 gr-item-select" onchange="updateInfoStokBaris('${rowId}')">
                <option value="">-- Pilih Material --</option>
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

// Menyimpan data Material Issue & validasi kelebihan stok
window.simpanIssue = async function() {
    // Muat ulang data material untuk mendapatkan stok terbaru sebelum submit
    await loadRawMaterials();

    const dept = document.getElementById('issDept').value.trim();
    const tanggal = document.getElementById('issTanggal').value;
    const catatan = document.getElementById('issCatatan').value.trim();
    const container = document.getElementById('issItemsContainer');
    const rows = container.getElementsByClassName('items-row');

    if (!dept || !tanggal || rows.length === 0) {
        alert("Mohon lengkapi semua data dan tambahkan minimal 1 material!");
        return;
    }

    const items = [];
    for (let row of rows) {
        const select = row.querySelector('.gr-item-select');
        const qtyInput = row.querySelector('.gr-item-qty');

        const kode_barang = select.value;
        const qty = parseInt(qtyInput.value);

        if (!kode_barang || isNaN(qty) || qty <= 0) {
            alert("Harap pilih material dan masukkan jumlah Qty yang valid!");
            return;
        }

        const material = rawMaterialsList.find(m => m.kode_barang === kode_barang);
        if (!material) {
            alert("Material tidak ditemukan di database!");
            return;
        }

        // VALIDASI OVER-ISSUE: Pastikan jumlah pengeluaran tidak melebihi stok di Firestore
        if (qty > material.stok) {
            alert(`Stok tidak mencukupi untuk ${material.nama_barang}! Stok tersedia: ${material.stok}, yang ingin dikeluarkan: ${qty}.`);
            return;
        }

        items.push({
            kode_barang,
            nama_barang: material.nama_barang,
            qty
        });
    }

    // Generate Nomor Issue unik
    const randomSuffix = Math.floor(100 + Math.random() * 900);
    const dateStr = tanggal.replace(/-/g, '').substring(2);
    const noIssue = `ISS-${dateStr}-${randomSuffix}`;

    const activeUser = JSON.parse(localStorage.getItem('foodsync_active_user')) || {};
    const namaUser = activeUser.firstName ? `${activeUser.firstName} ${activeUser.lastName}` : "Staff Gudang";

    const dataIssue = {
        no_issue: noIssue,
        departemen: dept,
        tanggal: new Date(tanggal).toISOString(),
        catatan: catatan,
        items: items,
        dibuat_oleh: namaUser
    };

    try {
        // 1. Simpan dokumen Pengeluaran Produksi
        await setDoc(doc(db, "pengeluaran_produksi", noIssue), dataIssue);

        // 2. Kurangi stok_barang & tulis log mutasi secara paralel
        for (let item of items) {
            // Update stok barang (kurangi)
            const stokRef = doc(db, "stok_barang", item.kode_barang);
            await updateDoc(stokRef, {
                stok: increment(-item.qty)
            });

            // Tulis log mutasi (Out)
            const mutationId = `MT-${Date.now()}-${Math.floor(100 + Math.random() * 900)}`;
            const dataMutasi = {
                no_mutasi: mutationId,
                tanggal: new Date(tanggal).toISOString(),
                jenis: "Keluar",
                kode_barang: item.kode_barang,
                nama_barang: item.nama_barang,
                dari: "Gudang Utama",
                ke: dept,
                qty: item.qty,
                no_referensi: noIssue
            };
            await setDoc(doc(db, "mutasi", mutationId), dataMutasi);
        }

        alert(`Pengeluaran barang ${noIssue} berhasil diproses!`);
        closeModalForm();
        // Reset form
        document.getElementById('issDept').value = '';
        document.getElementById('issCatatan').value = '';
        container.innerHTML = '';

    } catch (error) {
        console.error("Gagal memproses pengeluaran barang:", error);
        alert("Terjadi kesalahan saat memproses data.");
    }
};

// Menampilkan Detail Pengeluaran di Modal
window.bukaDetailIssue = async function(issId) {
    try {
        const docSnap = await getDoc(doc(db, "pengeluaran_produksi", issId));
        if (!docSnap.exists()) return;

        const issue = docSnap.data();
        
        // Isi header detail
        document.getElementById('detIssNo').innerText = issue.no_issue;
        document.getElementById('detIssTanggal').innerText = new Date(issue.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
        document.getElementById('detIssDept').innerText = issue.departemen;

        // Render tabel detail
        const tableBody = document.getElementById('detIssTableBody');
        tableBody.innerHTML = '';

        issue.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold">${item.nama_barang} (${item.kode_barang})</td>
                <td class="text-center text-red fw-bold">${item.qty}</td>
            `;
            tableBody.appendChild(tr);
        });

        openModalDetail();
    } catch (error) {
        console.error("Gagal memuat detail Issue:", error);
    }
};

// Eksis modal control dari HTML
window.openModalForm = function() {
    document.getElementById('formModal').style.display = 'flex';
    const container = document.getElementById('issItemsContainer');
    if (container.children.length === 0) {
        tambahBarisItem();
    }
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

window.updateInfoStokBaris = function(rowId) {
    const row = document.getElementById(rowId);
    if (!row) return;
    const select = row.querySelector('.gr-item-select');
    const qtyInput = row.querySelector('.gr-item-qty');
    const kode_barang = select.value;
    const material = rawMaterialsList.find(m => m.kode_barang === kode_barang);
    if (material) {
        qtyInput.max = material.stok;
        qtyInput.placeholder = `Max: ${material.stok}`;
    } else {
        qtyInput.removeAttribute('max');
        qtyInput.placeholder = '0';
    }
};