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

let allGrs = [];
let grChart = null;

function updateGrKpisAndChart(grList) {
    let totalGr = grList.length;
    let sesuaiCount = 0;
    let partialCount = 0;
    let rejectCount = 0;
    let totalItemsQty = 0;

    grList.forEach(gr => {
        let totalQty = 0;
        let totalReject = 0;
        gr.items.forEach(item => {
            totalQty += item.qty;
            totalReject += item.reject || 0;
        });

        totalItemsQty += totalQty;

        if (totalReject === 0) {
            sesuaiCount++;
        } else if (totalReject > 0 && totalReject < totalQty) {
            partialCount++;
        } else {
            rejectCount++;
        }
    });

    const elTotal = document.getElementById('kpiTotalGr');
    const elSesuai = document.getElementById('kpiSesuai');
    const elPartial = document.getElementById('kpiPartial');
    const elReject = document.getElementById('kpiReject');
    const elTotalItem = document.getElementById('kpiTotalItem');
    const elSummaryTotal = document.getElementById('summaryTotalItem');

    if (elTotal) elTotal.innerText = totalGr;
    if (elSesuai) elSesuai.innerText = sesuaiCount;
    if (elPartial) elPartial.innerText = partialCount;
    if (elReject) elReject.innerText = rejectCount;
    if (elTotalItem) elTotalItem.innerText = totalItemsQty.toLocaleString('id-ID');
    if (elSummaryTotal) elSummaryTotal.innerText = totalItemsQty.toLocaleString('id-ID');

    // Update Chart
    const canvas = document.getElementById('grStatusChart');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        if (grChart) {
            grChart.data.datasets[0].data = [sesuaiCount, partialCount, rejectCount];
            grChart.update();
        } else {
            grChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sesuai', 'Partial', 'Reject'],
                    datasets: [{
                        data: [sesuaiCount, partialCount, rejectCount],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                usePointStyle: true,
                                font: { size: 11, family: "'Open Sans', sans-serif" }
                            }
                        }
                    }
                }
            });
        }
    }
}

