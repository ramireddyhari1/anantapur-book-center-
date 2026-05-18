<?php

require_once __DIR__ . '/src/Database.php';

use App\Database;

$db = new Database();

// For schema creation, we should use exec or multiple calls
$schema = [
    "CREATE TABLE IF NOT EXISTS \"Report\" (
        \"id\" TEXT PRIMARY KEY,
        \"date\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        \"filename\" TEXT NOT NULL,
        \"month\" INTEGER,
        \"year\" INTEGER,
        \"startDate\" TEXT,
        \"endDate\" TEXT
    )",
    "CREATE TABLE IF NOT EXISTS \"ReportItem\" (
        \"id\" TEXT PRIMARY KEY,
        \"reportId\" TEXT NOT NULL,
        \"description\" TEXT NOT NULL,
        \"openingQty\" DOUBLE PRECISION DEFAULT 0,
        \"openingValue\" DOUBLE PRECISION DEFAULT 0,
        \"receiptQty\" DOUBLE PRECISION DEFAULT 0,
        \"receiptValue\" DOUBLE PRECISION DEFAULT 0,
        \"issueQty\" DOUBLE PRECISION DEFAULT 0,
        \"issueValue\" DOUBLE PRECISION DEFAULT 0,
        \"closingQty\" DOUBLE PRECISION DEFAULT 0,
        \"closingValue\" DOUBLE PRECISION DEFAULT 0,
        CONSTRAINT \"ReportItem_reportId_fkey\" FOREIGN KEY (\"reportId\") REFERENCES \"Report\"(\"id\") ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS \"User\" (
        \"id\" TEXT PRIMARY KEY,
        \"username\" TEXT UNIQUE NOT NULL,
        \"password\" TEXT NOT NULL,
        \"role\" TEXT DEFAULT 'worker'
    )",
    "CREATE TABLE IF NOT EXISTS \"Attendance\" (
        \"id\" TEXT PRIMARY KEY,
        \"userId\" TEXT NOT NULL,
        \"slot\" INTEGER NOT NULL,
        \"timestamp\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        \"photo\" TEXT,
        \"lat\" DOUBLE PRECISION,
        \"lng\" DOUBLE PRECISION,
        CONSTRAINT \"Attendance_userId_fkey\" FOREIGN KEY (\"userId\") REFERENCES \"User\"(\"id\") ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS \"AttendanceSettings\" (
        \"id\" TEXT PRIMARY KEY,
        \"slot1_start\" TEXT,
        \"slot1_end\" TEXT,
        \"slot2_start\" TEXT,
        \"slot2_end\" TEXT,
        \"slot3_start\" TEXT,
        \"slot3_end\" TEXT
    )",
    "CREATE TABLE IF NOT EXISTS \"Expense\" (
        \"id\" TEXT PRIMARY KEY,
        \"date\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        \"description\" TEXT,
        \"category\" TEXT,
        \"amount\" DOUBLE PRECISION DEFAULT 0,
        \"status\" TEXT DEFAULT 'settled',
        \"voucherNo\" TEXT,
        \"attachment\" TEXT
    )",
    "CREATE TABLE IF NOT EXISTS \"Task\" (
        \"id\" TEXT PRIMARY KEY,
        \"userId\" TEXT NOT NULL,
        \"title\" TEXT NOT NULL,
        \"description\" TEXT,
        \"priority\" TEXT DEFAULT 'Medium',
        \"status\" TEXT DEFAULT 'Pending',
        \"created_at\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT \"Task_userId_fkey\" FOREIGN KEY (\"userId\") REFERENCES \"User\" (\"id\") ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS \"Category\" (
        \"id\" TEXT PRIMARY KEY,
        \"name\" TEXT NOT NULL UNIQUE
    )",
    "CREATE TABLE IF NOT EXISTS \"Product\" (
        \"id\" TEXT PRIMARY KEY,
        \"sku\" TEXT UNIQUE,
        \"name\" TEXT NOT NULL,
        \"categoryId\" TEXT,
        \"hsn\" TEXT,
        \"gstRate\" DOUBLE PRECISION DEFAULT 18,
        \"costPrice\" DOUBLE PRECISION DEFAULT 0,
        \"sellingPrice\" DOUBLE PRECISION DEFAULT 0,
        \"stockQty\" DOUBLE PRECISION DEFAULT 0,
        \"barcode\" TEXT UNIQUE,
        \"expiryDate\" DATE,
        CONSTRAINT \"Product_categoryId_fkey\" FOREIGN KEY (\"categoryId\") REFERENCES \"Category\"(\"id\") ON DELETE SET NULL
    )",
    "CREATE TABLE IF NOT EXISTS \"Customer\" (
        \"id\" TEXT PRIMARY KEY,
        \"name\" TEXT NOT NULL,
        \"phone\" TEXT,
        \"email\" TEXT,
        \"gstin\" TEXT,
        \"address\" TEXT,
        \"balance\" DOUBLE PRECISION DEFAULT 0
    )",
    "CREATE TABLE IF NOT EXISTS \"Supplier\" (
        \"id\" TEXT PRIMARY KEY,
        \"name\" TEXT NOT NULL,
        \"phone\" TEXT,
        \"gstin\" TEXT,
        \"address\" TEXT,
        \"balance\" DOUBLE PRECISION DEFAULT 0
    )",
    "CREATE TABLE IF NOT EXISTS \"Invoice\" (
        \"id\" TEXT PRIMARY KEY,
        \"invoiceNo\" TEXT UNIQUE NOT NULL,
        \"date\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        \"customerId\" TEXT,
        \"totalAmount\" DOUBLE PRECISION DEFAULT 0,
        \"taxAmount\" DOUBLE PRECISION DEFAULT 0,
        \"discountAmount\" DOUBLE PRECISION DEFAULT 0,
        \"paymentStatus\" TEXT DEFAULT 'unpaid',
        \"paymentMode\" TEXT,
        CONSTRAINT \"Invoice_customerId_fkey\" FOREIGN KEY (\"customerId\") REFERENCES \"Customer\"(\"id\") ON DELETE SET NULL
    )",
    "CREATE TABLE IF NOT EXISTS \"InvoiceItem\" (
        \"id\" TEXT PRIMARY KEY,
        \"invoiceId\" TEXT NOT NULL,
        \"productId\" TEXT NOT NULL,
        \"qty\" DOUBLE PRECISION NOT NULL,
        \"rate\" DOUBLE PRECISION NOT NULL,
        \"taxRate\" DOUBLE PRECISION DEFAULT 0,
        \"taxAmount\" DOUBLE PRECISION DEFAULT 0,
        \"total\" DOUBLE PRECISION NOT NULL,
        CONSTRAINT \"InvoiceItem_invoiceId_fkey\" FOREIGN KEY (\"invoiceId\") REFERENCES \"Invoice\"(\"id\") ON DELETE CASCADE,
        CONSTRAINT \"InvoiceItem_productId_fkey\" FOREIGN KEY (\"productId\") REFERENCES \"Product\"(\"id\")
    )",
    "CREATE TABLE IF NOT EXISTS \"AccountLedger\" (
        \"id\" TEXT PRIMARY KEY,
        \"entityId\" TEXT NOT NULL,
        \"entityType\" TEXT NOT NULL, -- 'customer' or 'supplier'
        \"date\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        \"description\" TEXT,
        \"debit\" DOUBLE PRECISION DEFAULT 0,
        \"credit\" DOUBLE PRECISION DEFAULT 0,
        \"balance\" DOUBLE PRECISION DEFAULT 0,
        \"referenceId\" TEXT -- InvoiceId or PaymentId
    )",
    "CREATE TABLE IF NOT EXISTS \"Salary\" (
        \"id\" TEXT PRIMARY KEY,
        \"userId\" TEXT NOT NULL,
        \"month\" INTEGER NOT NULL,
        \"year\" INTEGER NOT NULL,
        \"amount\" DOUBLE PRECISION DEFAULT 0,
        \"status\" TEXT DEFAULT 'pending', -- 'pending', 'paid'
        \"paidDate\" TIMESTAMP,
        CONSTRAINT \"Salary_userId_fkey\" FOREIGN KEY (\"userId\") REFERENCES \"User\"(\"id\") ON DELETE CASCADE
    )"
];

try {
    foreach ($schema as $sql) {
        $db->query($sql);
    }
    echo "Database schema initialized successfully!\n";
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
}
