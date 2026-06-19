-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Jun 2026 pada 20.40
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hr_foodsync`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `karyawan`
--

CREATE TABLE `karyawan` (
  `id_karyawan` varchar(20) NOT NULL,
  `nik` varchar(30) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `departemen` varchar(50) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `role_akun` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `karyawan`
--

INSERT INTO `karyawan` (`id_karyawan`, `nik`, `nama_lengkap`, `email`, `password`, `departemen`, `jabatan`, `role_akun`) VALUES
('KRY-0001', '11111111', 'Nayla HRD', 'admin@indofood.com', '12345', 'HRD', 'HR Manager', 'HRD'),
('KRY-0002', '22222222', 'Budi Karyawan', 'budi@indofood.com', '12345', 'Manufacturing', 'Staff Produksi', 'Karyawan'),
('KRY-0003', '', 'Andi Wijaya', 'andi.wijaya@foodsync.com', '$2y$10$8Wv6pZ07S1mshg7kZ.7Ceu0bI6XU/yYvS6O0W.I6K1E1pB9V9L6K6', 'Manufacturing', 'Operator Produksi', 'Karyawan'),
('KRY-0004', '', 'Siti Rahmawati', 'siti.rahma@foodsync.com', '$2y$10$8Wv6pZ07S1mshg7kZ.7Ceu0bI6XU/yYvS6O0W.I6K1E1pB9V9L6K6', 'Manufacturing', 'Staff Gudang', 'Karyawan'),
('KRY-0005', '', 'Rian Hidayat', 'rian.hidayat@foodsync.com', '$2y$10$8Wv6pZ07S1mshg7kZ.7Ceu0bI6XU/yYvS6O0W.I6K1E1pB9V9L6K6', 'HRD', 'Staff Administrasi', 'Karyawan'),
('KRY-0006', '', 'Dewi Lestari', 'dewi.lestari@foodsync.com', '$2y$10$8Wv6pZ07S1mshg7kZ.7Ceu0bI6XU/yYvS6O0W.I6K1E1pB9V9L6K6', 'Finance', 'Accounting Staff', 'Finance'),
('KRY-0007', '', 'Eko Prasetyo', 'eko.prasetyo@foodsync.com', '$2y$10$8Wv6pZ07S1mshg7kZ.7Ceu0bI6XU/yYvS6O0W.I6K1E1pB9V9L6K6', 'Quality Control', 'QC Inspector Lead', 'Supervisor'),
('KRY-0008', '', 'Fajar Nugraha', 'fajar.nugraha@foodsync.com', '$2y$10$8Wv6pZ07S1mshg7kZ.7Ceu0bI6XU/yYvS6O0W.I6K1E1pB9V9L6K6', 'Quality Control', 'QC Staff', 'Karyawan'),
('KRY-2236', '58397418', 'supervisor .', 'supervisor@foodsync', '$2y$10$3gS4UdX65kyK6LPzCq0qwutEs0hkl86.ncrEoQ7jTJFpU/FifXdYC', 'Manufacturing', 'Staff Baru', 'Supervisor'),
('KRY-4109', '60793392', 'finance .', 'finance@foodsync', '$2y$10$Ja3f7CghVuXooBjTWMpQ5OGPMjcZVOIFwF6MVs02SC339fgWN38ga', 'Manufacturing', 'Staff Baru', 'Finance'),
('KRY-9576', '59556864', 'testing aja', 'testing@testing', '$2y$10$OkZKMK9wO8orpz.MTApKc.dEGCeoweHwdxRBsftb6.couh1Uc.Mj.', 'Belum Ditentukan', 'Staff Baru', 'Karyawan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_absensi`
--

