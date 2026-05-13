<?php
$suppliers = $db->fetchAll("SELECT * FROM \"Supplier\" ORDER BY name ASC");
$products = $db->fetchAll("SELECT * FROM \"Product\" ORDER BY name ASC");
?>

<div class="space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Purchase Entry (Stock In)</h1>
            <p class="text-sm text-gray-500 mt-1">Record new stock arrivals from suppliers</p>
        </div>
        <a href="index.php?page=inventory" class="text-sm font-semibold text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Inventory
        </a>
    </div>

    <form action="index.php?page=purchase_save" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Select Supplier *</label>
                <select name="supplierId" required class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
                    <option value="">Choose Supplier</option>
                    <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bill / Invoice No</label>
                <input type="text" name="billNo" placeholder="Vendor Invoice #" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Purchase Date</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
            </div>
        </div>

        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Stock Items</h3>
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black border-b border-gray-100">
                        <th class="pb-2">Product</th>
                        <th class="pb-2 text-center" width="100">Qty</th>
                        <th class="pb-2 text-right" width="120">Purchase Rate (₹)</th>
                        <th class="pb-2 text-right" width="120">Total (₹)</th>
                        <th class="pb-2" width="50"></th>
                    </tr>
                </thead>
                <tbody id="purchaseItems">
                    <!-- Dynamic rows will go here -->
                </tbody>
            </table>
            <button type="button" onclick="addRow()" class="mt-4 text-[#1a7eb5] font-bold text-xs hover:underline">+ ADD ITEM</button>
        </div>

        <div class="bg-gray-50 p-6 border-t border-gray-200 flex justify-between items-center">
            <div class="text-lg font-black text-gray-800">
                Total Amount: <span id="purchaseTotal" class="text-[#1a7eb5]">₹0.00</span>
            </div>
            <button type="submit" class="bg-[#1a7eb5] text-white px-8 py-2 rounded shadow-lg shadow-blue-900/20 font-bold hover:bg-blue-700 transition-all">
                RECORD PURCHASE
            </button>
        </div>
    </form>
</div>

<script>
const products = <?= json_encode($products) ?>;

function addRow() {
    const tbody = document.getElementById('purchaseItems');
    const row = document.createElement('tr');
    row.className = 'border-b border-gray-50';
    row.innerHTML = `
        <td class="py-4">
            <select name="items[][productId]" required onchange="updateRowPrice(this)" class="w-full bg-transparent border-none focus:ring-0 text-sm font-bold text-gray-800">
                <option value="">Select Product</option>
                ${products.map(p => `<option value="${p.id}" data-cost="${p.costPrice}">${p.name}</option>`).join('')}
            </select>
        </td>
        <td class="py-4 text-center">
            <input type="number" name="items[][qty]" required value="1" onchange="calculateTotal()" class="w-20 bg-gray-100 border-none rounded px-2 py-1 text-center font-mono font-bold">
        </td>
        <td class="py-4 text-right">
            <input type="number" step="0.01" name="items[][rate]" required onchange="calculateTotal()" class="w-28 bg-gray-100 border-none rounded px-2 py-1 text-right font-mono font-bold">
        </td>
        <td class="py-4 text-right font-bold text-gray-900 font-mono">₹0.00</td>
        <td class="py-4 text-center">
            <button type="button" onclick="this.closest('tr').remove(); calculateTotal();" class="text-rose-400 hover:text-rose-600"><i class="fa-solid fa-trash"></i></button>
        </td>
    `;
    tbody.appendChild(row);
}

function updateRowPrice(select) {
    const cost = select.options[select.selectedIndex].dataset.cost;
    const row = select.closest('tr');
    row.querySelector('input[name="items[][rate]"]').value = cost;
    calculateTotal();
}

function calculateTotal() {
    let grandTotal = 0;
    document.querySelectorAll('#purchaseItems tr').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name="items[][qty]"]').value) || 0;
        const rate = parseFloat(row.querySelector('input[name="items[][rate]"]').value) || 0;
        const total = qty * rate;
        grandTotal += total;
        row.querySelector('td:nth-child(4)').textContent = `₹${total.toFixed(2)}`;
    });
    document.getElementById('purchaseTotal').textContent = `₹${grandTotal.toFixed(2)}`;
}

// Initial row
addRow();
</script>
