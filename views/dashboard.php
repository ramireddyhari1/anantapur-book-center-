<?php
// Fetch Real Metrics
$latestReport = $db->fetch("SELECT id FROM \"Report\" ORDER BY date DESC LIMIT 1");
$topMover = null;
$monthlyRevenue = 0;

if ($latestReport) {
    $topMover = $db->fetch("SELECT description, \"issueQty\" FROM \"ReportItem\" WHERE \"reportId\" = ? ORDER BY \"issueQty\" DESC LIMIT 1", [$latestReport['id']]);
    $monthlyRevenue = $db->fetch("SELECT SUM(\"issueValue\") as total FROM \"ReportItem\" WHERE \"reportId\" = ?", [$latestReport['id']])['total'] ?? 0;
}

$whatsappText = urlencode("*Anantapur Book Centre ERP - Daily Summary*\n\n" . 
    "📅 Date: " . date('d M, Y') . "\n" .
    "💰 Monthly Revenue: ₹" . number_format($monthlyRevenue, 2) . "\n" .
    "📦 Stock Valuation: ₹" . number_format($stats['total_value'] ?? 0, 2) . "\n" .
    "📈 Top Item: " . ($topMover['description'] ?? 'N/A') . " (" . ($topMover['issueQty'] ?? 0) . " units)\n\n" .
    "Sent from ABC ERP Dashboard");
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Executive Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Enterprise Overview & Key Metrics</p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($isAdmin): ?>
            <a href="https://wa.me/?text=<?= $whatsappText ?>" target="_blank" class="bg-emerald-600 text-white px-4 py-2 rounded shadow-lg shadow-emerald-900/20 text-sm font-semibold hover:bg-emerald-700 transition-all">
                <i class="fa-brands fa-whatsapp mr-1"></i> Share Summary
            </a>
            <?php endif; ?>
            <div class="text-sm text-gray-500 font-semibold bg-white px-4 py-2 border border-gray-200 rounded">
                <i class="fa-regular fa-calendar mr-1"></i> <?= date('F j, Y') ?>
            </div>
        </div>
    </div>

    <!-- Alert / Sync Status -->
    <?php if (isset($_GET['sync']) && $_GET['sync'] === 'success'): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold"><i class="fa-solid fa-circle-check mr-2"></i>Success!</strong>
        <span class="block sm:inline"> ERP Data synchronized successfully. Master ledgers updated.</span>
    </div>
    <?php endif; ?>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php if ($isAdmin): ?>
        <!-- Revenue Card -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Gross Revenue (MTD)</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">₹<?= number_format($monthlyRevenue, 2) ?></h3>
                <p class="text-xs text-emerald-600 mt-2 font-semibold"><i class="fa-solid fa-arrow-up mr-1"></i> Live Tracking</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-[#1a7eb5] rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
        </div>

        <!-- Stock Value Card -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Stock Valuation</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">₹<?= number_format($stats['total_value'] ?? 0, 2) ?></h3>
                <p class="text-xs text-gray-400 mt-2 font-semibold">Asset Value Assessment</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-[#1a7eb5] rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>
        <?php endif; ?>

        <!-- High Buy / Best Seller -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Top Moving SKU</p>
                <h3 class="text-lg font-black text-gray-900 mt-1 truncate max-w-[120px]" title="<?= $topMover['description'] ?? 'N/A' ?>">
                    <?= $topMover['description'] ?? 'N/A' ?>
                </h3>
                <p class="text-xs text-emerald-600 mt-2 font-semibold"><?= number_format($topMover['issueQty'] ?? 0) ?> Units Sold</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-[#1a7eb5] rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">System Integrity</p>
                <h3 class="text-lg font-black text-gray-900 mt-1">ONLINE</h3>
                <p class="text-xs text-emerald-600 mt-2 font-semibold"><i class="fa-solid fa-circle text-[8px] mr-1"></i> All systems nominal</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-server"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Shortcut Area -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-sm font-bold text-gray-700">Quick Actions</h3>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php if ($isAdmin): ?>
                <a href="index.php?page=upload" class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center hover:bg-blue-50 hover:border-blue-200 transition-all group">
                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 group-hover:text-blue-500 mb-2"></i>
                    <p class="text-xs font-bold text-gray-600">Sync Marg</p>
                </a>
                <?php endif; ?>
                <a href="index.php?page=staff_attendance" class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-all group">
                    <i class="fa-solid fa-camera text-2xl text-gray-400 group-hover:text-emerald-500 mb-2"></i>
                    <p class="text-xs font-bold text-gray-600">Attendance</p>
                </a>
                <?php if ($isAdmin): ?>
                <a href="index.php?page=expenses" class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center hover:bg-orange-50 hover:border-orange-200 transition-all group">
                    <i class="fa-solid fa-indian-rupee-sign text-2xl text-gray-400 group-hover:text-orange-500 mb-2"></i>
                    <p class="text-xs font-bold text-gray-600">Add Expense</p>
                </a>
                <a href="index.php?page=analytics" class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-center hover:bg-purple-50 hover:border-purple-200 transition-all group">
                    <i class="fa-solid fa-chart-line text-2xl text-gray-400 group-hover:text-purple-500 mb-2"></i>
                    <p class="text-xs font-bold text-gray-600">Insights</p>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-700 mb-6 border-b border-gray-100 pb-4">Audit & Security</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-2 h-2 mt-1.5 rounded-full bg-emerald-500"></div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Database Sync</p>
                        <p class="text-[11px] text-gray-500">Marg ERP Ledger Parsed.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-2 h-2 mt-1.5 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Staff Portal</p>
                        <p class="text-[11px] text-gray-500">GPS verified logs enabled.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-2 h-2 mt-1.5 rounded-full bg-[#800000]"></div>
                    <div>
                        <p class="text-xs font-bold text-gray-700">Financial Ledger</p>
                        <p class="text-[11px] text-gray-500">New expense categories added.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
