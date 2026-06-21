const FoodSyncStorage = {
    dashboardStats: [
        { label: 'Total PR', value: '248', rate: '+12%', up: true, theme: 'blue' },
        { label: 'PO Tertunda', value: '32', rate: '+8%', up: false, theme: 'orange' },
        { label: 'Vendor Aktif', value: '156', rate: '+3%', up: true, theme: 'blue' },
        { label: 'Faktur Tertunda', value: '18', rate: '-5%', up: true, theme: 'orange' }
    ],
    latestActivities: [
        { title: 'PR Baru Dibuat', time: '5 Menit yang lalu', user: 'Logistics' },
        { title: 'PO Disetujui', time: '20 Menit yang lalu', user: 'Management' },
        { title: 'Faktur Diajukan', time: '1 Jam yang lalu', user: 'Vendor Mandiri' }
    ],
    approvals: [
        { doc: 'PR-2026-089', desc: 'Bahan Baku Gandum', amount: 'Rp 45.000.000', status: 'Pending' },
        { doc: 'PO-2026-042', desc: 'Packaging Box Karton', amount: 'Rp 12.500.000', status: 'Pending' }
    ],
    prData: [
        { id: 'PR-2026-001', date: '2026-06-01', dept: 'Produksi', requester: 'Budi', item: 'Minyak Goreng Sawit', qty: '500 L', status: 'Disetujui' },
        { id: 'PR-2026-002', date: '2026-06-03', dept: 'QA', requester: 'Siti', item: 'Lab Reagent Pack', qty: '12 Unit', status: 'Diproses' },
        { id: 'PR-2026-003', date: '2026-06-05', dept: 'Gudang', requester: 'Andi', item: 'Pallet Kayu H1', qty: '50 Pcs', status: 'Tertunda' }
    ],
    rfqData: [
        { id: 'RFQ-2026-101', desc: 'Pengadaan Gula Rafinasi', date: '2026-06-01', close: '2026-06-15', vendor: '3 Respon', status: 'Diproses' }
    ],
    rfqComparison: [
        { vendor: 'PT Sinar Pangan', price: 'Rp 12.200/kg', delivery: '3 Hari', rating: '4.8/5' },
        { vendor: 'CV Sugar Agro', price: 'Rp 12.050/kg', delivery: '5 Hari', rating: '4.5/5' }
    ],
    poData: [
        { id: 'PO-2026-551', vendor: 'PT Sinar Pangan', date: '2026-06-02', amount: 'Rp 122.000.000', alert: 'Normal', status: 'Disetujui' }
    ],
    grData: [
        { id: 'GR-2026-901', po: 'PO-2026-551', vendor: 'PT Sinar Pangan', date: '2026-06-05', items: 'Gula Rafinasi (10 Ton)', status: 'Selesai' }
    ],
    financeData: [
        { id: 'INV-2026-04', vendor: 'PT Sinar Pangan', po: 'PO-2026-551', gr: 'GR-2026-901', amount: 'Rp 122.000.000', due: '2026-07-02', match: 'Cocok', status: 'Selesai' }
    ],
    vendorData: [
        { id: 'VND-001', name: 'PT Sinar Pangan', cat: 'Bahan Baku', rating: '4.8', orders: '42', speed: '98%', status: 'Aktif' }
    ]
};