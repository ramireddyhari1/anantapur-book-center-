<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Inventory Master</h1>
            <p class="text-sm text-gray-500 mt-1">Global stock ledger and active SKU overview</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-file-export mr-1"></i> Export CSV
            </button>
            <?php if ($isAdmin): ?>
            <a href="index.php?page=upload" class="bg-[#800000] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors">
                <i class="fa-solid fa-rotate mr-1"></i> Sync Data
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase mb-2">Total SKU Count</p>
            <p class="text-2xl font-black text-gray-900"><?= number_format($stats['total_items'] ?? 0) ?></p>
        </div>
        <?php if ($isAdmin): ?>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase mb-2">Inventory Value</p>
            <p class="text-2xl font-black text-[#1a7eb5]">₹<?= number_format($stats['total_value'] ?? 0, 2) ?></p>
        </div>
        <?php endif; ?>
        <?php
        $latestReport = $db->fetch("SELECT id FROM \"Report\" ORDER BY date DESC LIMIT 1");
        $lowStockCount = 0;
        if ($latestReport) {
            $lowStockCount = $db->fetch("SELECT COUNT(*) as count FROM \"ReportItem\" WHERE \"reportId\" = ? AND \"closingQty\" < 10", [$latestReport['id']])['count'];
        }
        ?>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase mb-2">Low Stock Items</p>
            <p class="text-2xl font-black text-rose-600"><?= number_format($lowStockCount) ?></p>
        </div>
        <div class="bg-[#800000] p-6 rounded-xl shadow-lg shadow-red-900/20">
            <p class="text-xs font-bold text-white/70 uppercase mb-2">Sync Status</p>
            <p class="text-xl font-bold text-white">Latest Data Live</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative flex-1 md:max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" placeholder="Search by SKU or Description..." class="w-full bg-white border border-gray-300 rounded px-3 py-2 pl-10 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
        </div>
        <div class="flex items-center gap-4 text-xs font-bold text-gray-400 uppercase tracking-widest">
            <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-rose-500"></div> Low Stock</span>
            <span class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-emerald-500"></div> Healthy</span>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="px-6 py-3 font-semibold">Description</th>
                        <th class="px-6 py-3 font-semibold text-center">In Stock</th>
                        <th class="px-6 py-3 font-semibold text-right">Selling Price</th>
                        <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 font-semibold text-right">Inventory Value</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 font-semibold text-center">Category</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    $liveItems = $db->fetchAll("SELECT p.*, c.name as category_name FROM \"Product\" p LEFT JOIN \"Category\" c ON p.\"categoryId\" = c.id ORDER BY p.name ASC");
                    if (count($liveItems) > 0):
                        foreach ($liveItems as $item): 
                            $isLow = $item['stockQty'] < 10;
                            $value = $item['stockQty'] * $item['costPrice'];
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors <?= $isLow ? 'bg-rose-50/30' : '' ?>">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
                                    <span class="text-[9px] text-gray-400 font-mono uppercase tracking-tighter"><?= htmlspecialchars($item['sku'] ?? 'NO-SKU') ?></span>
                                    <?php if ($isLow): ?>
                                        <span class="text-[9px] font-black text-rose-600 uppercase tracking-tighter">Needs Reorder</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full font-mono text-xs font-bold <?= $isLow ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                    <?= number_format($item['stockQty']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono">₹<?= number_format($item['sellingPrice'], 2) ?></td>
                            <?php if ($isAdmin): ?>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-gray-900">₹<?= number_format($value, 2) ?></span>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase">
                                    <?= htmlspecialchars($item['category_name'] ?? 'General') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">No live stock data found. Go to Product Master to add items.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