CREATE TABLE `tb_absensi` (
  `id_absen` int(11) NOT NULL,
  `id_karyawan` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_keluar` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_absensi`
--

INSERT INTO `tb_absensi` (`id_absen`, `id_karyawan`, `tanggal`, `jam_masuk`, `jam_keluar`) VALUES
(3, 'KRY-0002', '2026-06-16', '22:42:52', '22:42:55'),
(4, 'KRY-2236', '2026-06-17', '00:50:39', '00:50:43'),
(5, 'KRY-0002', '2026-06-20', '07:45:12', '17:02:45'),
(6, 'KRY-0003', '2026-06-20', '07:52:30', NULL),
(7, 'KRY-0004', '2026-06-20', '08:15:22', NULL),
(8, 'KRY-4109', '2026-06-20', '07:30:00', '17:00:10'),
(9, 'KRY-2236', '2026-06-20', '08:04:15', NULL),
(10, 'KRY-0008', '2026-06-20', '08:45:00', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_cuti`
--

CREATE TABLE `tb_cuti` (
  `id_cuti` int(11) NOT NULL,
  `id_karyawan` varchar(20) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `status` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_cuti`
--

INSERT INTO `tb_cuti` (`id_cuti`, `id_karyawan`, `tgl_mulai`, `tgl_selesai`, `alasan`, `status`) VALUES
(1, 'KRY-0002', '2026-06-16', '2026-06-17', 'acara keluarga', 'Disetujui'),
(2, 'KRY-2236', '2026-06-17', '2026-06-18', 'testing aja', 'Disetujui'),
(3, 'KRY-0002', '2026-06-25', '2026-06-27', 'Acara pernikahan kandung luar kota', 'Pending'),
(4, 'KRY-0003', '2026-07-02', '2026-07-03', 'Renovasi rumah bocor parah', 'Pending'),
(5, 'KRY-0004', '2026-06-10', '2026-06-12', 'Urusan keluarga mendesak', 'Disetujui'),
(6, 'KRY-2236', '2026-06-29', '2026-07-01', 'Cuti tahunan luar kota', 'Pending'),
(7, 'KRY-0008', '2026-06-15', '2026-06-16', 'Mudik awal ke kampung halaman', 'Ditolak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_payroll`
--

CREATE TABLE `tb_payroll` (
  `id_payroll` int(11) NOT NULL,
  `id_karyawan` varchar(20) NOT NULL,
  `periode` varchar(20) NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL,
  `tunjangan_transportasi` decimal(15,2) DEFAULT 0.00,
  `tunjangan_makan` decimal(15,2) DEFAULT 0.00,
  `bonus_kinerja` decimal(15,2) DEFAULT 0.00,
  `potongan_bpjs` decimal(15,2) DEFAULT 0.00,
  `potongan_pajak` decimal(15,2) DEFAULT 0.00,
  `potongan_lainnya` decimal(15,2) DEFAULT 0.00,
  `total_gaji_bersih` decimal(15,2) NOT NULL,
  `status_pembayaran` enum('Pending','Menunggu Persetujuan','Siap Transfer','Selesai','Valid') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_reimbursement`
--

CREATE TABLE `tb_reimbursement` (
  `id_reimburse` int(11) NOT NULL,
  `id_karyawan` varchar(20) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `keterangan` text NOT NULL,
  `bukti_foto` varchar(255) NOT NULL,
  `status` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_reimbursement`
--

INSERT INTO `tb_reimbursement` (`id_reimburse`, `id_karyawan`, `nominal`, `keterangan`, `bukti_foto`, `status`) VALUES
(1, 'KRY-0002', 20000.00, 'contoh ', '', 'Ditolak'),
(2, 'KRY-0002', 150000.00, 'Beli bensin truk logistik pengiriman barang', 'bensin.jpg', 'Pending'),
(3, 'KRY-0003', 450000.00, 'Pembelian komponen sparepart mesin packing yang rusak', 'sparepart.jpg', 'Pending'),
(4, 'KRY-0004', 85000.00, 'Konsumsi rapat internal divisi manufaktur', 'snack.jpg', 'Disetujui'),
(5, 'KRY-4109', 1250000.00, 'Beli Lisensi Software Akuntansi Bulanan Perusahaan', 'software.jpg', 'Pending'),
(6, 'KRY-0008', 350000.00, 'Biaya servis printer pribadi di rumah', 'servis.jpg', 'Ditolak');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id_karyawan`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  ADD PRIMARY KEY (`id_absen`),
  ADD KEY `fk_absensi_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `tb_cuti`
--
ALTER TABLE `tb_cuti`
  ADD PRIMARY KEY (`id_cuti`),
  ADD KEY `fk_cuti_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `tb_payroll`
--
ALTER TABLE `tb_payroll`
  ADD PRIMARY KEY (`id_payroll`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `tb_reimbursement`
--
ALTER TABLE `tb_reimbursement`
  ADD PRIMARY KEY (`id_reimburse`),
  ADD KEY `fk_reimburse_karyawan` (`id_karyawan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  MODIFY `id_absen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `tb_cuti`
--
ALTER TABLE `tb_cuti`
  MODIFY `id_cuti` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tb_payroll`
--
ALTER TABLE `tb_payroll`
  MODIFY `id_payroll` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `tb_reimbursement`
--
ALTER TABLE `tb_reimbursement`
  MODIFY `id_reimburse` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  ADD CONSTRAINT `fk_absensi_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_cuti`
--
ALTER TABLE `tb_cuti`
  ADD CONSTRAINT `fk_cuti_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_payroll`
--
ALTER TABLE `tb_payroll`
  ADD CONSTRAINT `tb_payroll_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `tb_karyawan` (`id_karyawan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tb_reimbursement`
--
ALTER TABLE `tb_reimbursement`
  ADD CONSTRAINT `fk_reimburse_karyawan` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
