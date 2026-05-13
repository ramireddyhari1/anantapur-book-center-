<?php
$id = $_GET['id'] ?? null;
$product = null;
if ($id) {
    $product = $db->fetch("SELECT * FROM \"Product\" WHERE id = ?", [$id]);
}

$categories = $db->fetchAll("SELECT * FROM \"Category\" ORDER BY name ASC");
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]"><?= $id ? 'Edit Product' : 'Add New Product' ?></h1>
            <p class="text-sm text-gray-500 mt-1">Enter the details for the book or stationery item</p>
        </div>
        <a href="index.php?page=products" class="text-sm font-semibold text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <form action="index.php?page=product_save" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Info -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2">Basic Information</h3>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Product Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($product['name'] ?? '') ?>" 
                        class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] outline-none transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">SKU</label>
                        <input type="text" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Barcode</label>
                        <input type="text" name="barcode" value="<?= htmlspecialchars($product['barcode'] ?? '') ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Category</label>
                    <div class="flex gap-2">
                        <select name="categoryId" class="flex-1 bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none">
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($product['categoryId'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="document.getElementById('catModal').classList.remove('hidden')" class="bg-gray-100 p-2 rounded border border-gray-300 hover:bg-gray-200">
                            <i class="fa-solid fa-plus text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">HSN Code</label>
                    <input type="text" name="hsn" value="<?= htmlspecialchars($product['hsn'] ?? '') ?>" 
                        class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none">
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2">Pricing & Inventory</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cost Price (₹)</label>
                        <input type="number" step="0.01" name="costPrice" value="<?= $product['costPrice'] ?? '0.00' ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Selling Price (₹)</label>
                        <input type="number" step="0.01" name="sellingPrice" value="<?= $product['sellingPrice'] ?? '0.00' ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">GST Rate (%)</label>
                        <select name="gstRate" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none">
                            <option value="0" <?= ($product['gstRate'] ?? '') == 0 ? 'selected' : '' ?>>0% (Exempt)</option>
                            <option value="5" <?= ($product['gstRate'] ?? '') == 5 ? 'selected' : '' ?>>5%</option>
                            <option value="12" <?= ($product['gstRate'] ?? '') == 12 ? 'selected' : '' ?>>12%</option>
                            <option value="18" <?= ($product['gstRate'] ?? '18') == 18 ? 'selected' : '' ?>>18%</option>
                            <option value="28" <?= ($product['gstRate'] ?? '') == 28 ? 'selected' : '' ?>>28%</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Opening Stock</label>
                        <input type="number" name="stockQty" value="<?= $product['stockQty'] ?? '0' ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Expiry Date (Optional)</label>
                    <input type="date" name="expiryDate" value="<?= $product['expiryDate'] ?? '' ?>" 
                        class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm focus:border-[#1a7eb5] outline-none">
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
            <a href="index.php?page=products" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800">Cancel</a>
            <button type="submit" class="bg-[#800000] text-white px-6 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors">
                <i class="fa-solid fa-save mr-1"></i> Save Product
            </button>
        </div>
    </form>
</div>

<!-- Category Modal -->
<div id="catModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Add New Category</h3>
            <button onclick="document.getElementById('catModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="index.php?page=category_save" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Category Name</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
            </div>
            <button type="submit" class="w-full bg-[#1a7eb5] text-white py-2 rounded font-semibold text-sm hover:bg-blue-700 transition-colors">Create Category</button>
        </form>
    </div>
</div>
