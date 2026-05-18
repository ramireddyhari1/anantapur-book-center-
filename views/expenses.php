<?php
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? null;
$today = date('Y-m-d');

if ($isAdmin) {
    $expenses = $db->fetchAll("SELECT e.*, u.username as added_by FROM \"Expense\" e LEFT JOIN \"User\" u ON e.\"userId\" = u.id ORDER BY e.date DESC");
    $totalMonth = $db->fetch("SELECT SUM(amount) as total FROM \"Expense\" WHERE TO_CHAR(date, 'YYYY-MM') = TO_CHAR(CURRENT_DATE, 'YYYY-MM')")['total'] ?? 0;
    $settledDues = $db->fetch("SELECT SUM(amount) as total FROM \"Expense\" WHERE status = 'settled'")['total'] ?? 0;
} else {
    $expenses = $db->fetchAll("SELECT e.*, u.username as added_by FROM \"Expense\" e LEFT JOIN \"User\" u ON e.\"userId\" = u.id WHERE e.\"userId\" = ? AND DATE(e.date) = ? ORDER BY e.date DESC", [$userId, $today]);
    $totalMonth = $db->fetch("SELECT SUM(amount) as total FROM \"Expense\" WHERE \"userId\" = ? AND DATE(date) = ?", [$userId, $today])['total'] ?? 0;
    $settledDues = $db->fetch("SELECT SUM(amount) as total FROM \"Expense\" WHERE \"userId\" = ? AND DATE(date) = ? AND status = 'settled'", [$userId, $today])['total'] ?? 0;
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Expenses Ledger</h1>
            <p class="text-sm text-gray-500 mt-1"><?= $isAdmin ? 'Manage and track all organizational expenditures' : 'Submit and view your daily expenses' ?></p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <?php if ($isAdmin): ?>
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-print mr-1"></i> Print Report
            </button>
            <?php endif; ?>
            <button onclick="document.getElementById('expenseModal').classList.remove('hidden')" class="bg-[#800000] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors">
                <i class="fa-solid fa-plus mr-1"></i> Add Expense
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase"><?= $isAdmin ? 'Total Expenses (This Month)' : 'My Expenses Today' ?></p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">₹<?= number_format($totalMonth, 2) ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-[#1a7eb5] rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-chart-bar"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase"><?= $isAdmin ? 'Pending Approvals' : 'Entries Today' ?></p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $isAdmin ? '0' : count($expenses) ?></h3>
            </div>
            <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase">Settled Dues</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">₹<?= number_format($settledDues, 2) ?></h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-check-double"></i>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700"><?= $isAdmin ? 'All Transactions' : 'My Expenses Today' ?></h3>
            <div class="text-xs text-gray-500"><?= $isAdmin ? 'Showing all records' : date('d M, Y') ?></div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="px-6 py-3 font-semibold">Date</th>
                        <th class="px-6 py-3 font-semibold">Voucher No</th>
                        <th class="px-6 py-3 font-semibold">Description</th>
                        <th class="px-6 py-3 font-semibold">Category</th>
                        <th class="px-6 py-3 font-semibold">Amount</th>
                        <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 font-semibold">Added By</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 font-semibold text-center">File</th>
                        <th class="px-6 py-3 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if ($expenses): 
                        foreach ($expenses as $exp): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-600"><?= date('d M, Y', strtotime($exp['date'])) ?></td>
                            <td class="px-6 py-4 font-mono text-xs"><?= htmlspecialchars($exp['voucherNo']) ?></td>
                            <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($exp['description']) ?></td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><?= htmlspecialchars($exp['category']) ?></span>
                            </td>
                            <td class="px-6 py-4 font-bold text-gray-900">₹<?= number_format($exp['amount'], 2) ?></td>
                            <?php if ($isAdmin): ?>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-600 font-medium"><?= htmlspecialchars($exp['added_by'] ?? 'System') ?></span>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 text-center">
                                <?php if (!empty($exp['attachment'])): ?>
                                <a href="/uploads/expenses/<?= htmlspecialchars($exp['attachment']) ?>" target="_blank" class="text-blue-500 hover:text-blue-700" title="<?= htmlspecialchars($exp['attachment']) ?>">
                                    <i class="fa-solid fa-paperclip text-lg"></i>
                                </a>
                                <?php else: ?>
                                <span class="text-gray-300"><i class="fa-solid fa-minus"></i></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><?= htmlspecialchars($exp['status']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="<?= $isAdmin ? '8' : '7' ?>" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-3"></i>
                                <p class="text-sm"><?= $isAdmin ? 'No expenses recorded yet.' : 'No expenses added by you today.' ?></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div id="expenseModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
        <form action="index.php?page=expense_add" method="POST" enctype="multipart/form-data" class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Add New Expense</h3>
                <button type="button" onclick="document.getElementById('expenseModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                    <input type="text" name="description" required placeholder="e.g., Monthly Electricity Bill" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label>
                    <select name="category" required class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500 bg-white">
                        <option value="Salary">Staff Salary</option>
                        <option value="Stock">Stock Purchase</option>
                        <option value="Utilities">Utilities (Rent/Elec)</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Amount (₹)</label>
                        <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Voucher No</label>
                        <input type="text" name="voucherNo" placeholder="Optional" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                </div>

                <!-- Optional File Upload -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Attachment <span class="text-gray-400 normal-case font-normal">(Optional)</span></label>
                    <div id="dropZone" onclick="document.getElementById('expenseFile').click()" class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-lg px-3 py-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all">
                        <div id="uploadPlaceholder" class="flex flex-col items-center gap-1">
                            <i class="fa-solid fa-cloud-arrow-up text-xl text-gray-400"></i>
                            <span class="text-xs text-gray-500">Click to upload or drag & drop</span>
                            <span class="text-[10px] text-gray-400">PDF, Image, Excel, Word — Max 10MB</span>
                        </div>
                        <div id="uploadFileInfo" style="display:none" class="items-center gap-2 text-sm">
                            <i class="fa-solid fa-file text-blue-500"></i>
                            <span id="uploadFileName" class="text-gray-700 font-medium"></span>
                            <button type="button" onclick="clearFile(event)" class="text-red-400 hover:text-red-600 ml-1"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </div>
                    <input type="file" name="attachment" id="expenseFile" style="display:none" accept=".pdf,.jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.csv">
                </div>
            </div>

            <button type="submit" class="w-full bg-[#800000] text-white py-4 rounded-xl font-bold mt-8 shadow-lg shadow-red-900/20 hover:bg-red-900 transition-all">
                Save Expense Record
            </button>
        </form>
    </div>
</div>

<script>
const fileInput = document.getElementById('expenseFile');
const dropZone = document.getElementById('dropZone');
const placeholder = document.getElementById('uploadPlaceholder');
const fileInfo = document.getElementById('uploadFileInfo');
const fileName = document.getElementById('uploadFileName');

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        showFile(this.files[0].name);
    }
});

dropZone.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('border-blue-400', 'bg-blue-50/50');
});

dropZone.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50/50');
});

dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('border-blue-400', 'bg-blue-50/50');
    if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        showFile(e.dataTransfer.files[0].name);
    }
});

function showFile(name) {
    placeholder.style.display = 'none';
    fileInfo.style.display = 'flex';
    fileName.textContent = name;
}

function clearFile(e) {
    e.preventDefault();
    e.stopPropagation();
    fileInput.value = '';
    placeholder.style.display = 'flex';
    fileInfo.style.display = 'none';
}
</script>
