<?php
$customers = $db->fetchAll("SELECT * FROM \"Customer\" ORDER BY name ASC");
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Customer Registry</h1>
            <p class="text-sm text-gray-500 mt-1">Manage customer profiles and account balances</p>
        </div>
        <button onclick="document.getElementById('custModal').classList.remove('hidden')" class="bg-[#1a7eb5] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-blue-700 transition-colors">
            <i class="fa-solid fa-user-plus mr-1"></i> Add New Customer
        </button>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="px-6 py-3 font-semibold">Customer Name</th>
                        <th class="px-6 py-3 font-semibold">Contact Info</th>
                        <th class="px-6 py-3 font-semibold">GSTIN</th>
                        <th class="px-6 py-3 font-semibold text-right">Balance</th>
                        <th class="px-6 py-3 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (count($customers) > 0): ?>
                        <?php foreach ($customers as $c): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($c['name']) ?></td>
                            <td class="px-6 py-4">
                                <div class="text-xs"><?= htmlspecialchars($c['phone'] ?? '-') ?></div>
                                <div class="text-[10px] text-gray-400"><?= htmlspecialchars($c['email'] ?? '-') ?></div>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono"><?= htmlspecialchars($c['gstin'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-right font-bold <?= $c['balance'] >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                                ₹<?= number_format($c['balance'], 2) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="index.php?page=ledger&entityId=<?= $c['id'] ?>&type=customer" class="text-gray-400 hover:text-[#1a7eb5]"><i class="fa-solid fa-book"></i></a>
                                    <button class="text-gray-400 hover:text-blue-600"><i class="fa-solid fa-pen"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No customers found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Customer Modal (Reused) -->
<div id="custModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">New Customer Profile</h3>
            <button onclick="document.getElementById('custModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="index.php?page=customer_save" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name *</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone</label>
                    <input type="text" name="phone" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">GSTIN</label>
                    <input type="text" name="gstin" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                <input type="email" name="email" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Address</label>
                <textarea name="address" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]" rows="2"></textarea>
            </div>
            <button type="submit" class="w-full bg-[#1a7eb5] text-white py-2 rounded font-semibold text-sm hover:bg-blue-700 transition-colors">Create Profile</button>
        </form>
    </div>
</div>
