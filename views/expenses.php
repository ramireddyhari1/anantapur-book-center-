<?php
$expenses = $db->fetchAll("SELECT * FROM \"Expense\" ORDER BY date DESC");
$totalMonth = $db->fetch("SELECT SUM(amount) as total FROM \"Expense\" WHERE TO_CHAR(date, 'YYYY-MM') = TO_CHAR(CURRENT_DATE, 'YYYY-MM')")['total'] ?? 0;
$settledDues = $db->fetch("SELECT SUM(amount) as total FROM \"Expense\" WHERE status = 'settled'")['total'] ?? 0;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Expenses Ledger</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and track all organizational expenditures</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-print mr-1"></i> Print Report
            </button>
            <button onclick="document.getElementById('expenseModal').classList.remove('hidden')" class="bg-[#800000] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors">
                <i class="fa-solid fa-plus mr-1"></i> Add Expense
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase">Total Expenses (This Month)</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">₹<?= number_format($totalMonth, 2) ?></h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-[#1a7eb5] rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-chart-bar"></i>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase">Pending Approvals</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
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
            <h3 class="text-sm font-bold text-gray-700">Recent Transactions</h3>
            <div class="text-xs text-gray-500">Showing last 30 days</div>
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
                            <td class="px-6 py-4 text-center">
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase"><?= htmlspecialchars($exp['status']) ?></span>
                            </td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-folder-open text-3xl mb-3"></i>
                                <p class="text-sm">No expenses recorded yet.</p>
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
        <form action="index.php?page=expense_add" method="POST" class="p-6">
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
            </div>

            <button type="submit" class="w-full bg-[#800000] text-white py-4 rounded-xl font-bold mt-8 shadow-lg shadow-red-900/20 hover:bg-red-900 transition-all">
                Save Expense Record
            </button>
        </form>
    </div>
</div>
