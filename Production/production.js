/* ================================================================
   FoodSync Production System Dashboard
   Interactivity Layer — Vanilla JavaScript (tanpa framework)
   ----------------------------------------------------------------
   CARA PAKAI: Tempel seluruh kode ini tepat sebelum tag </body>
   pada production_dashboard_v2.html (setelah <script> bawaan).
   Tidak ada HTML/CSS yang diubah — semua elemen baru (toast,
   modal, dropdown filter, dll) dibuat & distyle sendiri oleh
   skrip ini melalui JavaScript murni.
   ================================================================ */
(function () {
  'use strict';

  /* ============================================================
     0. STORAGE HELPERS (aman, try-catch)
  ============================================================ */
  var STORAGE_KEYS = {
    VERIFIED: 'fs_verified_po',
    PROFILE: 'fs_profile_data',
    SUPPORT_TICKETS: 'fs_support_tickets',
    NOTIF_READ: 'fs_notif_read'
  };

  function storageGet(key, fallback) {
    try {
      var raw = localStorage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch (e) { return fallback; }
  }
  function storageSet(key, value) {
    try { localStorage.setItem(key, JSON.stringify(value)); } catch (e) { /* abaikan, website tetap berjalan */ }
  }

  /* ============================================================
     UTIL KECIL
  ============================================================ */
  function escapeHTML(str) {
    str = str == null ? '' : String(str);
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }
  function escapeAttr(str) {
    return String(str == null ? '' : str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
  }
  function formatDateID(dateStr) {
    if (!dateStr) return '-';
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    var d = new Date(dateStr + 'T00:00:00');
    if (isNaN(d.getTime())) return dateStr;
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
  }
  function getFieldByLabel(container, labelText) {
    if (!container) return null;
    var groups = container.querySelectorAll('.input-group');
    for (var i = 0; i < groups.length; i++) {
      var label = groups[i].querySelector('label');
      if (label && label.textContent.trim().toLowerCase() === labelText.toLowerCase()) {
        return groups[i].querySelector('input,select,textarea');
      }
    }
    return null;
  }
  function getInfoGroup(container, labelText) {
    if (!container) return null;
    var groups = container.querySelectorAll('.info-group');
    for (var i = 0; i < groups.length; i++) {
      var label = groups[i].querySelector('label');
      if (label && label.textContent.trim() === labelText) return groups[i];
    }
    return null;
  }
  function safeRun(fn) {
    try { fn(); } catch (e) { /* dijaga agar tidak muncul error di console */ }
  }

  /* ============================================================
     1. INJEKSI STYLE MINIMAL UNTUK ELEMEN BARU (dibuat via JS)
        Style asli pada <head> tidak disentuh sama sekali.
  ============================================================ */
  function injectDynamicStyles() {
    if (document.getElementById('fs-dynamic-style')) return;
    var style = document.createElement('style');
    style.id = 'fs-dynamic-style';
    style.textContent =
      '.fs-toast-container{position:fixed;bottom:30px;right:30px;display:flex;flex-direction:column;gap:12px;z-index:99999;align-items:flex-end;}' +
      '.fs-toast{display:flex;align-items:center;gap:12px;background:#1A2F4A;color:#fff;padding:14px 20px;border-radius:14px;font-size:14px;font-weight:600;box-shadow:0 10px 30px rgba(0,0,0,.2);opacity:0;transform:translateY(16px);transition:opacity .25s,transform .25s;max-width:340px;font-family:inherit;}' +
      '.fs-toast.fs-show{opacity:1;transform:translateY(0);}' +
      '.fs-toast i{font-size:17px;flex-shrink:0;}' +
      '.fs-toast.fs-success i{color:#22C55E;}.fs-toast.fs-error i{color:#EF4444;}.fs-toast.fs-warning i{color:#F59E0B;}.fs-toast.fs-info i{color:#60A5FA;}' +
      '.fs-field-error{color:#BB0013;font-size:12px;font-weight:600;margin-top:6px;display:block;}' +
      '.fs-input-error{border-color:#BB0013 !important;background:#FEF2F2 !important;}' +
      '.fs-overlay{position:fixed;inset:0;background:rgba(17,24,39,.45);display:flex;align-items:center;justify-content:center;z-index:9998;opacity:0;transition:opacity .2s;}' +
      '.fs-overlay.fs-show{opacity:1;}' +
      '.fs-modal-box{background:#fff;border-radius:20px;max-width:480px;width:90%;max-height:85vh;overflow-y:auto;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.25);transform:translateY(12px);transition:transform .2s;font-family:inherit;}' +
      '.fs-overlay.fs-show .fs-modal-box{transform:translateY(0);}' +
      '.fs-modal-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}' +
      '.fs-modal-head h3{font-size:18px;font-weight:700;color:#111827;margin:0;}' +
      '.fs-modal-close{background:#F3F4F6;border:none;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:14px;color:#6B7280;flex-shrink:0;}' +
      '.fs-modal-close:hover{background:#E5E7EB;}' +
      '.fs-modal-body{font-size:14px;color:#374151;line-height:1.7;}' +
      '.fs-modal-row{display:flex;justify-content:space-between;gap:14px;padding:9px 0;border-bottom:1px solid #F3F4F6;}' +
      '.fs-modal-row:last-child{border-bottom:none;}' +
      '.fs-modal-row span:first-child{color:#6B7280;font-weight:500;}' +
      '.fs-modal-row span:last-child{font-weight:700;color:#111827;text-align:right;}' +
      '.fs-modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:20px;}' +
      '.fs-btn{padding:11px 22px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;border:none;font-family:inherit;}' +
      '.fs-btn-primary{background:#003E7B;color:#fff;}.fs-btn-primary:hover{opacity:.9;}' +
      '.fs-btn-danger{background:#BB0013;color:#fff;}.fs-btn-danger:hover{opacity:.9;}' +
      '.fs-btn-ghost{background:#F3F4F6;color:#374151;}.fs-btn-ghost:hover{background:#E5E7EB;}' +
      '.fs-kebab-menu{position:absolute;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.15);padding:6px;z-index:500;min-width:175px;border:1px solid #F3F4F6;}' +
      '.fs-kebab-item{padding:10px 14px;font-size:13px;font-weight:600;color:#374151;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:8px;}' +
      '.fs-kebab-item:hover{background:#F9FAFB;}.fs-kebab-item i{width:14px;color:#9CA3AF;}' +
      '.fs-filter-panel{background:#fff;border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,.04);padding:16px 20px;margin-bottom:20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;border:1px solid #F3F4F6;}' +
      '.fs-filter-panel input{flex:1;min-width:200px;padding:10px 14px;border-radius:8px;border:1px solid #E5E7EB;background:#F9FAFB;font-size:13px;font-family:inherit;outline:none;}' +
      '.fs-filter-panel button{padding:10px 18px;border-radius:8px;border:none;background:#E5E7EB;font-weight:600;font-size:13px;cursor:pointer;font-family:inherit;}' +
      '.fs-filter-panel button:hover{background:#D1D5DB;}' +
      '.fs-pw-wrapper{position:relative;}' +
      '.fs-pw-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9CA3AF;cursor:pointer;font-size:15px;padding:4px;}' +
      '.fs-pw-toggle:hover{color:#003E7B;}' +
      '.fs-empty-state{text-align:center;padding:40px 20px;color:#9CA3AF;font-size:14px;font-weight:600;width:100%;}' +
      '.fs-empty-state i{font-size:26px;display:block;margin-bottom:10px;}' +
      '.fs-btn-loading{position:relative;opacity:.75;cursor:not-allowed !important;}' +
      '.fs-btn-loading .fs-spin{display:inline-block;width:13px;height:13px;border:2px solid rgba(255,255,255,.5);border-top-color:#fff;border-radius:50%;animation:fs-spin .6s linear infinite;margin-right:7px;vertical-align:middle;}' +
      '@keyframes fs-spin{to{transform:rotate(360deg);}}' +
      '.fs-wo-done{opacity:.5;position:relative;}' +
      '.fs-wo-done::after{content:"\\2713 Selesai";position:absolute;top:14px;right:14px;background:#DCFCE7;color:#15803D;font-size:10px;font-weight:700;padding:3px 9px;border-radius:10px;}' +
      '.fs-edit-input{width:100%;padding:10px 14px;border-radius:8px;border:1px solid #E5E7EB;background:#F9FAFB;font-size:15px;font-weight:600;color:#1E1E1E;font-family:inherit;outline:none;}' +
      '.fs-edit-input:focus{border-color:#003E7B;background:#fff;}';
    document.head.appendChild(style);
  }

  /* ============================================================
     2. TOAST NOTIFICATION (reusable, max 3 sekaligus)
  ============================================================ */
  var TOAST_ICONS = {
    success: 'fa-check-circle',
    error: 'fa-times-circle',
    warning: 'fa-exclamation-triangle',
    info: 'fa-info-circle'
  };
  function getToastContainer() {
    var c = document.querySelector('.fs-toast-container');
    if (!c) {
      c = document.createElement('div');
      c.className = 'fs-toast-container';
      document.body.appendChild(c);
    }
    return c;
  }
  function showToast(message, type) {
    type = TOAST_ICONS[type] ? type : 'success';
    var container = getToastContainer();
    var existing = container.querySelectorAll('.fs-toast');
    if (existing.length >= 3) existing[0].remove();

    var toast = document.createElement('div');
    toast.className = 'fs-toast fs-' + type;
    toast.innerHTML = '<i class="fas ' + TOAST_ICONS[type] + '"></i><span></span>';
    toast.querySelector('span').textContent = message;
    container.appendChild(toast);
    requestAnimationFrame(function () { toast.classList.add('fs-show'); });
    setTimeout(function () {
      toast.classList.remove('fs-show');
      setTimeout(function () { if (toast.parentNode) toast.remove(); }, 300);
    }, 3000);
  }

  /* Alihkan alert() bawaan (misalnya pada pagination riwayat) ke toast,
     supaya tidak ada native alert() yang mengganggu UX. */
  window.alert = function (msg) { showToast(String(msg), 'info'); };

  /* ============================================================
     3. SISTEM NAVIGASI VIEW
  ============================================================ */
  var NAV_MAP = {
    dashboard: 0, rencana: 1, 'rencana-page2': 1, 'form-rencana': 1,
    po: 2, 'po-page2': 2, 'form-po': 2, proses: 3, input: 4, riwayat: 5
  };

  function switchView(viewName, clickedElement) {
    if (!viewName) return;
    var target = document.getElementById('view-' + viewName);
    if (!target) {
      showToast('Halaman "' + viewName + '" tidak ditemukan.', 'error');
      return;
    }
    document.querySelectorAll('.view-section').forEach(function (v) { v.classList.remove('active'); });
    target.classList.add('active');

    var navItems = document.querySelectorAll('.nav-menu .nav-item');
    if (clickedElement && clickedElement.classList && clickedElement.classList.contains('nav-item')) {
      navItems.forEach(function (i) { i.classList.remove('active'); });
      clickedElement.classList.add('active');
    } else if (viewName in NAV_MAP) {
      navItems.forEach(function (i) { i.classList.remove('active'); });
      var idx = NAV_MAP[viewName];
      if (navItems[idx]) navItems[idx].classList.add('active');
    }
    closeAllDropdowns();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  /* ============================================================
     4. DROPDOWN SETTINGS & NOTIFIKASI
  ============================================================ */
  function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-modal.show').forEach(function (m) { m.classList.remove('show'); });
  }
  function toggleModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    var willOpen = !modal.classList.contains('show');
    closeAllDropdowns();
    if (willOpen) modal.classList.add('show');
  }

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.header-actions')) closeAllDropdowns();
  });

  function initNotifications() {
    var markAllLink = document.querySelector('#modal-notif .dropdown-header a');
    if (markAllLink) {
      markAllLink.style.cursor = 'pointer';
      markAllLink.addEventListener('click', function () {
        document.querySelectorAll('#modal-notif .list-item').forEach(function (item, idx) {
          if (idx < 3) item.style.opacity = '.55';
        });
        storageSet(STORAGE_KEYS.NOTIF_READ, true);
        showToast('Semua notifikasi telah ditandai sebagai dibaca.', 'success');
      });
    }
    var lihatSemua = document.querySelector('#modal-notif .list-item:last-child a');
    if (lihatSemua) {
      lihatSemua.style.cursor = 'pointer';
      lihatSemua.addEventListener('click', function () {
        toggleModal('modal-notif');
        showToast('Menampilkan seluruh notifikasi.', 'info');
      });
    }
    document.querySelectorAll('#modal-notif .list-item').forEach(function (item, idx) {
      if (idx < 3) {
        item.style.cursor = 'pointer';
        item.addEventListener('click', function () {
          var h4 = item.querySelector('h4');
          var p = item.querySelector('p');
          showModal('fs-modal-notif-detail', h4 ? h4.textContent.trim() : 'Notifikasi',
            '<p>' + (p ? p.innerHTML : '') + '</p>');
        });
      }
    });
    if (storageGet(STORAGE_KEYS.NOTIF_READ, false)) {
      document.querySelectorAll('#modal-notif .list-item').forEach(function (item, idx) {
        if (idx < 3) item.style.opacity = '.55';
      });
    }
  }

  /* ============================================================
     5. LOADING STATE TOMBOL
  ============================================================ */
  function setButtonLoading(button, isLoading, loadingText) {
    if (!button) return;
    loadingText = loadingText || 'Memproses...';
    if (isLoading) {
      if (button.dataset.fsOriginalHtml === undefined) {
        button.dataset.fsOriginalHtml = button.innerHTML;
      }
      button.innerHTML = '<span class="fs-spin"></span>' + escapeHTML(loadingText);
      button.classList.add('fs-btn-loading');
      button.disabled = true;
    } else {
      if (button.dataset.fsOriginalHtml !== undefined) {
        button.innerHTML = button.dataset.fsOriginalHtml;
        delete button.dataset.fsOriginalHtml;
      }
      button.classList.remove('fs-btn-loading');
      button.disabled = false;
    }
  }

  /* ============================================================
     6. VALIDASI FORM REUSABLE
  ============================================================ */
  function showFieldError(input, message) {
    if (!input) return;
    clearFieldError(input);
    input.classList.add('fs-input-error');
    var err = document.createElement('span');
    err.className = 'fs-field-error';
    err.textContent = message;
    var anchor = input.closest('.input-with-unit') || input;
    anchor.insertAdjacentElement('afterend', err);
    if (!input.dataset.fsErrorBound) {
      input.dataset.fsErrorBound = '1';
      input.addEventListener('input', function () { clearFieldError(input); });
      input.addEventListener('change', function () { clearFieldError(input); });
    }
  }
  function clearFieldError(input) {
    if (!input) return;
    input.classList.remove('fs-input-error');
    var anchor = input.closest('.input-with-unit') || input;
    var next = anchor.nextElementSibling;
    if (next && next.classList && next.classList.contains('fs-field-error')) next.remove();
  }
  function clearFormErrors(container) {
    if (!container) return;
    container.querySelectorAll('.fs-field-error').forEach(function (e) { e.remove(); });
    container.querySelectorAll('.fs-input-error').forEach(function (e) { e.classList.remove('fs-input-error'); });
  }
  function validateRequired(input, message) {
    if (!input) return false;
    if (input.tagName === 'SELECT') {
      var opt = input.options[input.selectedIndex];
      if (input.selectedIndex <= 0 || (opt && opt.disabled)) {
        showFieldError(input, message || 'Wajib dipilih.');
        return false;
      }
    } else if (!input.value.trim()) {
      showFieldError(input, message || 'Wajib diisi.');
      return false;
    }
    clearFieldError(input);
    return true;
  }
  function validateNumber(input, message) {
    if (!input) return false;
    var val = input.value.trim();
    if (val === '' || isNaN(Number(val))) {
      showFieldError(input, message || 'Harus berupa angka.');
      return false;
    }
    clearFieldError(input);
    return true;
  }
  function validateEmail(input, message) {
    if (!input) return false;
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!re.test(input.value.trim())) {
      showFieldError(input, message || 'Format email tidak valid.');
      return false;
    }
    clearFieldError(input);
    return true;
  }

  /* ============================================================
     7. MODAL DINAMIS
  ============================================================ */
  function createDynamicModal(id, title, contentHTML) {
    var existing = document.getElementById(id);
    if (existing) existing.remove();
    var overlay = document.createElement('div');
    overlay.id = id;
    overlay.className = 'fs-overlay';
    overlay.innerHTML =
      '<div class="fs-modal-box">' +
        '<div class="fs-modal-head"><h3></h3><button type="button" class="fs-modal-close">&times;</button></div>' +
        '<div class="fs-modal-body"></div>' +
      '</div>';
    overlay.querySelector('h3').textContent = title || '';
    overlay.querySelector('.fs-modal-body').innerHTML = contentHTML || '';
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeDynamicModal(id);
    });
    overlay.querySelector('.fs-modal-close').addEventListener('click', function () { closeDynamicModal(id); });
    document.body.appendChild(overlay);
    return overlay;
  }
  function openDynamicModal(id) {
    var overlay = document.getElementById(id);
    if (!overlay) return;
    overlay.style.display = 'flex';
    requestAnimationFrame(function () { overlay.classList.add('fs-show'); });
  }
  function closeDynamicModal(id) {
    var overlay = document.getElementById(id);
    if (!overlay) return;
    overlay.classList.remove('fs-show');
    setTimeout(function () { if (overlay.parentNode) overlay.remove(); }, 200);
  }
  function closeTopDynamicModal() {
    var overlays = document.querySelectorAll('.fs-overlay.fs-show');
    if (overlays.length) closeDynamicModal(overlays[overlays.length - 1].id);
  }
  function showModal(id, title, html) {
    createDynamicModal(id, title, html);
    openDynamicModal(id);
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeAllDropdowns();
      closeTopDynamicModal();
      document.querySelectorAll('.fs-kebab-menu').forEach(function (m) { m.remove(); });
    }
  });

  /* ============================================================
     8. EXPORT CSV
  ============================================================ */
  function exportTableToCSV(tableSelector, filename) {
    var table = document.querySelector(tableSelector);
    if (!table) { showToast('Tabel tidak ditemukan untuk diekspor.', 'error'); return; }
    var rows = Array.prototype.filter.call(table.querySelectorAll('tr'), function (tr) {
      return tr.style.display !== 'none' && !tr.classList.contains('fs-empty-row');
    });
    var csv = rows.map(function (tr) {
      return Array.prototype.map.call(tr.querySelectorAll('th,td'), function (cell) {
        var text = cell.textContent.trim().replace(/\s+/g, ' ').replace(/"/g, '""');
        return '"' + text + '"';
      }).join(',');
    }).join('\r\n');

    var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = filename || 'export.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('Data berhasil diekspor ke ' + (filename || 'export.csv') + '.', 'success');
  }

  /* ============================================================
     9. EMPTY-STATE HELPER (dipakai oleh search & filter)
  ============================================================ */
  function toggleEmptyRow(tbody, visibleCount, colSpan) {
    var emptyRow = tbody.querySelector('.fs-empty-row');
    if (visibleCount === 0) {
      if (!emptyRow) {
        emptyRow = document.createElement('tr');
        emptyRow.className = 'fs-empty-row';
        emptyRow.innerHTML = '<td colspan="' + colSpan + '"><div class="fs-empty-state"><i class="fas fa-folder-open"></i>Data tidak ditemukan</div></td>';
        tbody.appendChild(emptyRow);
      }
    } else if (emptyRow) {
      emptyRow.remove();
    }
  }
  function toggleKanbanEmptyState(view, visible) {
    var board = view.querySelector('.kanban-board');
    var empty = view.querySelector('#fs-kanban-empty');
    if (visible === 0) {
      if (!empty && board) {
        empty = document.createElement('div');
        empty.id = 'fs-kanban-empty';
        empty.className = 'fs-empty-state';
        empty.innerHTML = '<i class="fas fa-folder-open"></i>Data tidak ditemukan';
        board.insertAdjacentElement('afterend', empty);
      }
    } else if (empty) {
      empty.remove();
    }
  }

  /* ============================================================
     10. SEARCH BAR GLOBAL
  ============================================================ */
  function initGlobalSearch() {
    var input = document.querySelector('.search-bar input');
    if (!input) return;
    input.addEventListener('input', function () { applyGlobalSearch(input.value); });
  }
  function applyGlobalSearch(query) {
    var view = document.querySelector('.view-section.active');
    if (!view) return;
    var q = query.trim().toLowerCase();
    var table = view.querySelector('table');
    var kanbanCards = view.querySelectorAll('.kanban-card');

    if (table) {
      var tbody = table.querySelector('tbody');
      if (!tbody) return;
      var visible = 0;
      var colCount = table.querySelectorAll('thead th').length || 6;
      tbody.querySelectorAll('tr').forEach(function (tr) {
        if (tr.classList.contains('fs-empty-row')) return;
        var match = !q || tr.textContent.toLowerCase().indexOf(q) !== -1;
        tr.style.display = match ? '' : 'none';
        if (match) visible++;
      });
      toggleEmptyRow(tbody, visible, colCount);
    } else if (kanbanCards.length) {
      var visibleCards = 0;
      kanbanCards.forEach(function (card) {
        var match = !q || card.textContent.toLowerCase().indexOf(q) !== -1;
        card.style.display = match ? '' : 'none';
        if (match) visibleCards++;
      });
      toggleKanbanEmptyState(view, visibleCards);
    }
  }

  /* ============================================================
     11. RENCANA PRODUKSI
  ============================================================ */
  function addRencanaRow(nomorPO, produk, jumlah, target) {
    var tbody = document.querySelector('#view-rencana .rp-table tbody');
    if (!tbody) return;
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td>' + escapeHTML(nomorPO) + '</td>' +
      '<td>-</td>' +
      '<td>' + escapeHTML(produk) + '</td>' +
      '<td>' + escapeHTML(jumlah) + '</td>' +
      '<td>' + escapeHTML(target) + '</td>' +
      '<td><button class="btn-verif">Verifikasi</button></td>';
    tbody.insertBefore(tr, tbody.firstChild);
  }

  function initRencanaProduksi() {
    var saveBtn = document.querySelector('#view-form-rencana .btn-terbitkan');
    if (saveBtn) {
      saveBtn.onclick = function () {
        var formEl = document.getElementById('view-form-rencana');
        clearFormErrors(formEl);
        var nomorPO = getFieldByLabel(formEl, 'Nomor PO');
        var produk = getFieldByLabel(formEl, 'Pilih Rencana Produksi');
        var jumlah = getFieldByLabel(formEl, 'Jumlah Rencana Produksi');
        var target = getFieldByLabel(formEl, 'Target Selesai');

        var valid = true;
        if (!validateRequired(nomorPO, 'Nomor PO wajib diisi.')) valid = false;
        if (!validateRequired(produk, 'Nama produk wajib diisi.')) valid = false;
        if (!validateRequired(jumlah, 'Jumlah produksi wajib diisi.')) valid = false;
        if (!validateRequired(target, 'Target selesai wajib diisi.')) valid = false;
        if (!valid) { showToast('Mohon lengkapi semua data yang wajib diisi.', 'error'); return; }

        setButtonLoading(saveBtn, true, 'Menyimpan...');
        setTimeout(function () {
          addRencanaRow(nomorPO.value.trim(), produk.value.trim(), jumlah.value.trim(), target.value.trim());
          [nomorPO, produk, jumlah, target].forEach(function (i) { if (i) i.value = ''; });
          setButtonLoading(saveBtn, false);
          showToast('Data rencana produksi berhasil disimpan.', 'success');
          switchView('rencana');
        }, 600);
      };
    }

    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.btn-verif');
      if (!btn || btn.disabled) return;
      var row = btn.closest('tr');
      var orderId = row && row.cells[0] ? row.cells[0].textContent.trim() : null;
      setButtonLoading(btn, true, 'Memverifikasi...');
      setTimeout(function () {
        btn.classList.remove('fs-btn-loading');
        btn.innerHTML = 'Terverifikasi';
        delete btn.dataset.fsOriginalHtml;
        btn.disabled = true;
        btn.style.opacity = '.7';
        btn.style.cursor = 'default';
        if (orderId) {
          var verified = storageGet(STORAGE_KEYS.VERIFIED, []);
          if (verified.indexOf(orderId) === -1) { verified.push(orderId); storageSet(STORAGE_KEYS.VERIFIED, verified); }
        }
        showToast('Permintaan produksi berhasil diverifikasi.', 'success');
      }, 500);
    });
  }

  function restoreVerifiedState() {
    var verified = storageGet(STORAGE_KEYS.VERIFIED, []);
    if (!verified.length) return;
    document.querySelectorAll('.rp-table tbody tr').forEach(function (tr) {
      var id = tr.cells[0] ? tr.cells[0].textContent.trim() : null;
      if (id && verified.indexOf(id) !== -1) {
        var btn = tr.querySelector('.btn-verif');
        if (btn) {
          btn.textContent = 'Terverifikasi';
          btn.disabled = true;
          btn.style.opacity = '.7';
          btn.style.cursor = 'default';
        }
      }
    });
  }

  /* Pagination .page-num tanpa onclick (halaman 3,4,5) */
  document.addEventListener('click', function (e) {
    var pn = e.target.closest('.page-num');
    if (!pn || pn.hasAttribute('onclick')) return;
    showToast('Data halaman ' + pn.textContent.trim() + ' belum tersedia.', 'info');
  });
  /* Highlight visual untuk pagination riwayat (.rpg-btn) */
  document.addEventListener('click', function (e) {
    var rpg = e.target.closest('.rpg-btn');
    if (!rpg) return;
    document.querySelectorAll('.rpg-btn').forEach(function (b) { b.classList.remove('rpg-active'); });
    rpg.classList.add('rpg-active');
  });

  /* ============================================================
     12. MONITORING PERINTAH KERJA
  ============================================================ */
  function addMonitoringRow(nomorPO, lini, produk, jumlah, tanggal) {
    var tbody = document.querySelector('#view-po .rp-table tbody');
    if (!tbody) return;
    var tr = document.createElement('tr');
    tr.innerHTML =
      '<td>' + escapeHTML(nomorPO) + '</td>' +
      '<td>' + escapeHTML(lini) + '</td>' +
      '<td>' + escapeHTML(produk) + '</td>' +
      '<td class="center" style="color:var(--text-main);">' + escapeHTML(jumlah) + '</td>' +
      '<td>' + escapeHTML(tanggal) + '</td>' +
      '<td class="center"><span class="badge badge-draft">Draft</span></td>' +
      '<td class="center"><button class="btn-cetak">Cetak PDF</button></td>';
    tbody.insertBefore(tr, tbody.firstChild);
  }

  function printPOData(data) {
    var win = window.open('', '_blank', 'width=720,height=600');
    if (!win) { showToast('Mohon izinkan pop-up untuk mencetak.', 'warning'); return; }
    win.document.write(
      '<html><head><title>Cetak Perintah Kerja</title><style>' +
      'body{font-family:Arial,sans-serif;padding:40px;color:#111827;}' +
      'h1{font-size:20px;color:#003E7B;margin-bottom:4px;}' +
      'p.sub{color:#6B7280;margin-bottom:24px;}' +
      'table{width:100%;border-collapse:collapse;}' +
      'td{padding:10px 0;border-bottom:1px solid #E5E7EB;font-size:14px;}' +
      'td:first-child{color:#6B7280;width:220px;}td:last-child{font-weight:700;}' +
      '</style></head><body>' +
      '<h1>FoodSync - Perintah Kerja</h1>' +
      '<p class="sub">Dokumen dicetak otomatis dari sistem FoodSync Production.</p>' +
      '<table>' +
        '<tr><td>Nomor PO</td><td>' + escapeHTML(data.nomorPO) + '</td></tr>' +
        '<tr><td>Lini Produksi</td><td>' + escapeHTML(data.lini) + '</td></tr>' +
        '<tr><td>Produk</td><td>' + escapeHTML(data.produk) + '</td></tr>' +
        '<tr><td>Target Qty</td><td>' + escapeHTML(data.qty) + '</td></tr>' +
        '<tr><td>Tanggal Diterbitkan</td><td>' + escapeHTML(data.tanggal) + '</td></tr>' +
        '<tr><td>Status</td><td>' + escapeHTML(data.status) + '</td></tr>' +
      '</table></body></html>'
    );
    win.document.close();
    win.focus();
    setTimeout(function () { win.print(); }, 300);
  }

  function initMonitoringPerintahKerja() {
    window.handleTerbitkan = function () {
      var formEl = document.getElementById('view-form-po');
      if (!formEl) return;
      clearFormErrors(formEl);
      var nomorPO = formEl.querySelector('input[placeholder="Contoh: PO-2023-013"]');
      var targetInput = formEl.querySelector('input[type="date"]');
      var rencanaSelect = getFieldByLabel(formEl, 'Pilih Rencana Produksi');
      var liniSelect = getFieldByLabel(formEl, 'Lini Produksi');
      var jumlah = formEl.querySelector('input[placeholder="50,000 Dus"]');
      var textarea = formEl.querySelector('textarea');

      var valid = true;
      if (!validateRequired(nomorPO, 'Nomor PO wajib diisi.')) valid = false;
      if (!validateRequired(targetInput, 'Target selesai wajib diisi.')) valid = false;
      if (!validateRequired(rencanaSelect, 'Pilih rencana produksi terlebih dahulu.')) valid = false;
      if (!validateRequired(liniSelect, 'Pilih lini produksi terlebih dahulu.')) valid = false;
      if (!validateRequired(jumlah, 'Jumlah rencana produksi wajib diisi.')) valid = false;
      if (!valid) { showToast('Mohon lengkapi data perintah kerja.', 'error'); return; }

      var nomorVal = nomorPO.value.trim();
      var liniVal = liniSelect.options[liniSelect.selectedIndex].text;
      var produkVal = rencanaSelect.options[rencanaSelect.selectedIndex].text;
      var jumlahVal = jumlah.value.trim();
      var tglVal = formatDateID(targetInput.value);

      var btn = formEl.querySelector('.btn-terbitkan');
      setButtonLoading(btn, true, 'Menerbitkan...');
      setTimeout(function () {
        addMonitoringRow(nomorVal, liniVal, produkVal, jumlahVal, tglVal);
        nomorPO.value = ''; jumlah.value = ''; targetInput.value = '';
        rencanaSelect.selectedIndex = 0;
        liniSelect.selectedIndex = 0;
        if (textarea) textarea.value = '';
        setButtonLoading(btn, false);
        showToast('Perintah Kerja ' + nomorVal + ' berhasil diterbitkan!', 'success');
        switchView('po');
      }, 600);
    };

    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.btn-cetak');
      if (!btn) return;
      var row = btn.closest('tr');
      if (!row) return;
      var cells = row.querySelectorAll('td');
      var data = {
        nomorPO: cells[0] ? cells[0].textContent.trim() : '-',
        lini: cells[1] ? cells[1].textContent.trim() : '-',
        produk: cells[2] ? cells[2].textContent.trim() : '-',
        qty: cells[3] ? cells[3].textContent.trim() : '-',
        tanggal: cells[4] ? cells[4].textContent.trim() : '-',
        status: cells[5] ? cells[5].textContent.trim() : '-'
      };
      setButtonLoading(btn, true, 'Mencetak...');
      setTimeout(function () {
        printPOData(data);
        setButtonLoading(btn, false);
        showToast('PDF perintah kerja ' + data.nomorPO + ' siap dicetak.', 'success');
      }, 500);
    });
  }

  /* ============================================================
     13. PROSES PRODUKSI / KANBAN
  ============================================================ */
  function getCardData(card) {
    var po = card.querySelector('.po-chip, .po-chip-yellow');
    var title = card.querySelector('.kanban-card-title');
    var line = card.querySelector('.kanban-card-line');
    var progressPct = card.querySelector('.progress-pct');
    var progressQty = card.querySelector('.progress-qty');
    var footer = card.querySelectorAll('.kanban-card-footer-row span');
    return {
      po: po ? po.textContent.trim() : '-',
      title: title ? title.textContent.trim() : '-',
      line: line ? line.textContent.trim().replace(/\s+/g, ' ') : '-',
      progress: progressPct ? progressPct.textContent.trim() : null,
      qty: progressQty ? progressQty.textContent.trim() : (footer[0] ? footer[0].textContent.trim() : '-'),
      eta: footer[1] ? footer[1].textContent.trim() : '-'
    };
  }
  function openKanbanDetailModal(card) {
    var data = getCardData(card);
    var colTitleEl = card.closest('.kanban-col').querySelector('.kanban-col-title');
    var colName = colTitleEl ? colTitleEl.textContent.trim() : '-';
    var rows =
      '<div class="fs-modal-row"><span>Nomor PO</span><span>' + escapeHTML(data.po) + '</span></div>' +
      '<div class="fs-modal-row"><span>Produk</span><span>' + escapeHTML(data.title) + '</span></div>' +
      '<div class="fs-modal-row"><span>Lini</span><span>' + escapeHTML(data.line) + '</span></div>' +
      '<div class="fs-modal-row"><span>Kolom Status</span><span>' + escapeHTML(colName) + '</span></div>';
    if (data.progress) {
      rows += '<div class="fs-modal-row"><span>Progress</span><span>' + escapeHTML(data.progress) + '</span></div>';
    }
    rows += '<div class="fs-modal-row"><span>Kuantitas / Estimasi</span><span>' + escapeHTML(data.qty) + ' &bull; ' + escapeHTML(data.eta) + '</span></div>';
    showModal('fs-modal-kanban-detail', 'Detail Produksi - ' + data.po, rows);
  }
  function recalcKanbanCounts() {
    document.querySelectorAll('#view-proses .kanban-col').forEach(function (col) {
      var count = col.querySelectorAll('.kanban-card').length;
      var badge = col.querySelector('.kanban-count');
      if (badge) badge.textContent = count;
    });
  }
  function updateCardProgress(card) {
    var input = window.prompt('Masukkan progress produksi (0-100):', '0');
    if (input === null) return;
    var num = parseInt(input, 10);
    if (isNaN(num) || num < 0 || num > 100) {
      showToast('Masukkan angka antara 0 - 100.', 'error');
      return;
    }
    var pct = card.querySelector('.progress-pct');
    var fill = card.querySelector('.progress-bar-fill');
    if (!pct || !fill) {
      var section = document.createElement('div');
      section.className = 'progress-section';
      section.innerHTML =
        '<div class="progress-header"><span class="progress-label">Progress</span><span class="progress-pct">0%</span></div>' +
        '<div class="progress-bar-bg"><div class="progress-bar-fill" style="width:0%;"></div></div>';
      var footerRow = card.querySelector('.kanban-card-footer-row');
      if (footerRow) card.insertBefore(section, footerRow); else card.appendChild(section);
      pct = card.querySelector('.progress-pct');
      fill = card.querySelector('.progress-bar-fill');
    }
    pct.textContent = num + '%';
    fill.style.width = num + '%';
    showToast('Progress diperbarui menjadi ' + num + '%.', 'success');
    if (num === 100) {
      var poChip = card.querySelector('.po-chip, .po-chip-yellow');
      showToast('Produksi ' + (poChip ? poChip.textContent.trim() : '') + ' telah selesai!', 'success');
    }
  }
  function moveCardToNextColumn(card) {
    var columns = Array.prototype.slice.call(document.querySelectorAll('#view-proses .kanban-col'));
    var currentCol = card.closest('.kanban-col');
    var idx = columns.indexOf(currentCol);
    if (idx === -1 || idx === columns.length - 1) {
      showToast('Card sudah berada di kolom terakhir.', 'info');
      return;
    }
    columns[idx + 1].appendChild(card);
    recalcKanbanCounts();
    showToast('Status produksi dipindahkan ke kolom selanjutnya.', 'success');
  }
  function markCardDone(card) {
    var columns = document.querySelectorAll('#view-proses .kanban-col');
    var lastCol = columns[columns.length - 1];
    lastCol.appendChild(card);
    var pct = card.querySelector('.progress-pct');
    var fill = card.querySelector('.progress-bar-fill');
    if (pct) pct.textContent = '100%';
    if (fill) fill.style.width = '100%';
    recalcKanbanCounts();
    showToast('Produksi telah ditandai selesai.', 'success');
  }
  function openKanbanMenu(btn) {
    document.querySelectorAll('.fs-kebab-menu').forEach(function (m) { m.remove(); });
    var menu = document.createElement('div');
    menu.className = 'fs-kebab-menu';
    menu.innerHTML =
      '<div class="fs-kebab-item" data-action="detail"><i class="fas fa-eye"></i> Lihat Detail</div>' +
      '<div class="fs-kebab-item" data-action="progress"><i class="fas fa-tasks"></i> Update Progress</div>' +
      '<div class="fs-kebab-item" data-action="move"><i class="fas fa-arrow-right"></i> Pindahkan Status</div>' +
      '<div class="fs-kebab-item" data-action="done"><i class="fas fa-check"></i> Tandai Selesai</div>';
    document.body.appendChild(menu);
    var rect = btn.getBoundingClientRect();
    menu.style.top = (window.scrollY + rect.bottom + 4) + 'px';
    menu.style.left = (window.scrollX + rect.right - 175) + 'px';

    var card = btn.closest('.kanban-card');
    menu.addEventListener('click', function (e) {
      var item = e.target.closest('.fs-kebab-item');
      if (!item) return;
      var action = item.dataset.action;
      menu.remove();
      if (action === 'detail') openKanbanDetailModal(card);
      if (action === 'progress') updateCardProgress(card);
      if (action === 'move') moveCardToNextColumn(card);
      if (action === 'done') markCardDone(card);
    });

    setTimeout(function () {
      document.addEventListener('click', function closeMenu(ev) {
        if (!menu.contains(ev.target)) {
          menu.remove();
          document.removeEventListener('click', closeMenu);
        }
      });
    }, 0);
  }
  function initKanban() {
    document.addEventListener('click', function (e) {
      var menuBtn = e.target.closest('.card-menu-btn');
      if (menuBtn) {
        e.stopPropagation();
        openKanbanMenu(menuBtn);
        return;
      }
      var card = e.target.closest('.kanban-card');
      var prosesView = document.getElementById('view-proses');
      if (card && prosesView && prosesView.contains(card)) {
        openKanbanDetailModal(card);
      }
    });
  }

  /* ============================================================
     14. INPUT HASIL PRODUKSI
  ============================================================ */
  var completedWO = {};

  function markWODone(poNum) {
    document.querySelectorAll('.wo-list-item').forEach(function (item) {
      var numEl = item.querySelector('.wo-po-num');
      if (numEl && numEl.textContent.trim() === poNum) item.classList.add('fs-wo-done');
    });
  }

  function initInputHasilProduksi() {
    var view = document.getElementById('view-input');
    if (!view) return;

    window.selectWO = function (el, poNum, productName) {
      if (completedWO[poNum]) {
        showToast('Work order ' + poNum + ' sudah diselesaikan dan tidak dapat dipilih ulang.', 'warning');
        return;
      }
      document.querySelectorAll('.wo-list-item').forEach(function (i) { i.classList.remove('selected'); });
      el.classList.add('selected');
      var title = document.getElementById('wo-form-title');
      if (title) title.textContent = 'Input Hasil Produksi - ' + poNum;
      var good = document.getElementById('input-good');
      var defect = document.getElementById('input-defect');
      var catatan = document.getElementById('input-catatan');
      if (good) good.value = '0';
      if (defect) defect.value = '0';
      if (catatan) catatan.value = '';
      clearFormErrors(view);
    };

    window.handleSelesaikan = function () {
      clearFormErrors(view);
      var good = document.getElementById('input-good');
      var defect = document.getElementById('input-defect');
      var title = document.getElementById('wo-form-title');
      var poNum = title ? title.textContent.replace('Input Hasil Produksi - ', '').trim() : null;

      if (poNum && completedWO[poNum]) {
        showToast('Work order ini sudah diselesaikan sebelumnya.', 'warning');
        return;
      }

      var valid = true;
      if (!validateNumber(good, 'Jumlah produk baik harus berupa angka.')) valid = false;
      if (!validateNumber(defect, 'Jumlah produk cacat harus berupa angka.')) valid = false;
      if (!valid) { showToast('Mohon lengkapi data hasil produksi dengan benar.', 'error'); return; }

      var goodNum = parseInt(good.value, 10) || 0;
      var defectNum = parseInt(defect.value, 10) || 0;

      if (goodNum === 0 && defectNum === 0) {
        showFieldError(good, 'Jumlah produk baik wajib lebih dari 0.');
        showToast('Mohon masukkan jumlah produk baik terlebih dahulu.', 'error');
        return;
      }

      var btn = view.querySelector('.btn-selesaikan');
      setButtonLoading(btn, true, 'Menyimpan...');
      setTimeout(function () {
        if (poNum) completedWO[poNum] = true;
        markWODone(poNum);
        setButtonLoading(btn, false);
        showToast('Produksi ' + poNum + ' berhasil diselesaikan! (' + goodNum.toLocaleString('id-ID') + ' Karton)', 'success');
        setTimeout(function () { switchView('riwayat'); }, 500);
      }, 600);
    };
  }

  /* ============================================================
     15. RIWAYAT PRODUKSI
  ============================================================ */
  function filterRiwayatTable(query) {
    query = query.trim().toLowerCase();
    var tbody = document.querySelector('#view-riwayat .riwayat-table tbody');
    if (!tbody) return;
    var visibleCount = 0;
    tbody.querySelectorAll('tr').forEach(function (tr) {
      if (tr.classList.contains('fs-empty-row')) return;
      var match = !query || tr.textContent.toLowerCase().indexOf(query) !== -1;
      tr.style.display = match ? '' : 'none';
      if (match) visibleCount++;
    });
    toggleEmptyRow(tbody, visibleCount, 6);
  }
  function toggleRiwayatFilterPanel(toolbar) {
    var panel = document.getElementById('fs-riwayat-filter-panel');
    if (panel) { panel.remove(); return; }
    panel = document.createElement('div');
    panel.id = 'fs-riwayat-filter-panel';
    panel.className = 'fs-filter-panel';
    panel.innerHTML =
      '<input type="text" id="fs-riwayat-filter-input" placeholder="Cari produk atau status...">' +
      '<button type="button" id="fs-riwayat-filter-reset">Reset Filter</button>';
    toolbar.insertAdjacentElement('afterend', panel);
    var input = panel.querySelector('#fs-riwayat-filter-input');
    var resetBtn = panel.querySelector('#fs-riwayat-filter-reset');
    input.addEventListener('input', function () { filterRiwayatTable(input.value); });
    resetBtn.addEventListener('click', function () {
      input.value = '';
      filterRiwayatTable('');
      showToast('Filter riwayat produksi telah direset.', 'info');
    });
    input.focus();
  }
  function openRiwayatDetailModal(row) {
    var cells = row.querySelectorAll('td');
    var labels = ['Nomor PO', 'Produk', 'Waktu Selesai', 'Quantity', 'Defect', 'Status'];
    var rows = '';
    cells.forEach(function (cell, i) {
      rows += '<div class="fs-modal-row"><span>' + (labels[i] || '') + '</span><span>' + escapeHTML(cell.textContent.trim()) + '</span></div>';
    });
    showModal('fs-modal-riwayat-detail', 'Detail Riwayat Produksi', rows);
  }
  function initRiwayatProduksi() {
    var view = document.getElementById('view-riwayat');
    if (!view) return;
    var filterBtn = view.querySelector('.btn-filter-riwayat');
    var exportBtn = view.querySelector('.btn-export-file');
    var toolbar = view.querySelector('.riwayat-toolbar');

    if (filterBtn) filterBtn.addEventListener('click', function () { toggleRiwayatFilterPanel(toolbar); });
    if (exportBtn) {
      exportBtn.addEventListener('click', function () {
        setButtonLoading(exportBtn, true, 'Mengekspor...');
        setTimeout(function () {
          exportTableToCSV('#view-riwayat .riwayat-table', 'riwayat_produksi.csv');
          setButtonLoading(exportBtn, false);
        }, 400);
      });
    }
    document.addEventListener('click', function (e) {
      var row = e.target.closest('#view-riwayat .riwayat-table tbody tr');
      if (!row || row.classList.contains('fs-empty-row')) return;
      openRiwayatDetailModal(row);
    });
  }

  /* ============================================================
     16. HELP CENTER (fungsi tersedia secara global & aman
         walau elemen FAQ belum ada pada HTML saat ini)
  ============================================================ */
  function filterHelpCategory(category, element) {
    var items = document.querySelectorAll('.faq-item, .help-category');
    if (!items.length) return;
    items.forEach(function (item) {
      if (!category || category === 'all' || item.dataset.category === category) {
        item.style.display = '';
      } else {
        item.style.display = 'none';
      }
    });
    if (element) {
      document.querySelectorAll('.help-category-btn').forEach(function (b) { b.classList.remove('active'); });
      if (element.classList) element.classList.add('active');
    }
  }
  function searchHelpArticles(keyword) {
    var items = document.querySelectorAll('.faq-item');
    if (!items.length) return;
    var q = (keyword || '').toLowerCase();
    items.forEach(function (item) {
      var match = !q || item.textContent.toLowerCase().indexOf(q) !== -1;
      item.style.display = match ? '' : 'none';
    });
  }
  function toggleFAQ(button) {
    if (!button) return;
    var item = button.closest('.faq-item');
    if (!item) return;
    item.classList.toggle('open');
  }
  function openSupportModal() {
    var html =
      '<div class="input-group"><label>Judul</label><input type="text" id="fs-support-judul" placeholder="Judul keluhan/pertanyaan"></div>' +
      '<div class="input-group"><label>Kategori</label><select id="fs-support-kategori"><option value="" disabled selected>Pilih kategori</option><option>Teknis</option><option>Akun</option><option>Produksi</option><option>Lainnya</option></select></div>' +
      '<div class="input-group"><label>Pesan</label><textarea id="fs-support-pesan" placeholder="Jelaskan kendala Anda (minimal 10 karakter)" style="height:110px; resize:vertical;"></textarea></div>' +
      '<div class="fs-modal-actions">' +
        '<button type="button" class="fs-btn fs-btn-ghost" id="fs-support-cancel">Batal</button>' +
        '<button type="button" class="fs-btn fs-btn-primary" id="fs-support-submit">Kirim Pesan Bantuan</button>' +
      '</div>';
    createDynamicModal('fs-modal-support', 'Hubungi Support', html);
    openDynamicModal('fs-modal-support');
    document.getElementById('fs-support-cancel').addEventListener('click', closeSupportModal);
    document.getElementById('fs-support-submit').addEventListener('click', function (e) { submitSupportForm(e); });
  }
  function closeSupportModal() { closeDynamicModal('fs-modal-support'); }
  function submitSupportForm(event) {
    if (event && event.preventDefault) event.preventDefault();
    var modal = document.getElementById('fs-modal-support');
    if (!modal) return;
    var judul = modal.querySelector('#fs-support-judul');
    var kategori = modal.querySelector('#fs-support-kategori');
    var pesan = modal.querySelector('#fs-support-pesan');
    clearFormErrors(modal);

    var valid = true;
    if (!validateRequired(judul, 'Judul wajib diisi.')) valid = false;
    if (!validateRequired(kategori, 'Kategori wajib dipilih.')) valid = false;
    if (pesan.value.trim().length < 10) {
      showFieldError(pesan, 'Pesan minimal 10 karakter.');
      valid = false;
    }
    if (!valid) return;

    var submitBtn = modal.querySelector('#fs-support-submit');
    setButtonLoading(submitBtn, true, 'Mengirim...');
    setTimeout(function () {
      var tickets = storageGet(STORAGE_KEYS.SUPPORT_TICKETS, []);
      tickets.push({ judul: judul.value.trim(), kategori: kategori.value, pesan: pesan.value.trim(), waktu: new Date().toISOString() });
      storageSet(STORAGE_KEYS.SUPPORT_TICKETS, tickets);
      setButtonLoading(submitBtn, false);
      closeSupportModal();
      showToast('Pesan bantuan berhasil dikirim. Tim kami akan segera merespons.', 'success');
    }, 600);
  }
  function initHelpCenter() {
    var helpNav = document.querySelector('.nav-bottom .nav-item:not(.nav-logout)');
    if (helpNav) {
      helpNav.style.cursor = 'pointer';
      helpNav.addEventListener('click', function () { openSupportModal(); });
    }
  }

  /* ============================================================
     17. PROFIL SAYA
  ============================================================ */
  function applySavedProfile(view) {
    var data = storageGet(STORAGE_KEYS.PROFILE, null);
    if (!data) return;
    var infoCol = view.querySelector('.profile-info-col');
    var nama = getInfoGroup(infoCol, 'Nama Lengkap');
    var email = getInfoGroup(infoCol, 'Email');
    var telpon = getInfoGroup(infoCol, 'No. Telpon');
    if (nama && data.nama) nama.querySelector('p').textContent = data.nama;
    if (email && data.email) email.querySelector('p').textContent = data.email;
    if (telpon && data.telpon) telpon.querySelector('p').textContent = data.telpon;
    var userInfoH4 = document.querySelector('.user-info h4');
    if (userInfoH4 && data.nama) userInfoH4.textContent = data.nama;
  }

  function initProfil() {
    var view = document.getElementById('view-profil');
    if (!view) return;
    var editBtn = view.querySelector('.btn-action');
    var infoCol = view.querySelector('.profile-info-col');
    var ubahFotoLink = view.querySelector('.profile-avatar-col a');
    var profilePic = view.querySelector('.profile-pic');

    applySavedProfile(view);

    var originalValues = {};
    var saveBtn = null, cancelBtn = null;

    function getGroups() {
      return {
        nama: getInfoGroup(infoCol, 'Nama Lengkap'),
        email: getInfoGroup(infoCol, 'Email'),
        telpon: getInfoGroup(infoCol, 'No. Telpon')
      };
    }

    function exitEditMode(saved, data) {
      var groups = getGroups();
      var values = saved ? data : originalValues;
      ['nama', 'email', 'telpon'].forEach(function (field) {
        var group = groups[field];
        if (!group) return;
        var inputEl = group.querySelector('input');
        if (inputEl) inputEl.outerHTML = '<p>' + escapeHTML(values[field]) + '</p>';
      });
      if (saveBtn) { saveBtn.remove(); saveBtn = null; }
      if (cancelBtn) { cancelBtn.remove(); cancelBtn = null; }
      if (editBtn) editBtn.style.display = '';
    }

    function handleSaveProfile() {
      var namaInput = view.querySelector('.fs-edit-input[data-field="nama"]');
      var emailInput = view.querySelector('.fs-edit-input[data-field="email"]');
      var telponInput = view.querySelector('.fs-edit-input[data-field="telpon"]');
      clearFormErrors(view);
      var valid = true;
      if (!validateRequired(namaInput, 'Nama wajib diisi.')) valid = false;
      if (!validateEmail(emailInput, 'Format email tidak valid.')) valid = false;
      if (!valid) { showToast('Mohon periksa kembali data profil.', 'error'); return; }

      setButtonLoading(saveBtn, true, 'Menyimpan...');
      setTimeout(function () {
        var data = {
          nama: namaInput.value.trim(),
          email: emailInput.value.trim(),
          telpon: telponInput ? telponInput.value.trim() : ''
        };
        storageSet(STORAGE_KEYS.PROFILE, data);
        setButtonLoading(saveBtn, false);
        exitEditMode(true, data);
        var userInfoH4 = document.querySelector('.user-info h4');
        if (userInfoH4) userInfoH4.textContent = data.nama;
        showToast('Profil berhasil diperbarui.', 'success');
      }, 600);
    }

    if (editBtn) {
      editBtn.addEventListener('click', function () {
        var groups = getGroups();
        originalValues = {
          nama: groups.nama ? groups.nama.querySelector('p').textContent : '',
          email: groups.email ? groups.email.querySelector('p').textContent : '',
          telpon: groups.telpon ? groups.telpon.querySelector('p').textContent : ''
        };
        if (groups.nama) groups.nama.querySelector('p').outerHTML = '<input type="text" class="fs-edit-input" data-field="nama" value="' + escapeAttr(originalValues.nama) + '">';
        if (groups.email) groups.email.querySelector('p').outerHTML = '<input type="email" class="fs-edit-input" data-field="email" value="' + escapeAttr(originalValues.email) + '">';
        if (groups.telpon) groups.telpon.querySelector('p').outerHTML = '<input type="text" class="fs-edit-input" data-field="telpon" value="' + escapeAttr(originalValues.telpon) + '">';

        editBtn.style.display = 'none';
        saveBtn = document.createElement('button');
        saveBtn.className = 'btn-action';
        saveBtn.textContent = 'Simpan';
        saveBtn.style.marginLeft = '10px';
        cancelBtn = document.createElement('button');
        cancelBtn.className = 'btn-action';
        cancelBtn.textContent = 'Batal';
        cancelBtn.style.background = '#E5E7EB';
        cancelBtn.style.color = '#374151';
        cancelBtn.style.marginLeft = '10px';
        editBtn.insertAdjacentElement('afterend', cancelBtn);
        editBtn.insertAdjacentElement('afterend', saveBtn);

        cancelBtn.addEventListener('click', function () { exitEditMode(false); });
        saveBtn.addEventListener('click', handleSaveProfile);
      });
    }

    if (ubahFotoLink) {
      ubahFotoLink.addEventListener('click', function (e) {
        e.preventDefault();
        var fileInput = document.getElementById('fs-avatar-file-input');
        if (!fileInput) {
          fileInput = document.createElement('input');
          fileInput.type = 'file';
          fileInput.accept = 'image/*';
          fileInput.id = 'fs-avatar-file-input';
          fileInput.style.display = 'none';
          document.body.appendChild(fileInput);
        }
        fileInput.value = '';
        fileInput.onchange = function () {
          var file = fileInput.files[0];
          if (!file) return;
          var reader = new FileReader();
          reader.onload = function (ev) {
            [profilePic, document.querySelector('.avatar')].forEach(function (el) {
              if (!el) return;
              el.style.backgroundImage = 'url(' + ev.target.result + ')';
              el.style.backgroundSize = 'cover';
              el.style.backgroundPosition = 'center';
              var icon = el.querySelector('i');
              if (icon) icon.style.display = 'none';
            });
            showToast('Foto profil berhasil diperbarui.', 'success');
          };
          reader.readAsDataURL(file);
        };
        fileInput.click();
      });
    }
  }

  /* ============================================================
     18. KEAMANAN
  ============================================================ */
  function addPasswordToggle(input) {
    if (input.dataset.fsToggleAdded) return;
    input.dataset.fsToggleAdded = '1';
    var wrapper = document.createElement('div');
    wrapper.className = 'fs-pw-wrapper';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    var toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'fs-pw-toggle';
    toggle.innerHTML = '<i class="far fa-eye"></i>';
    wrapper.appendChild(toggle);
    toggle.addEventListener('click', function () {
      var isPw = input.type === 'password';
      input.type = isPw ? 'text' : 'password';
      toggle.innerHTML = isPw ? '<i class="far fa-eye-slash"></i>' : '<i class="far fa-eye"></i>';
    });
  }
  function initKeamanan() {
    var view = document.getElementById('view-keamanan');
    if (!view) return;
    view.querySelectorAll('input[type="password"]').forEach(addPasswordToggle);

    var submitBtn = view.querySelector('.btn-full');
    if (submitBtn) {
      submitBtn.addEventListener('click', function () {
        clearFormErrors(view);
        var current = getFieldByLabel(view, 'Password Saat Ini');
        var baru = getFieldByLabel(view, 'Password Baru');
        var konfirmasi = getFieldByLabel(view, 'Konfirmasi Password Baru');

        var valid = true;
        if (!validateRequired(current, 'Password saat ini wajib diisi.')) valid = false;
        if (!baru || baru.value.length < 8) { showFieldError(baru, 'Password baru minimal 8 karakter.'); valid = false; }
        if (!konfirmasi || konfirmasi.value !== (baru ? baru.value : '')) { showFieldError(konfirmasi, 'Konfirmasi password tidak sama.'); valid = false; }
        if (!valid) { showToast('Mohon periksa kembali data keamanan Anda.', 'error'); return; }

        setButtonLoading(submitBtn, true, 'Menyimpan...');
        setTimeout(function () {
          [current, baru, konfirmasi].forEach(function (i) { if (i) i.value = ''; });
          setButtonLoading(submitBtn, false);
          showToast('Password berhasil diubah.', 'success');
        }, 700);
      });
    }
  }

  /* ============================================================
     19. LOGOUT
  ============================================================ */
  function showLogoutConfirm() {
    var html =
      '<p style="margin-bottom:20px; color:#374151;">Apakah Anda yakin ingin keluar dari sistem FoodSync?</p>' +
      '<div class="fs-modal-actions">' +
        '<button type="button" class="fs-btn fs-btn-ghost" id="fs-logout-cancel">Batal</button>' +
        '<button type="button" class="fs-btn fs-btn-danger" id="fs-logout-confirm">Ya, Keluar</button>' +
      '</div>';
    createDynamicModal('fs-modal-logout', 'Konfirmasi Logout', html);
    openDynamicModal('fs-modal-logout');
    document.getElementById('fs-logout-cancel').addEventListener('click', function () {
      closeDynamicModal('fs-modal-logout');
      showToast('Logout dibatalkan.', 'info');
    });
    document.getElementById('fs-logout-confirm').addEventListener('click', function () {
      closeDynamicModal('fs-modal-logout');
      showToast('Anda berhasil keluar. Mengalihkan...', 'success');
      setTimeout(function () { window.location.href = 'index.html'; }, 700);
    });
  }
  function initLogout() {
    var sidebarLogout = document.querySelector('.nav-logout');
    var settingsLogout = document.querySelector('#modal-settings .list-item:last-child');
    function handleLogoutClick(e) {
      e.preventDefault();
      closeAllDropdowns();
      showLogoutConfirm();
    }
    if (sidebarLogout) sidebarLogout.addEventListener('click', handleLogoutClick);
    if (settingsLogout) settingsLogout.addEventListener('click', handleLogoutClick);
  }

  /* ============================================================
     20. SENTUHAN TAMBAHAN (Bahasa & filter chart dashboard)
  ============================================================ */
  function initMinorTouches() {
    var bahasaItem = document.querySelectorAll('#modal-settings .list-item')[2];
    if (bahasaItem) {
      bahasaItem.style.cursor = 'pointer';
      bahasaItem.addEventListener('click', function () {
        showToast('Fitur ganti bahasa akan tersedia segera.', 'info');
      });
    }
    var chartFilterBtn = document.querySelector('#view-dashboard .btn-filter');
    if (chartFilterBtn) {
      var options = ['This Week', 'This Month', 'This Year'];
      var idx = 0;
      chartFilterBtn.addEventListener('click', function () {
        idx = (idx + 1) % options.length;
        chartFilterBtn.innerHTML = options[idx] + ' <i class="fas fa-chevron-down" style="margin-left:6px;color:#9CA3AF;"></i>';
        showToast('Menampilkan data periode: ' + options[idx], 'info');
      });
    }
  }

  /* ============================================================
     21. EKSPOS FUNGSI GLOBAL (kompatibilitas)
  ============================================================ */
  window.switchView = switchView;
  window.toggleModal = toggleModal;
  window.showToast = showToast;
  window.filterHelpCategory = filterHelpCategory;
  window.searchHelpArticles = searchHelpArticles;
  window.toggleFAQ = toggleFAQ;
  window.openSupportModal = openSupportModal;
  window.closeSupportModal = closeSupportModal;
  window.submitSupportForm = submitSupportForm;
  window.createDynamicModal = createDynamicModal;
  window.openDynamicModal = openDynamicModal;
  window.closeDynamicModal = closeDynamicModal;
  window.exportTableToCSV = exportTableToCSV;
  window.setButtonLoading = setButtonLoading;
  window.showFieldError = showFieldError;
  window.clearFieldError = clearFieldError;
  window.clearFormErrors = clearFormErrors;
  window.validateRequired = validateRequired;
  window.validateNumber = validateNumber;
  window.validateEmail = validateEmail;
  window.closeAllDropdowns = closeAllDropdowns;

  /* ============================================================
     22. INISIALISASI
  ============================================================ */
  document.addEventListener('DOMContentLoaded', function () {
    safeRun(injectDynamicStyles);
    safeRun(initRencanaProduksi);
    safeRun(initMonitoringPerintahKerja);
    safeRun(initKanban);
    safeRun(initInputHasilProduksi);
    safeRun(initRiwayatProduksi);
    safeRun(initHelpCenter);
    safeRun(initProfil);
    safeRun(initKeamanan);
    safeRun(initLogout);
    safeRun(initGlobalSearch);
    safeRun(initNotifications);
    safeRun(initMinorTouches);
    safeRun(restoreVerifiedState);
    safeRun(recalcKanbanCounts);
  });

})();