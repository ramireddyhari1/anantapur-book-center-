<?php
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BillingController.php';

use App\Database;
use App\BillingController;
use PhpOffice\PhpSpreadsheet\IOFactory;

$db = new Database();
$billing = new BillingController($db);

$db->query("CREATE TABLE IF NOT EXISTS \"User\" (
    \"id\" TEXT PRIMARY KEY,
    \"username\" TEXT UNIQUE NOT NULL,
    \"password\" TEXT NOT NULL,
    \"role\" TEXT DEFAULT 'worker'
)");

$db->query("CREATE TABLE IF NOT EXISTS \"Attendance\" (
    \"id\" TEXT PRIMARY KEY,
    \"userId\" TEXT NOT NULL,
    \"slot\" INTEGER NOT NULL,
    \"timestamp\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    \"photo\" TEXT,
    \"lat\" DOUBLE PRECISION,
    \"lng\" DOUBLE PRECISION,
    CONSTRAINT \"Attendance_userId_fkey\" FOREIGN KEY (\"userId\") REFERENCES \"User\" (\"id\") ON DELETE CASCADE
)");

$db->query("CREATE TABLE IF NOT EXISTS \"AttendanceSettings\" (
    \"id\" TEXT PRIMARY KEY,
    \"slot1_start\" TEXT,
    \"slot1_end\" TEXT,
    \"slot2_start\" TEXT,
    \"slot2_end\" TEXT,
    \"slot3_start\" TEXT,
    \"slot3_end\" TEXT
)");

// Migration: Drop unused slot3 columns if they exist (soft — we just ignore them in code)

$db->query("CREATE TABLE IF NOT EXISTS \"Expense\" (
    \"id\" TEXT PRIMARY KEY,
    \"date\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    \"description\" TEXT,
    \"category\" TEXT,
    \"amount\" DOUBLE PRECISION DEFAULT 0,
    \"status\" TEXT DEFAULT 'settled',
    \"voucherNo\" TEXT,
    \"attachment\" TEXT
)");

// Migration: Add attachment column if it doesn't exist
try { $db->query("ALTER TABLE \"Expense\" ADD COLUMN \"attachment\" TEXT"); } catch (Exception $e) { /* column already exists */ }

$db->query("CREATE TABLE IF NOT EXISTS \"Task\" (
    \"id\" TEXT PRIMARY KEY,
    \"userId\" TEXT NOT NULL,
    \"title\" TEXT NOT NULL,
    \"description\" TEXT,
    \"priority\" TEXT DEFAULT 'Medium',
    \"status\" TEXT DEFAULT 'Pending',
    \"created_at\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT \"Task_userId_fkey\" FOREIGN KEY (\"userId\") REFERENCES \"User\" (\"id\") ON DELETE CASCADE
)");

$db->query("CREATE TABLE IF NOT EXISTS \"Category\" (
    \"id\" TEXT PRIMARY KEY,
    \"name\" TEXT NOT NULL UNIQUE
)");

$db->query("CREATE TABLE IF NOT EXISTS \"Product\" (
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
)");

$db->query("CREATE TABLE IF NOT EXISTS \"Customer\" (
    \"id\" TEXT PRIMARY KEY,
    \"name\" TEXT NOT NULL,
    \"phone\" TEXT,
    \"email\" TEXT,
    \"gstin\" TEXT,
    \"address\" TEXT,
    \"balance\" DOUBLE PRECISION DEFAULT 0
)");

$db->query("CREATE TABLE IF NOT EXISTS \"Supplier\" (
    \"id\" TEXT PRIMARY KEY,
    \"name\" TEXT NOT NULL,
    \"phone\" TEXT,
    \"gstin\" TEXT,
    \"address\" TEXT,
    \"balance\" DOUBLE PRECISION DEFAULT 0
)");

$db->query("CREATE TABLE IF NOT EXISTS \"Invoice\" (
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
)");

$db->query("CREATE TABLE IF NOT EXISTS \"InvoiceItem\" (
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
)");

