import { db } from './firebase-config.js';
import { 
    collection, 
    onSnapshot 
} from "https://www.gstatic.com/firebasejs/12.15.0/firebase-firestore.js";

// Pengaturan Global Chart.js untuk Font agar bersih
Chart.defaults.font.family = "'Open Sans', sans-serif";
Chart.defaults.color = '#a3aed1';

// Inisialisasi Grafik
let lineChart, donutChart;

function initCharts() {
    // --- LINE CHART (Grafik Stok 6 Bulan Terakhir) ---
    const canvasLine = document.getElementById('lineChart');
    if (canvasLine) {
        const ctxLine = canvasLine.getContext('2d');
        lineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [
                    {
                        label: 'Stok',
                        data: [35000, 42000, 38000, 48000, 35000, 45000],
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderWidth: 0,
                        pointHitRadius: 10
                    },
                    {
                        label: 'Masuk',
                        data: [15000, 18000, 14000, 19000, 16000, 15000],
                        borderColor: '#10b981',
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#10b981',
                        pointBorderWidth: 0,
                        pointHitRadius: 10
                    },
                    {
                        label: 'Keluar',
                        data: [10000, 12000, 11000, 13000, 14000, 12000],
                        borderColor: '#ec4899',
                        borderWidth: 3,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#ec4899',
                        pointBorderWidth: 0,
                        pointHitRadius: 10
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2b3674',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: { 
                        display: false,
                        grid: { display: false } 
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { font: { size: 12, weight: 600 } }
                    }
                }
            }
        });
    }

    // --- DONUT CHART (Distribusi Stok) ---
    const centerTextPlugin = {
        id: 'centerText',
        afterDraw: (chart) => {
            const { ctx, chartArea: { top, bottom, left, right, width, height } } = chart;
            ctx.save();
            ctx.font = '700 0.7rem Open Sans';
            ctx.fillStyle = '#a3aed1';
            ctx.textAlign = 'center';
            ctx.fillText('Total', width / 2, (height / 2) + top - 25);

            ctx.font = '700 1.8rem Open Sans';
            ctx.fillStyle = '#2b3674';
            ctx.fillText('258.760', width / 2, (height / 2) + top);

            ctx.font = '600 0.8rem Open Sans';
            ctx.fillStyle = '#a3aed1';
            ctx.fillText('Unit', width / 2, (height / 2) + top + 25);
            ctx.restore();
        }
    };

    const canvasDonut = document.getElementById('donutChart');
    if (canvasDonut) {
        const ctxDonut = canvasDonut.getContext('2d');
        donutChart = new Chart(ctxDonut, {
            type: 'doughnut',
            plugins: [centerTextPlugin],
            data: {
                labels: ['Barang Jadi', 'Bahan Baku', 'Lainnya'],
                datasets: [{
                    data: [65, 25, 10],
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b'
                    ],
                    borderWidth: 0,
                    cutout: '80%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2b3674',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });
    }
}

// Sinkronisasi data dinamis dari Firestore ke Dashboard secara real-time
function syncDashboardData() {
    // 1. Sinkronisasi Data Stok & Tabel Minimum
    onSnapshot(collection(db, "stok_barang"), (querySnapshot) => {
        let totalItems = 0;
        let totalStok = 0;
        let alertStokCount = 0;
        
        const minStockTableBody = document.getElementById('minStockTableBody');
        if (minStockTableBody) minStockTableBody.innerHTML = '';

        querySnapshot.forEach((docSnap) => {
            const item = docSnap.data();
            totalItems++;
            totalStok += item.stok;

            const isAlert = item.stok <= item.minimum;
            if (isAlert) {
                alertStokCount++;
                
                // Tambahkan baris di tabel stok minimum
                if (minStockTableBody) {
                    const tr = document.createElement('tr');
                    const badgeClass = item.stok < item.minimum ? 'badge-danger' : 'badge-warning';
                    const statusText = item.stok < item.minimum ? 'Kritis' : 'Rendah';
                    
                    tr.innerHTML = `
                        <td>${item.kode_barang}</td>
                        <td class="fw-bold">${item.nama_barang}</td>
                        <td class="text-danger fw-bold">${item.stok.toLocaleString('id-ID')} ${item.satuan}</td>
                        <td>${item.minimum.toLocaleString('id-ID')} ${item.satuan}</td>
                        <td><span class="badge ${badgeClass}">${statusText}</span></td>
                    `;
                    minStockTableBody.appendChild(tr);
                }
            }
        });

        // Update DOM KPI
        const elTotalItem = document.getElementById('kpiTotalItem');
        const elTotalStok = document.getElementById('kpiTotalStok');
        const elAlertStok = document.getElementById('kpiAlertStok');

        if (elTotalItem) elTotalItem.innerHTML = `${totalItems} <small>Item</small>`;
        if (elTotalStok) elTotalStok.innerHTML = `${totalStok.toLocaleString('id-ID')} <small>Unit</small>`;
        if (elAlertStok) elAlertStok.innerHTML = `${alertStokCount} <small>Item</small>`;
    });

    // 2. Sinkronisasi Data Mutasi (Barang Masuk, Keluar, & Aktivitas Terbaru)
    onSnapshot(collection(db, "mutasi"), (querySnapshot) => {
        let barangMasukTotal = 0;
        let barangKeluarTotal = 0;
        const mutations = [];

        querySnapshot.forEach((docSnap) => {
            const mut = docSnap.data();
            mutations.push(mut);

            if (mut.jenis === 'Masuk') {
                barangMasukTotal += mut.qty;
            } else if (mut.jenis === 'Keluar') {
                barangKeluarTotal += mut.qty;
            }
        });

        // Update DOM KPI Masuk & Keluar
        const elMasuk = document.getElementById('kpiBarangMasuk');
        const elKeluar = document.getElementById('kpiBarangKeluar');

        if (elMasuk) elMasuk.innerHTML = `${barangMasukTotal.toLocaleString('id-ID')} <small>Unit</small>`;
        if (elKeluar) elKeluar.innerHTML = `${barangKeluarTotal.toLocaleString('id-ID')} <small>Unit</small>`;

        // Render Aktivitas Terbaru (top 5 mutasi terbaru)
        const recentActivityList = document.getElementById('recentActivityList');
        if (recentActivityList) {
            recentActivityList.innerHTML = '';
            
            // Urutkan berdasarkan tanggal terbaru
            mutations.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
            
            const top5 = mutations.slice(0, 5);
            if (top5.length === 0) {
                recentActivityList.innerHTML = '<li class="text-center text-muted text-sm py-3">Belum ada aktivitas mutasi</li>';
                return;
            }

            top5.forEach(mut => {
                const li = document.createElement('li');
                let iconClass = 'bg-blue';
                let iconHtml = '<i class="fas fa-sync"></i>';
                let actionText = '';
                
                if (mut.jenis === 'Masuk') {
                    iconClass = 'bg-green';
                    iconHtml = '<i class="fas fa-arrow-down"></i>';
                    actionText = `Penerimaan/Hasil Produksi: ${mut.nama_barang}`;
                } else if (mut.jenis === 'Keluar') {
                    iconClass = 'bg-pink';
                    iconHtml = '<i class="fas fa-arrow-up"></i>';
                    actionText = `Pengeluaran Produksi: ${mut.nama_barang}`;
                }

                li.innerHTML = `
                    <div class="act-icon ${iconClass}">${iconHtml}</div>
                    <div class="act-details">
                        <p>${actionText}</p>
                        <span>${mut.qty.toLocaleString('id-ID')} unit</span>
                    </div>
                `;
                recentActivityList.appendChild(li);
            });
        }
    });
}

// Inisialisasi halaman
document.addEventListener("DOMContentLoaded", () => {
    initCharts();
    syncDashboardData();
});