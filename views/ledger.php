<?php
$entityId = $_GET['entityId'] ?? null;
$entityType = $_GET['type'] ?? 'customer';

if (!$entityId) exit('No entity ID provided');

$entity = ($entityType === 'customer') 
    ? $db->fetch("SELECT * FROM \"Customer\" WHERE id = ?", [$entityId])
    : $db->fetch("SELECT * FROM \"Supplier\" WHERE id = ?", [$entityId]);

if (!$entity) exit('Entity not found');

$entries = $db->fetchAll("SELECT * FROM \"AccountLedger\" WHERE \"entityId\" = ? AND \"entityType\" = ? ORDER BY date DESC", [$entityId, $entityType]);
?>

<div class="space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($entity['name']) ?> - Ledger</h1>
            <p class="text-sm text-gray-500 mt-1">Transaction history for <?= $entityType ?></p>
        </div>
        <div class="flex gap-3">
            <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-gray-50">
                <i class="fa-solid fa-print mr-1"></i> Print Ledger
            </button>
            <a href="index.php?page=<?= $entityType ?>s" class="text-sm font-semibold text-gray-500 hover:text-gray-700 pt-2">
                <i class="fa-solid fa-arrow-left mr-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Current Balance</p>
            <p class="text-2xl font-black <?= $entity['balance'] >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">₹<?= number_format($entity['balance'], 2) ?></p>
        </div>
        <!-- More stats can be added here -->
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="px-6 py-3 font-semibold">Date</th>
                        <th class="px-6 py-3 font-semibold">Description</th>
                        <th class="px-6 py-3 font-semibold text-right">Debit (In)</th>
                        <th class="px-6 py-3 font-semibold text-right">Credit (Out)</th>
                        <th class="px-6 py-3 font-semibold text-right">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (count($entries) > 0): ?>
                        <?php foreach ($entries as $e): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-xs"><?= date('d-M-Y H:i', strtotime($e['date'])) ?></td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-700"><?= htmlspecialchars($e['description']) ?></div>
                                <div class="text-[10px] text-gray-400 uppercase">Ref: <?= $e['referenceId'] ?? '-' ?></div>
                            </td>
                            <td class="px-6 py-4 text-right text-emerald-600 font-mono">
                                <?= $e['debit'] > 0 ? '₹' . number_format($e['debit'], 2) : '-' ?>
                            </td>
                            <td class="px-6 py-4 text-right text-rose-600 font-mono">
                                <?= $e['credit'] > 0 ? '₹' . number_format($e['credit'], 2) : '-' ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold font-mono">₹<?= number_format($e['balance'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No transactions recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