$db->query("CREATE TABLE IF NOT EXISTS \"AccountLedger\" (
    \"id\" TEXT PRIMARY KEY,
    \"entityId\" TEXT NOT NULL,
    \"entityType\" TEXT NOT NULL,
    \"date\" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    \"description\" TEXT,
    \"debit\" DOUBLE PRECISION DEFAULT 0,
    \"credit\" DOUBLE PRECISION DEFAULT 0,
    \"balance\" DOUBLE PRECISION DEFAULT 0,
    \"referenceId\" TEXT
)");

$page = $_GET['page'] ?? 'dashboard';

// Handle POST request for upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'upload') {
    if (isset($_FILES['excel_file'])) {
        if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            echo "<h1>Upload Failed</h1><p>Error code: " . $_FILES['excel_file']['error'] . "</p><p>If error code is 1 or 2, the file is too large (exceeds php.ini limits).</p><a href='index.php?page=upload'>Go Back</a>";
            exit;
        }
        $file = $_FILES['excel_file'];
        $month = $_POST['month'] ?? date('n');
        $year = $_POST['year'] ?? date('Y');
        
        $spreadsheet = IOFactory::load($file['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();
        
        $reportId = uniqid('rep_');
        $db->query("INSERT INTO \"Report\" (id, filename, month, year) VALUES (?, ?, ?, ?)", [
            $reportId, $file['name'], $month, $year
        ]);
        
        // Skip header rows (Marg ERP starts at row 9 usually)
        for ($i = 8; $i < count($data); $i++) {
            $row = $data[$i];
            if (empty($row[0]) || strpos($row[0], 'GRAND TOTAL') !== false) continue;
            
            $db->query("INSERT INTO \"ReportItem\" (id, \"reportId\", description, \"openingQty\", \"openingValue\", \"receiptQty\", \"receiptValue\", \"issueQty\", \"issueValue\", \"closingQty\", \"closingValue\") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                uniqid('item_'),
                $reportId,
                trim($row[0]),
                floatval(str_replace(',', '', $row[1] ?? 0)),
                floatval(str_replace(',', '', $row[2] ?? 0)),
                floatval(str_replace(',', '', $row[3] ?? 0)),
                floatval(str_replace(',', '', $row[4] ?? 0)),
                floatval(str_replace(',', '', $row[5] ?? 0)),
                floatval(str_replace(',', '', $row[6] ?? 0)),
                floatval(str_replace(',', '', $row[7] ?? 0)),
                floatval(str_replace(',', '', $row[8] ?? 0))
            ]);
        }
        
        header("Location: index.php?page=dashboard&sync=success");
        exit;
    }
}

// Global Data fetching logic
if ($page === 'dashboard') {
    $latestReport = $db->fetch("SELECT * FROM \"Report\" ORDER BY date DESC LIMIT 1");
    $stats = ['total_items' => 0, 'total_value' => 0];

    if ($latestReport) {
        $items = $db->fetchAll("SELECT * FROM \"ReportItem\" WHERE \"reportId\" = ?", [$latestReport['id']]);
        $stats['total_items'] = count($items);
        $stats['total_value'] = array_reduce($items, fn($acc, $item) => $acc + $item['closingValue'], 0);
    }
}

session_start();

// Auto-seed default users for testing if database is empty
$adminCheck = $db->fetch("SELECT * FROM \"User\" WHERE username = 'admin'");
if (!$adminCheck) {
    $db->query("INSERT INTO \"User\" (id, username, password, role) VALUES (?, ?, ?, ?)", [uniqid('u_'), 'admin', 'password', 'admin']);
    $db->query("INSERT INTO \"User\" (id, username, password, role) VALUES (?, ?, ?, ?)", [uniqid('u_'), 'staff1', 'password', 'worker']);
}

