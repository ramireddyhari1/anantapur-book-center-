<?php
// Fetch Monthly Revenue Trajectory from Invoices
$revenueData = $db->fetchAll("
    SELECT 
        EXTRACT(MONTH FROM date) as month, 
        EXTRACT(YEAR FROM date) as year, 
        SUM(\"totalAmount\") as total 
    FROM \"Invoice\" 
    GROUP BY year, month 
    ORDER BY year DESC, month DESC 
    LIMIT 6
");
$revenueData = array_reverse($revenueData); // chronological order

// Fetch Expense Category Mix
$expenseMix = $db->fetchAll("
    SELECT category, SUM(amount) as total 
    FROM \"Expense\" 
    GROUP BY category
");

// P&L Calculations
$totalRevenue = $db->fetch("SELECT SUM(\"totalAmount\") as total FROM \"Invoice\"")['total'] ?? 0;
$totalExpenses = $db->fetch("SELECT SUM(amount) as total FROM \"Expense\"")['total'] ?? 0;

// Calculate COGS (Cost of Goods Sold)
$cogsData = $db->fetch("
    SELECT SUM(ii.qty * p.\"costPrice\") as total 
    FROM \"InvoiceItem\" ii 
    JOIN \"Product\" p ON ii.\"productId\" = p.id
")['total'] ?? 0;

$grossProfit = $totalRevenue - $cogsData;
$netProfit = $grossProfit - $totalExpenses;

// Map months to names
$monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
$revLabels = [];
$revValues = [];
foreach ($revenueData as $rd) {
    $revLabels[] = $monthNames[(int)$rd['month'] - 1] . ' ' . (int)$rd['year'];
    $revValues[] = $rd['total'];
}

$mixLabels = [];
$mixValues = [];
foreach ($expenseMix as $em) {
    $mixLabels[] = $em['category'];
    $mixValues[] = $em['total'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Data Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">Intelligence and Market Velocity Hub</p>
        </div>
    </div>

    <!-- Charts Hub -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h3 class="text-sm font-bold text-gray-600">Inventory Turnover (Issues)</h3>
                <i class="fa-solid fa-chart-line text-[#1a7eb5]"></i>
            </div>
            <div class="h-[300px] w-full">
                <?php if (empty($revValues)): ?>
                    <div class="h-full flex items-center justify-center text-gray-400 italic text-sm">Awaiting Sync Data...</div>
                <?php else: ?>
                    <canvas id="revenueChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <h3 class="text-sm font-bold text-gray-600">Expense Distribution</h3>
                <i class="fa-solid fa-chart-pie text-[#1a7eb5]"></i>
            </div>
            <div class="h-[300px] w-full flex items-center justify-center">
                <?php if (empty($mixValues)): ?>
                    <div class="h-full flex items-center justify-center text-gray-400 italic text-sm">No Expense Data...</div>
                <?php else: ?>
                    <canvas id="categoryMixChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- P&L Summary -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-widest">Profit & Loss Summary (Lifetime)</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="p-6">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Total Revenue</p>
                <p class="text-2xl font-black text-emerald-600">₹<?= number_format($totalRevenue, 2) ?></p>
            </div>
            <div class="p-6">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Cost of Goods (COGS)</p>
                <p class="text-2xl font-black text-rose-600">₹<?= number_format($cogsData, 2) ?></p>
            </div>
            <div class="p-6">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Gross Profit</p>
                <p class="text-2xl font-black text-[#1a7eb5]">₹<?= number_format($grossProfit, 2) ?></p>
            </div>
            <div class="p-6 bg-[#1a7eb5]/5">
                <p class="text-[10px] font-black text-[#1a7eb5] uppercase mb-1">Net Profit</p>
                <p class="text-2xl font-black <?= $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' ?>">₹<?= number_format($netProfit, 2) ?></p>
            </div>
        </div>
    </div>

    <!-- Key Insights -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-xs font-bold uppercase text-gray-500 mb-2">Total Operating Expenses</p>
            <h4 class="text-2xl font-bold text-rose-500">₹<?= number_format($totalExpenses, 2) ?></h4>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-xs font-bold uppercase text-gray-500 mb-2">Profit Margin</p>
            <h4 class="text-2xl font-bold text-gray-800"><?= $totalRevenue > 0 ? number_format(($netProfit / $totalRevenue) * 100, 1) : '0' ?>%</h4>
        </div>
        <div class="bg-[#800000] p-6 rounded-lg shadow-lg shadow-red-900/20 text-white">
            <p class="text-xs font-bold uppercase text-red-200 mb-2">System Status</p>
            <h4 class="text-2xl font-bold flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                OPERATIONAL
            </h4>
        </div>
    </div>
</div>

<script>
    // Revenue Trajectory
    const revCtx = document.getElementById('revenueChart');
    if (revCtx) {
        new Chart(revCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode($revLabels) ?>,
                datasets: [{
                    label: 'Issues Value',
                    data: <?= json_encode($revValues) ?>,
                    borderColor: '#1a7eb5',
                    backgroundColor: 'rgba(26, 126, 181, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1a7eb5',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        grid: { color: '#f3f4f6' }, 
                        border: { display: false },
                        ticks: { callback: value => '₹' + value.toLocaleString() }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Category Mix
    const catCtx = document.getElementById('categoryMixChart');
    if (catCtx) {
        new Chart(catCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($mixLabels) ?>,
                datasets: [{
                    data: <?= json_encode($mixValues) ?>,
                    backgroundColor: ['#1a7eb5', '#3399cc', '#66b2ff', '#94a3b8', '#cbd5e1'],
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: { 
                    legend: { 
                        position: 'right', 
                        labels: { boxWidth: 12, font: { size: 12, weight: 'bold' }, padding: 20 } 
                    } 
                }
            }
        });
    }
</script>
