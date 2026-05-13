<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Utility Suite</h1>
            <p class="text-sm text-gray-500 mt-1">Retail Tools & Productivity Engines</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Discount Calculator -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm relative">
            <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Discount Engine</h3>
                    <p class="text-xs text-gray-500 mt-1">Instant Margin Analysis</p>
                </div>
                <i class="fa-solid fa-calculator text-[#1a7eb5] text-2xl"></i>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Market Price (MRP)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">₹</span>
                        <input type="number" id="mrp" placeholder="0.00" oninput="calculate()" class="w-full bg-white border border-gray-300 rounded px-3 py-2 pl-8 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Applied Discount (%)</label>
                    <input type="number" id="discount" value="10" oninput="calculate()" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>

                <div class="bg-gray-50 p-4 rounded border border-gray-200 mt-6">
                    <p class="text-xs font-bold text-gray-500 mb-1">Net Realizable Value</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-sm font-bold text-gray-500">₹</span>
                        <h2 id="finalPrice" class="text-3xl font-bold text-[#1a7eb5]">0</h2>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-3">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase">Retailer Savings</p>
                            <p id="savings" class="text-sm font-bold text-emerald-600">₹0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Tools -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4 cursor-pointer hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 bg-blue-50 rounded flex items-center justify-center text-xl text-[#1a7eb5]">
                    <i class="fa-solid fa-barcode"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800">Label Generator</h4>
                    <p class="text-xs text-gray-500 mt-1">Print custom SKU barcodes</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4 cursor-pointer hover:bg-gray-50 transition-colors">
                <div class="w-12 h-12 bg-blue-50 rounded flex items-center justify-center text-xl text-[#1a7eb5]">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-800">Fast Invoicing</h4>
                    <p class="text-xs text-gray-500 mt-1">Draft instant PDF estimates</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function calculate() {
        const mrp = parseFloat(document.getElementById('mrp').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        
        const savingsValue = (mrp * discount) / 100;
        const finalPriceValue = mrp - savingsValue;
        
        document.getElementById('finalPrice').innerText = Math.round(finalPriceValue).toLocaleString();
        document.getElementById('savings').innerText = '₹' + Math.round(savingsValue).toLocaleString();
    }
</script>
