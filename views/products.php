<?php
$products = $db->fetchAll("SELECT p.*, c.name as category_name FROM \"Product\" p LEFT JOIN \"Category\" c ON p.\"categoryId\" = c.id ORDER BY p.name ASC");
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Product Master</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your books, stationery, and inventory items</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <a href="index.php?page=product_new" class="bg-[#1a7eb5] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-blue-700 transition-colors">
                <i class="fa-solid fa-plus mr-1"></i> Add New Product
            </a>
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-file-import mr-1"></i> Bulk Import
            </button>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative flex-1 md:max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="productSearch" placeholder="Search by Name, SKU or Barcode..." class="w-full bg-white border border-gray-300 rounded px-3 py-2 pl-10 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
        </div>
        <div class="flex items-center gap-4">
            <select class="bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
                <option value="">All Categories</option>
                <?php 
                $categories = $db->fetchAll("SELECT * FROM \"Category\" ORDER BY name ASC");
                foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="px-6 py-3 font-semibold">Product Name</th>
                        <th class="px-6 py-3 font-semibold">SKU / Barcode</th>
                        <th class="px-6 py-3 font-semibold">Category</th>
                        <th class="px-6 py-3 font-semibold text-right">Selling Price</th>
                        <th class="px-6 py-3 font-semibold text-center">Stock</th>
                        <th class="px-6 py-3 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $p): 
                            $isLow = $p['stockQty'] < 10;
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="text-[10px] text-gray-400 uppercase"><?= htmlspecialchars($p['hsn'] ?? 'No HSN') ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-mono text-gray-600"><?= htmlspecialchars($p['sku'] ?? '-') ?></div>
                                <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($p['barcode'] ?? '-') ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase">
                                    <?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                ₹<?= number_format($p['sellingPrice'], 2) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full font-mono text-xs font-bold <?= $isLow ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                    <?= number_format($p['stockQty']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="index.php?page=product_new&id=<?= $p['id'] ?>" class="text-blue-600 hover:text-blue-800"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <button class="text-rose-600 hover:text-rose-800"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-20"></i>
                                    <p class="font-medium">No products found</p>
                                    <p class="text-xs">Start by adding a new product to your inventory.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('productSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});
</script>