// Auto-seed default attendance settings
$attendanceSettings = $db->fetch("SELECT * FROM \"AttendanceSettings\" LIMIT 1");
if (!$attendanceSettings) {
    $db->query("INSERT INTO \"AttendanceSettings\" (id, slot1_start, slot1_end, slot2_start, slot2_end) VALUES (?, ?, ?, ?, ?)", [
        'default', '08:00', '13:00', '08:00', '21:00'
    ]);
}

// Handle Authentication
$login_error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $user = $db->fetch("SELECT * FROM \"User\" WHERE username = ? AND password = ?", [$username, $password]);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php?page=dashboard");
        exit;
    } else {
        $login_error = "Invalid username or password";
    }
}

// Handle Logout
if ($page === 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// Handle Attendance Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'attendance_submit') {
    $userId = $_SESSION['user_id'] ?? null;
    $slot = $_POST['slot'] ?? 0;
    $lat = $_POST['lat'] ?? null;
    $lng = $_POST['lng'] ?? null;
    $photoData = $_POST['photo'] ?? null;

    if ($userId && $photoData && $slot) {
        $img = str_replace('data:image/jpeg;base64,', '', $photoData);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $filename = 'att_' . $userId . '_' . time() . '.jpg';
        $uploadDir = __DIR__ . '/uploads/attendance/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        file_put_contents($uploadDir . $filename, $data);

        $db->query("INSERT INTO \"Attendance\" (id, \"userId\", slot, photo, lat, lng, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            uniqid('att_'), $userId, (int)$slot, $filename, (float)$lat, (float)$lng, date('Y-m-d H:i:s')
        ]);
        header("Location: index.php?page=staff_attendance&status=success");
        exit;
    }
}

// Handle Attendance Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'update_attendance_settings') {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $db->query("UPDATE \"AttendanceSettings\" SET slot1_start=?, slot1_end=?, slot2_start=?, slot2_end=? WHERE id='default'", [
            $_POST['slot1_start'], $_POST['slot1_end'],
            $_POST['slot2_start'], $_POST['slot2_end']
        ]);
        header("Location: index.php?page=attendance_settings&status=updated");
        exit;
    }
}

// Handle Expense Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'expense_add') {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $attachmentName = null;

        // Handle optional file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/expenses/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $attachmentName = 'exp_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $attachmentName);
        }

        $db->query("INSERT INTO \"Expense\" (id, description, category, amount, \"voucherNo\", attachment) VALUES (?, ?, ?, ?, ?, ?)", [
            uniqid('exp_'),
            $_POST['description'],
            $_POST['category'],
            (float)$_POST['amount'],
            $_POST['voucherNo'] ?? ('V-' . time()),
            $attachmentName
        ]);
        header("Location: index.php?page=expenses&status=success");
        exit;
    }
}

// Handle Task Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'task_add') {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $db->query("INSERT INTO \"Task\" (id, \"userId\", title, description, priority) VALUES (?, ?, ?, ?, ?)", [
            uniqid('task_'),
            $_POST['userId'],
            $_POST['title'],
            $_POST['description'],
            $_POST['priority']
        ]);
        header("Location: index.php?page=staff_task&status=created");
        exit;
    }
}

// Handle Task Update Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'task_update_status') {
    $taskId = $_POST['taskId'];
    $newStatus = $_POST['status'];
    
    // Check if task belongs to user or if user is admin
    $task = $db->fetch("SELECT * FROM \"Task\" WHERE id = ?", [$taskId]);
    if ($task && ($task['userId'] === $_SESSION['user_id'] || $_SESSION['role'] === 'admin')) {
        $db->query("UPDATE \"Task\" SET status = ? WHERE id = ?", [$newStatus, $taskId]);
        header("Location: index.php?page=staff_task&status=updated");
        exit;
    }
}

