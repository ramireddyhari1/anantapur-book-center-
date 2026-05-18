<?php
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? null;
$user = $db->fetch("SELECT * FROM \"User\" WHERE id = ?", [$userId]);

// Stats
$today = date('Y-m-d');
$totalAttendance = $db->fetch("SELECT COUNT(*) as count FROM \"Attendance\" WHERE \"userId\" = ?", [$userId])['count'] ?? 0;
$todayAttendance = $db->fetch("SELECT COUNT(*) as count FROM \"Attendance\" WHERE \"userId\" = ? AND DATE(timestamp) = ?", [$userId, $today])['count'] ?? 0;
$totalExpenses = $db->fetch("SELECT COUNT(*) as count FROM \"Expense\" WHERE \"userId\" = ?", [$userId])['count'] ?? 0;
$totalTasks = $db->fetch("SELECT COUNT(*) as count FROM \"Task\" WHERE \"userId\" = ?", [$userId])['count'] ?? 0;
$completedTasks = $db->fetch("SELECT COUNT(*) as count FROM \"Task\" WHERE \"userId\" = ? AND status = 'Completed'", [$userId])['count'] ?? 0;

// Recent activity
$recentAttendance = $db->fetchAll("SELECT * FROM \"Attendance\" WHERE \"userId\" = ? ORDER BY timestamp DESC LIMIT 5", [$userId]);
$recentExpenses = $db->fetchAll("SELECT * FROM \"Expense\" WHERE \"userId\" = ? ORDER BY date DESC LIMIT 5", [$userId]);
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-[#1a7eb5] to-[#0d5a87] relative">
            <div class="absolute -bottom-12 left-8">
                <div class="w-24 h-24 rounded-2xl bg-[#800000] text-white flex items-center justify-center text-3xl font-bold border-4 border-white shadow-lg">
                    <?= strtoupper(substr($user['username'], 0, 2)) ?>
                </div>
            </div>
        </div>
        <div class="pt-16 pb-6 px-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($user['username']) ?></h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full <?= $isAdmin ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                            <i class="fa-solid <?= $isAdmin ? 'fa-shield-halved' : 'fa-user' ?>"></i>
                            <?= $isAdmin ? 'Administrator' : 'Staff Member' ?>
                        </span>
                        <span class="text-xs text-gray-400">
                            <i class="fa-solid fa-fingerprint mr-1"></i> ID: <?= substr($user['id'], 0, 10) ?>...
                        </span>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 text-xs font-bold px-4 py-2 rounded-lg border border-emerald-200">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> Online Now
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm text-center">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= $totalAttendance ?></p>
            <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">Total Attendance</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm text-center">
            <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= $todayAttendance ?>/2</p>
            <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">Today's Check-in</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm text-center">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= $totalExpenses ?></p>
            <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">Expenses Filed</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm text-center">
            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <p class="text-2xl font-bold text-gray-800"><?= $completedTasks ?>/<?= $totalTasks ?></p>
            <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">Tasks Done</p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Attendance -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-700"><i class="fa-solid fa-clock-rotate-left mr-2 text-blue-500"></i>Recent Attendance</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if ($recentAttendance): ?>
                    <?php foreach ($recentAttendance as $att): ?>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?= date('d M, Y', strtotime($att['timestamp'])) ?></p>
                            <p class="text-xs text-gray-400">
                                <?= (int)$att['slot'] === 1 ? 'Half Day' : 'Full Day' ?> • <?= date('h:i A', strtotime($att['timestamp'])) ?>
                            </p>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= (int)$att['slot'] === 1 ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' ?>">
                            <?= (int)$att['slot'] === 1 ? 'HALF' : 'FULL' ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">No attendance records yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-bold text-gray-700"><i class="fa-solid fa-credit-card mr-2 text-amber-500"></i>Recent Expenses</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if ($recentExpenses): ?>
                    <?php foreach ($recentExpenses as $exp): ?>
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($exp['description']) ?></p>
                            <p class="text-xs text-gray-400"><?= date('d M, Y', strtotime($exp['date'])) ?></p>
                        </div>
                        <span class="text-sm font-bold text-gray-800">₹<?= number_format($exp['amount'], 2) ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">No expenses filed yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Account Info -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-700"><i class="fa-solid fa-gear mr-2 text-gray-400"></i>Account Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Username</label>
                    <p class="text-sm font-semibold text-gray-800 bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"><?= htmlspecialchars($user['username']) ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Role</label>
                    <p class="text-sm font-semibold text-gray-800 bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"><?= $isAdmin ? 'Administrator' : 'Staff / Worker' ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">User ID</label>
                    <p class="text-sm font-mono text-gray-600 bg-gray-50 px-4 py-3 rounded-lg border border-gray-200"><?= htmlspecialchars($user['id']) ?></p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Session Status</label>
                    <p class="text-sm font-semibold text-emerald-600 bg-gray-50 px-4 py-3 rounded-lg border border-gray-200">
                        <i class="fa-solid fa-circle text-[8px] mr-1 animate-pulse"></i> Active
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
