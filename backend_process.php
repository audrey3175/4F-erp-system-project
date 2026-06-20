<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) { exit("Akses Ditolak"); }

$action = $_POST['action'] ?? '';
$initiator = $_SESSION['nama'] ?? 'System';
$today = date('Y-m-d');

// 1. Modul Accounts Receivable: Catat Pembayaran
if ($action == 'record_payment') {
    $invoice_number = $_POST['invoice_number'];
    $payment_amount = $_POST['payment_amount'];
    
    $query = mysqli_query($conn, "SELECT amount, paid_amount FROM accounts_receivable WHERE invoice_number='$invoice_number'");
    if(mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $new_paid = $data['paid_amount'] + $payment_amount;
        $status = ($new_paid >= $data['amount']) ? 'Paid' : 'Partially Paid';
        
        mysqli_query($conn, "UPDATE accounts_receivable SET paid_amount='$new_paid', status='$status' WHERE invoice_number='$invoice_number'");
    }
    header("Location: accounts-receivable.php?msg=success");
    exit();
}

// 2. Modul Treasury: Transfer Bank Internal
if ($action == 'create_transfer') {
    $from_bank = $_POST['from_bank'];
    $to_bank = $_POST['to_bank'];
    $amount = $_POST['amount'];
    $desc = "Transfer from $from_bank to $to_bank";
    $trx_id = 'TRX-' . rand(10000, 99999);
    
    mysqli_query($conn, "INSERT INTO recent_transactions (trans_date, transaction_id, description, initiator, amount, type, status) VALUES ('$today', '$trx_id', '$desc', '$initiator', '$amount', 'out', 'Completed')");
    
    header("Location: treasury.php?msg=transfer_success");
    exit();
}

// 3. Modul General Ledger: Jurnal Manual
if ($action == 'create_journal') {
    $account_code = $_POST['account_code'];
    $account_name = $_POST['account_name'];
    $type = $_POST['type']; // debit atau credit
    $amount = $_POST['amount'];
    $journal_num = 'GL-' . date('ym') . '-' . rand(100, 999);
    
    $debit = ($type == 'debit') ? $amount : 0;
    $credit = ($type == 'credit') ? $amount : 0;
    
    mysqli_query($conn, "INSERT INTO general_ledger (journal_number, date, account_code, account_name, debit, credit, status) VALUES ('$journal_num', '$today', '$account_code', '$account_name', '$debit', '$credit', 'Posted')");
    
    header("Location: general-ledger.php?msg=journal_success");
    exit();
}

// 4. Modul Approval: Proses Persetujuan/Penolakan
if ($action == 'process_approval') {
    $doc_number = $_POST['doc_number'];
    $status_update = $_POST['status_update']; // 'Approved' atau 'Rejected'
    
    mysqli_query($conn, "UPDATE approvals SET status='$status_update' WHERE doc_number='$doc_number'");
    
    header("Location: approval.php?msg=processed");
    exit();
}
?>