// Handle Product Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'product_save') {
    $id = $_POST['id'] ?: uniqid('prod_');
    $name = $_POST['name'];
    $sku = $_POST['sku'] ?: null;
    $barcode = $_POST['barcode'] ?: null;
    $categoryId = $_POST['categoryId'] ?: null;
    $hsn = $_POST['hsn'] ?: null;
    $costPrice = (float)($_POST['costPrice'] ?? 0);
    $sellingPrice = (float)($_POST['sellingPrice'] ?? 0);
    $gstRate = (float)($_POST['gstRate'] ?? 18);
    $stockQty = (float)($_POST['stockQty'] ?? 0);
    $expiryDate = $_POST['expiryDate'] ?: null;

    if ($_POST['id']) {
        $db->query("UPDATE \"Product\" SET name=?, sku=?, barcode=?, \"categoryId\"=?, hsn=?, \"costPrice\"=?, \"sellingPrice\"=?, \"gstRate\"=?, \"stockQty\"=?, \"expiryDate\"=? WHERE id=?", [
            $name, $sku, $barcode, $categoryId, $hsn, $costPrice, $sellingPrice, $gstRate, $stockQty, $expiryDate, $_POST['id']
        ]);
    } else {
        $db->query("INSERT INTO \"Product\" (id, name, sku, barcode, \"categoryId\", hsn, \"costPrice\", \"sellingPrice\", \"gstRate\", \"stockQty\", \"expiryDate\") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $id, $name, $sku, $barcode, $categoryId, $hsn, $costPrice, $sellingPrice, $gstRate, $stockQty, $expiryDate
        ]);
    }
    header("Location: index.php?page=products&status=saved");
    exit;
}

// Handle Category Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'category_save') {
    $name = $_POST['name'];
    $db->query("INSERT INTO \"Category\" (id, name) VALUES (?, ?)", [uniqid('cat_'), $name]);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Handle Invoice Create (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'invoice_create') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    $result = $billing->createInvoice($data);
    echo json_encode($result);
    exit;
}

// Handle Customer Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'customer_save') {
    $billing->saveCustomer($_POST['name'], $_POST['phone'] ?? null, $_POST['email'] ?? null, $_POST['gstin'] ?? null, $_POST['address'] ?? null);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Handle Supplier Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'supplier_save') {
    $billing->saveSupplier($_POST['name'], $_POST['phone'] ?? null, $_POST['gstin'] ?? null, $_POST['address'] ?? null);
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Handle Purchase Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'purchase_save') {
    $result = $billing->recordPurchase($_POST);
    if ($result['success']) {
        header("Location: index.php?page=inventory&status=purchased");
    } else {
        echo "Error: " . $result['message'];
    }
    exit;
}

// Handle Staff Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'staff_save') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'worker';
    $db->query("INSERT INTO \"User\" (id, username, password, role) VALUES (?, ?, ?, ?)", [uniqid('u_'), $username, $password, $role]);
    header("Location: index.php?page=staff&status=created");
    exit;
}

// Handle Staff Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'staff_update') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $role = $_POST['role'] ?? 'worker';
    
    if (!empty($_POST['password'])) {
        $db->query("UPDATE \"User\" SET username = ?, password = ?, role = ? WHERE id = ?", [$username, $_POST['password'], $role, $id]);
    } else {
        $db->query("UPDATE \"User\" SET username = ?, role = ? WHERE id = ?", [$username, $role, $id]);
    }
    header("Location: index.php?page=staff&status=updated");
    exit;
}

// Handle Salary Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'salary_update') {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $userId = $_POST['userId'];
        $month = (int)$_POST['month'];
        $year = (int)$_POST['year'];
        $amount = (float)$_POST['amount'];
        
        $existing = $db->fetch("SELECT id FROM \"Salary\" WHERE \"userId\" = ? AND month = ? AND year = ?", [$userId, $month, $year]);
        if ($existing) {
            $db->query("UPDATE \"Salary\" SET amount = ? WHERE id = ?", [$amount, $existing['id']]);
        } else {
            $db->query("INSERT INTO \"Salary\" (id, \"userId\", month, year, amount, status) VALUES (?, ?, ?, ?, ?, ?)", [
                uniqid('sal_'), $userId, $month, $year, $amount, 'pending'
            ]);
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    exit;
}