function applyFilters() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    const searchValue = document.getElementById('searchInput').value.toLowerCase().trim();
    const filterTanggalValue = document.getElementById('filterTanggal').value;
    const filterVendorValue = document.getElementById('filterVendor').value;

    const filtered = allGrs.filter(gr => {
        const noGrMatch = gr.no_gr.toLowerCase().includes(searchValue);
        const vendorMatch = gr.vendor.toLowerCase().includes(searchValue);
        const searchMatch = noGrMatch || vendorMatch;

        let dateMatch = true;
        if (filterTanggalValue) {
            const grDateStr = new Date(gr.tanggal).toISOString().split('T')[0];
            dateMatch = grDateStr === filterTanggalValue;
        }

        let vendorFilterMatch = true;
        if (filterVendorValue) {
            vendorFilterMatch = gr.vendor === filterVendorValue;
        }

        return searchMatch && dateMatch && vendorFilterMatch;
    });

    if (filtered.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Data tidak ditemukan.</td></tr>';
        return;
    }

    filtered.forEach((gr) => {
        const tr = document.createElement('tr');
        const tanggalLokal = new Date(gr.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric'
        });

        tr.innerHTML = `
            <td class="fw-bold">${gr.no_gr}</td>
            <td>${tanggalLokal}</td>
            <td>${gr.no_po}</td>
            <td>${gr.vendor}</td>
            <td>${gr.items.length} Item</td>
            <td><span class="badge badge-success">Selesai</span></td>
            <td>${gr.dibuat_oleh || "Staff Gudang"}</td>
            <td class="action-icons">
                <i class="fas fa-eye text-blue" onclick="bukaDetailGr('${gr.no_gr}')"></i>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

window.resetFilters = function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterTanggal').value = '';
    document.getElementById('filterVendor').value = '';
    applyFilters();
};

function listenToGr() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Memuat data...</td></tr>';

    onSnapshot(collection(db, "penerimaan_bahan"), (querySnapshot) => {
        allGrs = [];
        querySnapshot.forEach((docSnap) => {
            allGrs.push(docSnap.data());
        });

        // Urutkan berdasarkan tanggal terbaru
        allGrs.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

        // Populate filterVendor dynamically
        const filterVendor = document.getElementById('filterVendor');
        if (filterVendor) {
            const currentSelected = filterVendor.value;
            const uniqueVendors = [...new Set(allGrs.map(gr => gr.vendor))].sort();
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

        updateGrKpisAndChart(allGrs);
        applyFilters();
    }, (error) => {
        console.error("Gagal memuat data GR:", error);
    });
}

// Inisialisasi halaman
document.addEventListener("DOMContentLoaded", () => {
    loadRawMaterials().then(() => {
        listenToGr();
    });
    
    const inputTgl = document.getElementById('grTanggal');
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

    const filterVendor = document.getElementById('filterVendor');
    if (filterVendor) filterVendor.addEventListener('change', applyFilters);
});

// Menambahkan baris item dinamis ke formulir
window.tambahBarisItem = function() {
    const container = document.getElementById('grItemsContainer');
    const rowId = 'row_' + Date.now();

    const itemOptions = rawMaterialsList.map(item => 
        `<option value="${item.kode_barang}">${item.nama_barang} (${item.kode_barang})</option>`
    ).join('');

    const rowHtml = `
        <div class="items-row" id="${rowId}">
            <select class="form-input flex-2 gr-item-select">
                <option value="">-- Pilih Barang --</option>
                ${itemOptions}
            </select>
            <input type="number" class="form-input flex-1 text-center gr-item-qty" placeholder="0" min="1">
            <input type="number" class="form-input flex-1 text-center gr-item-reject" placeholder="0" min="0" value="0">
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

// Menyimpan data Goods Receipt dan memperbarui stok & mutasi
window.simpanGr = async function() {
    const noPo = document.getElementById('grNoPo').value.trim();
    const vendor = document.getElementById('grVendor').value.trim();
    const tanggal = document.getElementById('grTanggal').value;
    const catatan = document.getElementById('grCatatan').value.trim();
    const container = document.getElementById('grItemsContainer');
    const rows = container.getElementsByClassName('items-row');

    if (!noPo || !vendor || !tanggal || rows.length === 0) {
        alert("Mohon lengkapi semua data dan tambahkan minimal 1 item!");
        return;
    }

    const items = [];
    for (let row of rows) {
        const select = row.querySelector('.gr-item-select');
        const qtyInput = row.querySelector('.gr-item-qty');
        const rejectInput = row.querySelector('.gr-item-reject');

        const kode_barang = select.value;
        const qty = parseInt(qtyInput.value);
        const reject = parseInt(rejectInput.value) || 0;

        if (!kode_barang || isNaN(qty) || qty <= 0) {
            alert("Harap pilih barang dan masukkan jumlah Qty yang valid!");
            return;
        }

        const material = rawMaterialsList.find(m => m.kode_barang === kode_barang);
        items.push({
            kode_barang,
            nama_barang: material ? material.nama_barang : "Bahan Baku",
            qty,
            reject
        });
    }

    // Generate Nomor GR unik
    const randomSuffix = Math.floor(100 + Math.random() * 900);
    const dateStr = tanggal.replace(/-/g, '').substring(2);
    const noGr = `GR-${dateStr}-${randomSuffix}`;

    const activeUser = JSON.parse(localStorage.getItem('foodsync_active_user')) || {};
    const namaUser = activeUser.firstName ? `${activeUser.firstName} ${activeUser.lastName}` : "Staff Gudang";

    const dataGr = {
        no_gr: noGr,
        no_po: noPo,
        vendor: vendor,
        tanggal: new Date(tanggal).toISOString(),
        catatan: catatan,
        items: items,
        dibuat_oleh: namaUser
    };

    try {
        // 1. Simpan dokumen Goods Receipt
        await setDoc(doc(db, "penerimaan_bahan", noGr), dataGr);

        // 2. Perbarui stok_barang & tulis log mutasi secara paralel
        for (let item of items) {
            const stokDiff = item.qty; // Stok masuk bertambah sebanyak qty diterima

            // Update stok barang
            const stokRef = doc(db, "stok_barang", item.kode_barang);
            await updateDoc(stokRef, {
                stok: increment(stokDiff)
            });

            // Tulis log mutasi
            const mutationId = `MT-${Date.now()}-${Math.floor(100 + Math.random() * 900)}`;
            const dataMutasi = {
                no_mutasi: mutationId,
                tanggal: new Date(tanggal).toISOString(),
                jenis: "Masuk",
                kode_barang: item.kode_barang,
                nama_barang: item.nama_barang,
                dari: vendor,
                ke: "Gudang Utama",
                qty: item.qty,
                no_referensi: noGr
            };
            await setDoc(doc(db, "mutasi", mutationId), dataMutasi);
        }

        alert(`Penerimaan barang ${noGr} berhasil diproses!`);
        closeModalForm();
        // Reset form
        document.getElementById('grNoPo').value = '';
        document.getElementById('grVendor').value = '';
        document.getElementById('grCatatan').value = '';
        container.innerHTML = '';

    } catch (error) {
        console.error("Gagal memproses penerimaan barang:", error);
        alert("Terjadi kesalahan saat memproses data.");
    }
};

// Menampilkan Detail Goods Receipt di Modal
window.bukaDetailGr = async function(grId) {
    try {
        const docSnap = await getDoc(doc(db, "penerimaan_bahan", grId));
        if (!docSnap.exists()) return;

        const gr = docSnap.data();
        
        // Isi header detail
        document.getElementById('detGrNo').innerText = gr.no_gr;
        document.getElementById('detGrTanggal').innerText = new Date(gr.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
        document.getElementById('detGrVendor').innerText = gr.vendor;

        // Render tabel detail
        const tableBody = document.getElementById('detGrTableBody');
        tableBody.innerHTML = '';

        gr.items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold">${item.nama_barang} (${item.kode_barang})</td>
                <td class="text-center text-green fw-bold">${item.qty}</td>
                <td class="text-center text-red fw-bold">${item.reject}</td>
            `;
            tableBody.appendChild(tr);
        });

        openModalDetail();
    } catch (error) {
        console.error("Gagal memuat detail GR:", error);
    }
};

// Eksis modal control dari HTML
window.openModalForm = function() {
    document.getElementById('formModal').style.display = 'flex';
    // Otomatis tambahkan satu baris item kosong di awal
    const container = document.getElementById('grItemsContainer');
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
window.grStatusChartInit = function() {
    // Diinisialisasi secara dinamis di updateGrKpisAndChart
};