<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role'])) { exit("Akses Ditolak"); }

$type = $_GET['type'] ?? '';
$date_suffix = date('Ymd');

function set_csv_headers($filename) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
}

$output = fopen('php://output', 'w');

if ($type == 'ar') {
    set_csv_headers("Accounts_Receivable_$date_suffix.csv");
    fputcsv($output, array('No. Invoice', 'Customer', 'Invoice Date', 'Due Date', 'Amount', 'Paid Amount', 'Status'));
    $query = mysqli_query($conn, "SELECT invoice_number, customer, invoice_date, due_date, amount, paid_amount, status FROM accounts_receivable");
    while ($row = mysqli_fetch_assoc($query)) { fputcsv($output, $row); }
} 
elseif ($type == 'ap') {
    set_csv_headers("Accounts_Payable_$date_suffix.csv");
    fputcsv($output, array('No. Invoice', 'PO Reference', 'Vendor', 'Due Date', 'Amount', 'Status'));
    $query = mysqli_query($conn, "SELECT no_invoice, po_reference, vendor_name, due_date, amount, status FROM invoice_vendor");
    while ($row = mysqli_fetch_assoc($query)) { fputcsv($output, $row); }
}
elseif ($type == 'treasury') {
    set_csv_headers("Treasury_Transactions_$date_suffix.csv");
    fputcsv($output, array('Transaction Date', 'Transaction ID', 'Description', 'Initiator', 'Amount', 'Type', 'Status'));
    $query = mysqli_query($conn, "SELECT trans_date, transaction_id, description, initiator, amount, type, status FROM recent_transactions");
    while ($row = mysqli_fetch_assoc($query)) { fputcsv($output, $row); }
}
elseif ($type == 'gl') {
    set_csv_headers("General_Ledger_$date_suffix.csv");
    fputcsv($output, array('Journal Number', 'Date', 'Account Code', 'Account Name', 'Debit', 'Credit', 'Status'));
    $query = mysqli_query($conn, "SELECT journal_number, date, account_code, account_name, debit, credit, status FROM general_ledger");
    while ($row = mysqli_fetch_assoc($query)) { fputcsv($output, $row); }
}
elseif ($type == 'approval') {
    set_csv_headers("Approval_Queue_$date_suffix.csv");
    fputcsv($output, array('Document Number', 'Document Type', 'Requestor', 'Submission Date', 'Amount', 'Status'));
    $query = mysqli_query($conn, "SELECT doc_number, doc_type, requestor, submission_date, amount, status FROM approvals");
    while ($row = mysqli_fetch_assoc($query)) { fputcsv($output, $row); }
}
else {
    echo "Modul export tidak ditemukan.";
}

fclose($output);
exit();
?>