<?php
$staffId = $_GET['id'] ?? null;
$worker = $db->fetch("SELECT * FROM \"User\" WHERE id = ?", [$staffId]);

if (!$worker) {
    echo "<div class='py-20 text-center'><h1 class='text-2xl font-bold text-gray-500'>Staff record not found.</h1></div>";
    return;
}
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Edit Staff Member</h1>
            <p class="text-sm text-gray-500 mt-1">Update personnel details for <?= htmlspecialchars($worker['username']) ?></p>
        </div>
        <a href="index.php?page=staff" class="text-gray-500 hover:text-gray-700 font-semibold text-sm">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm max-w-2xl">
        <form action="index.php?page=staff_update" method="POST" class="p-6 space-y-6">
            <input type="hidden" name="id" value="<?= $worker['id'] ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" value="<?= htmlspecialchars($worker['username']) ?>" required class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Role Type</label>
                    <select name="role" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                        <option value="worker" <?= $worker['role'] === 'worker' ? 'selected' : '' ?>>Staff / Worker</option>
                        <option value="admin" <?= $worker['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="index.php?page=staff" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded shadow-sm text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-[#1a7eb5] text-white px-6 py-2 rounded shadow-sm text-sm font-semibold hover:bg-[#156695] transition-colors">
                    <i class="fa-solid fa-save mr-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