// Handle Salary Mark Paid
if ($page === 'salary_mark_paid' && isset($_GET['id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $db->query("UPDATE \"Salary\" SET status = 'paid', \"paidDate\" = CURRENT_TIMESTAMP WHERE id = ?", [$_GET['id']]);
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    exit;
}

// Handle Salary Clone
if ($page === 'salary_clone' && isset($_GET['month']) && isset($_GET['year'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $month = (int)$_GET['month'];
        $year = (int)$_GET['year'];
        
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth === 0) {
            $prevMonth = 12;
            $prevYear--;
        }
        
        $prevSalaries = $db->fetchAll("SELECT \"userId\", amount FROM \"Salary\" WHERE month = ? AND year = ?", [$prevMonth, $prevYear]);
        foreach ($prevSalaries as $ps) {
            $existing = $db->fetch("SELECT id FROM \"Salary\" WHERE \"userId\" = ? AND month = ? AND year = ?", [$ps['userId'], $month, $year]);
            if (!$existing) {
                $db->query("INSERT INTO \"Salary\" (id, \"userId\", month, year, amount, status) VALUES (?, ?, ?, ?, ?, ?)", [
                    uniqid('sal_'), $ps['userId'], $month, $year, $ps['amount'], 'pending'
                ]);
            }
        }
        header("Location: index.php?page=salaries&month=$month&year=$year&cloned=success");
    }
    exit;
}

// Handle Staff Delete
if ($page === 'staff_delete' && isset($_GET['id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $db->query("DELETE FROM \"User\" WHERE id = ? AND role != 'admin'", [$_GET['id']]);
        header("Location: index.php?page=staff&status=deleted");
    }
    exit;
}

// Middleware Guard: Redirect to login if not authenticated
if ($page !== 'login' && !isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

if ($page === 'login') {
    include __DIR__ . '/../views/login.php';
    exit;
}

include __DIR__ . '/../views/header.php';

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

switch ($page) {
    case 'dashboard':
        include __DIR__ . '/../views/dashboard.php';
        break;
    case 'inventory':
    case 'products':
    case 'product_new':
    case 'pos':
    case 'invoice_print':
    case 'customers':
    case 'suppliers':
    case 'purchases':
    case 'ledger':
    case 'gst_reports':
    case 'expenses':
    case 'analytics':
    case 'tools':
    case 'upload':
        if (!$isAdmin) {
            echo "<div class='py-40 text-center'><h1 class='text-4xl font-bold text-rose-600 uppercase'>Access Denied</h1><p class='text-gray-500'>This module is restricted to administrators only.</p></div>";
        } else {
            include __DIR__ . "/../views/{$page}.php";
            if ($page === 'invoice_print') exit;
        }
        break;
    case 'staff':
        include __DIR__ . '/../views/staff.php';
        break;
    case 'staff_attendance':
        include __DIR__ . '/../views/staff_attendance.php';
        break;
    case 'staff_task':
        include __DIR__ . '/../views/staff_task.php';
        break;
    case 'staff_new':
        include __DIR__ . '/../views/staff_new.php';
        break;
    case 'staff_edit':
        include __DIR__ . '/../views/staff_edit.php';
        break;
    case 'attendance_settings':
        include __DIR__ . '/../views/attendance_settings.php';
        break;
    case 'salaries':
        include __DIR__ . '/../views/salaries.php';
        break;
    case 'expenses':
        include __DIR__ . '/../views/expenses.php';
        break;
    case 'analytics':
        include __DIR__ . '/../views/analytics.php';
        break;
    case 'tools':
        include __DIR__ . '/../views/tools.php';
        break;
    case 'upload':
        include __DIR__ . '/../views/upload.php';
        break;
    default:
        echo "<div class='py-40 text-center'><h1 class='text-6xl font-black italic tracking-tighter opacity-20 uppercase'>Page Not <span class='text-[#800000]'>Found</span></h1></div>";
        break;
}

include __DIR__ . '/../views/footer.php';
