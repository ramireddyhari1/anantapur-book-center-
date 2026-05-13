<?php
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? null;

if ($isAdmin) {
    // Admin View: List all staff and their salary status for the current month
    $month = $_GET['month'] ?? date('n');
    $year = $_GET['year'] ?? date('Y');
    
    $staff = $db->fetchAll("SELECT * FROM \"User\" WHERE role != 'admin' ORDER BY username ASC");
    $salaries = $db->fetchAll("SELECT * FROM \"Salary\" WHERE month = ? AND year = ?", [(int)$month, (int)$year]);
    $salaryMap = [];
    foreach ($salaries as $s) {
        $salaryMap[$s['userId']] = $s;
    }
} else {
    // Staff View: Show my salary history
    $mySalaries = $db->fetchAll("SELECT * FROM \"Salary\" WHERE \"userId\" = ? ORDER BY year DESC, month DESC", [$userId]);
}

$monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Salary Management</h1>
            <p class="text-sm text-gray-500 mt-1"><?= $isAdmin ? 'Process and track staff payroll' : 'My payment history and status' ?></p>
        </div>
        <?php if ($isAdmin): ?>
        <div class="flex items-center gap-3 mt-4 md:mt-0">
            <form action="index.php" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="page" value="salaries">
                <select name="month" class="bg-white border border-gray-300 rounded px-3 py-1.5 text-sm outline-none focus:border-[#1a7eb5]">
                    <?php foreach ($monthNames as $i => $name): ?>
                        <option value="<?= $i+1 ?>" <?= $month == $i+1 ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="year" class="bg-white border border-gray-300 rounded px-3 py-1.5 text-sm outline-none focus:border-[#1a7eb5]">
                    <option value="<?= date('Y') ?>"><?= date('Y') ?></option>
                    <option value="<?= date('Y')-1 ?>"><?= date('Y')-1 ?></option>
                </select>
                <button type="submit" class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded border border-gray-200 text-xs font-bold hover:bg-gray-200 transition-colors">GO</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($isAdmin): 
        $totalPaid = array_reduce($salaries, fn($acc, $s) => $acc + ($s['status'] === 'paid' ? $s['amount'] : 0), 0);
        $totalPending = array_reduce($salaries, fn($acc, $s) => $acc + ($s['status'] === 'pending' ? $s['amount'] : 0), 0);
    ?>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Payout (Paid)</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">₹<?= number_format($totalPaid, 2) ?></h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending Liability</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">₹<?= number_format($totalPending, 2) ?></h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
    <!-- Admin View Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                    <th class="px-6 py-3 font-semibold">Staff Name</th>
                    <th class="px-6 py-3 font-semibold">Amount (₹)</th>
                    <th class="px-6 py-3 font-semibold">Status</th>
                    <th class="px-6 py-3 font-semibold">Paid Date</th>
                    <th class="px-6 py-3 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($staff as $s): 
                    $sal = $salaryMap[$s['id']] ?? null;
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($s['username']) ?></td>
                    <td class="px-6 py-4">
                        <form action="index.php?page=salary_update" method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="userId" value="<?= $s['id'] ?>">
                            <input type="hidden" name="month" value="<?= $month ?>">
                            <input type="hidden" name="year" value="<?= $year ?>">
                            <input type="number" name="amount" value="<?= $sal['amount'] ?? '' ?>" placeholder="Set Salary" class="w-24 bg-gray-50 border border-gray-200 rounded px-2 py-1 text-sm outline-none focus:border-[#1a7eb5]">
                            <button type="submit" class="text-[#1a7eb5] hover:text-blue-700"><i class="fa-solid fa-save"></i></button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($sal): ?>
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?= $sal['status'] === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                <?= strtoupper($sal['status']) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-300 italic text-xs">Not Generated</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">
                        <?= $sal && $sal['paidDate'] ? date('d M, Y', strtotime($sal['paidDate'])) : '--' ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($sal && $sal['status'] === 'pending'): ?>
                            <a href="index.php?page=salary_mark_paid&id=<?= $sal['id'] ?>" class="bg-emerald-600 text-white px-3 py-1 rounded text-[10px] font-bold hover:bg-emerald-700 transition-colors">Mark Paid</a>
                        <?php elseif (!$sal): ?>
                             <span class="text-gray-400 text-[10px]">Enter amount to start</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <!-- Staff View Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                    <th class="px-6 py-3 font-semibold">Month / Year</th>
                    <th class="px-6 py-3 font-semibold">Amount (₹)</th>
                    <th class="px-6 py-3 font-semibold">Status</th>
                    <th class="px-6 py-3 font-semibold text-right">Paid Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if ($mySalaries): ?>
                    <?php foreach ($mySalaries as $sal): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-gray-800"><?= $monthNames[$sal['month']-1] ?> <?= $sal['year'] ?></td>
                        <td class="px-6 py-4 font-mono">₹<?= number_format($sal['amount'], 2) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase <?= $sal['status'] === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                <?= strtoupper($sal['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-gray-500 font-medium">
                            <?= $sal['paidDate'] ? date('d M, Y', strtotime($sal['paidDate'])) : '--' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 italic">No salary records found yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
