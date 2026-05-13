<?php
// Monthly GST Summary
$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');

$invoices = $db->fetchAll("
    SELECT * FROM \"Invoice\" 
    WHERE EXTRACT(MONTH FROM date) = ? AND EXTRACT(YEAR FROM date) = ?
", [$month, $year]);

$totalTaxable = 0;
$totalGST = 0;
$b2bInvoices = [];
$b2cInvoices = [];

foreach ($invoices as $inv) {
    if ($inv['customerId']) {
        $customer = $db->fetch("SELECT gstin FROM \"Customer\" WHERE id = ?", [$inv['customerId']]);
        if ($customer && $customer['gstin']) {
            $b2bInvoices[] = $inv;
        } else {
            $b2cInvoices[] = $inv;
        }
    } else {
        $b2cInvoices[] = $inv;
    }
    
    $totalGST += $inv['taxAmount'];
    $totalTaxable += ($inv['totalAmount'] - $inv['taxAmount']);
}
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">GST Returns & Compliance</h1>
            <p class="text-sm text-gray-500 mt-1">GSTR-1 and GSTR-3B monthly reporting summary</p>
        </div>
        <div class="flex gap-3 mt-4 md:mt-0">
            <form class="flex gap-2">
                <input type="hidden" name="page" value="gst_reports">
                <select name="month" class="bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none">
                    <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?= $i ?>" <?= $month == $i ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="bg-[#1a7eb5] text-white px-4 py-2 rounded text-sm font-bold hover:bg-blue-700">Filter</button>
            </form>
            <button class="bg-[#800000] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors">
                <i class="fa-solid fa-file-excel mr-1"></i> Export JSON for Portal
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Taxable Value</p>
            <p class="text-2xl font-black text-gray-900">₹<?= number_format($totalTaxable, 2) ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total GST Output</p>
            <p class="text-2xl font-black text-[#1a7eb5]">₹<?= number_format($totalGST, 2) ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">B2B Invoices</p>
            <p class="text-2xl font-black text-gray-900"><?= count($b2bInvoices) ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-emerald-500">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">Compliance Status</p>
            <p class="text-lg font-bold text-emerald-700">Ready to File</p>
        </div>
    </div>

    <!-- GSTR-1 Breakdown -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-widest">GSTR-1 Section Summary</h3>
            <span class="text-[10px] bg-blue-100 text-[#1a7eb5] px-2 py-1 rounded font-black uppercase">Month: <?= date('F', mktime(0, 0, 0, $month, 1)) ?></span>
        </div>
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-gray-400 uppercase text-[10px] font-black border-b border-gray-100">
                    <th class="px-6 py-3">Section</th>
                    <th class="px-6 py-3 text-center">Count</th>
                    <th class="px-6 py-3 text-right">Taxable Value</th>
                    <th class="px-6 py-3 text-right">Tax Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <tr>
                    <td class="px-6 py-4 font-bold text-gray-700">4A, 4B, 4C, 6B, 6C - B2B Invoices</td>
                    <td class="px-6 py-4 text-center"><?= count($b2bInvoices) ?></td>
                    <td class="px-6 py-4 text-right">₹<?= number_format(array_reduce($b2bInvoices, fn($acc, $i) => $acc + ($i['totalAmount'] - $i['taxAmount']), 0), 2) ?></td>
                    <td class="px-6 py-4 text-right">₹<?= number_format(array_reduce($b2bInvoices, fn($acc, $i) => $acc + $i['taxAmount'], 0), 2) ?></td>
                </tr>
                <tr>
                    <td class="px-6 py-4 font-bold text-gray-700">7 - B2C (Small) Invoices</td>
                    <td class="px-6 py-4 text-center"><?= count($b2cInvoices) ?></td>
                    <td class="px-6 py-4 text-right">₹<?= number_format(array_reduce($b2cInvoices, fn($acc, $i) => $acc + ($i['totalAmount'] - $i['taxAmount']), 0), 2) ?></td>
                    <td class="px-6 py-4 text-right">₹<?= number_format(array_reduce($b2cInvoices, fn($acc, $i) => $acc + $i['taxAmount'], 0), 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
