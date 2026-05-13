<?php
$products = $db->fetchAll("SELECT * FROM \"Product\" WHERE \"stockQty\" > 0 ORDER BY name ASC");
$customers = $db->fetchAll("SELECT * FROM \"Customer\" ORDER BY name ASC");
?>

<div class="h-[calc(100vh-100px)] flex gap-6">
    <!-- Left Side: Billing Cart -->
    <div class="flex-1 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-[#1a7eb5]"></i> Current Bill
            </h2>
            <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                Invoice #<span id="nextInvoiceNo"><?= time() ?></span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-left text-sm">
                <thead class="sticky top-0 bg-white border-b border-gray-100 text-gray-400 uppercase text-[10px] font-black">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3 text-center">Qty</th>
                        <th class="px-6 py-3 text-right">Price</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-center"></th>
                    </tr>
                </thead>
                <tbody id="cartTable" class="divide-y divide-gray-50">
                    <!-- Cart items will appear here -->
                </tbody>
            </table>
            <div id="emptyCart" class="flex flex-col items-center justify-center py-20 text-gray-300">
                <i class="fa-solid fa-cash-register text-6xl mb-4 opacity-10"></i>
                <p class="font-medium">No items in bill</p>
                <p class="text-[10px] uppercase">Scan barcode or search to add</p>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-200 space-y-4">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span id="subtotal">₹0.00</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>GST Amount</span>
                <span id="taxAmount">₹0.00</span>
            </div>
            <div class="flex justify-between items-end border-t border-gray-200 pt-4">
                <span class="text-lg font-bold text-gray-800">Grand Total</span>
                <span id="grandTotal" class="text-3xl font-black text-[#800000]">₹0.00</span>
            </div>
            <button onclick="checkout()" class="w-full bg-[#800000] text-white py-4 rounded-xl font-black text-lg shadow-lg shadow-red-900/20 hover:bg-red-900 transition-all active:scale-[0.98]">
                COMPLETE CHECKOUT (F8)
            </button>
        </div>
    </div>

    <!-- Right Side: Product Search & Customer -->
    <div class="w-96 flex flex-col gap-6">
        <!-- Customer Selection -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2">Customer Details</h3>
            <div>
                <select id="customerSelect" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
                    <option value="">Cash Customer</option>
                    <?php foreach($customers as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= $c['phone'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button onclick="document.getElementById('custModal').classList.remove('hidden')" class="w-full border border-dashed border-gray-300 py-2 rounded text-xs font-bold text-gray-400 hover:bg-gray-50">
                + ADD NEW CUSTOMER
            </button>
        </div>

        <!-- Product Search -->
        <div class="flex-1 bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col overflow-hidden">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b pb-2 mb-4">Search Products</h3>
            <div class="relative mb-4">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="posSearch" placeholder="Search or scan barcode..." class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 pl-10 text-sm outline-none focus:border-[#1a7eb5] transition-all">
            </div>
            <div class="flex-1 overflow-y-auto space-y-2 pr-2" id="productList">
                <?php foreach($products as $p): ?>
                <div onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)" class="p-3 border border-gray-100 rounded-lg cursor-pointer hover:border-[#1a7eb5] hover:bg-blue-50/50 transition-all group">
                    <div class="font-bold text-gray-800 text-sm group-hover:text-[#1a7eb5]"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-[10px] text-gray-400 uppercase">Stock: <?= $p['stockQty'] ?></span>
                        <span class="text-xs font-bold text-gray-900">₹<?= number_format($p['sellingPrice'], 2) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div id="custModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Add New Customer</h3>
            <button onclick="document.getElementById('custModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="index.php?page=customer_save" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Customer Name *</label>
                <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone Number</label>
                <input type="text" name="phone" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5]">
            </div>
            <button type="submit" class="w-full bg-[#1a7eb5] text-white py-2 rounded font-semibold text-sm hover:bg-blue-700 transition-colors">Save Customer</button>
        </form>
    </div>
</div>

<script>
let cart = [];

function addToCart(product) {
    const existing = cart.find(item => item.id === product.id);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: parseFloat(product.sellingPrice),
            gstRate: parseFloat(product.gstRate),
            qty: 1
        });
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    renderCart();
}

function updateQty(id, qty) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.qty = parseFloat(qty) || 0;
        if (item.qty <= 0) removeFromCart(id);
        else renderCart();
    }
}

function renderCart() {
    const tbody = document.getElementById('cartTable');
    const empty = document.getElementById('emptyCart');
    tbody.innerHTML = '';
    
    if (cart.length === 0) {
        empty.style.display = 'flex';
    } else {
        empty.style.display = 'none';
    }

    let subtotal = 0;
    let taxAmount = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.qty;
        const itemTax = itemTotal * (item.gstRate / 100);
        subtotal += itemTotal;
        taxAmount += itemTax;

        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-800">${item.name}</div>
                    <div class="text-[9px] text-gray-400 uppercase">GST ${item.gstRate}%</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <input type="number" value="${item.qty}" onchange="updateQty('${item.id}', this.value)" class="w-16 bg-gray-100 border-none rounded px-2 py-1 text-center font-mono font-bold text-sm">
                </td>
                <td class="px-6 py-4 text-right font-mono">₹${item.price.toFixed(2)}</td>
                <td class="px-6 py-4 text-right font-bold font-mono">₹${itemTotal.toFixed(2)}</td>
                <td class="px-6 py-4 text-center">
                    <button onclick="removeFromCart('${item.id}')" class="text-rose-400 hover:text-rose-600"><i class="fa-solid fa-xmark"></i></button>
                </td>
            </tr>
        `;
    });

    const grandTotal = subtotal + taxAmount;
    document.getElementById('subtotal').textContent = `₹${subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    document.getElementById('taxAmount').textContent = `₹${taxAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
    document.getElementById('grandTotal').textContent = `₹${grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})}`;
}

function checkout() {
    if (cart.length === 0) {
        alert('Please add items to the bill first');
        return;
    }

    const customerId = document.getElementById('customerSelect').value;
    const data = {
        customerId,
        items: cart,
        totalAmount: cart.reduce((acc, item) => acc + (item.price * item.qty), 0),
        taxAmount: cart.reduce((acc, item) => acc + (item.price * item.qty * (item.gstRate/100)), 0)
    };

    fetch('index.php?page=invoice_create', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            window.open('index.php?page=invoice_print&id=' + res.id, '_blank');
            cart = [];
            renderCart();
            location.reload(); // Refresh to update stock levels
        } else {
            alert('Error: ' + res.message);
        }
    });
}

// Search Logic
document.getElementById('posSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#productList > div');
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? '' : 'none';
    });
});

// Keyboard Shortcuts
window.addEventListener('keydown', (e) => {
    if (e.key === 'F8') {
        e.preventDefault();
        checkout();
    }
});
</script>
