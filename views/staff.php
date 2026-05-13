<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Personnel Registry</h1>
            <p class="text-sm text-gray-500 mt-1">Human resource management and staff overview</p>
        </div>
        <a href="index.php?page=staff_new" class="bg-[#800000] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors mt-4 md:mt-0">
            <i class="fa-solid fa-user-plus mr-1"></i> Enroll Staff
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Staff List -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-sm font-bold text-gray-600 mb-4 border-b border-gray-200 pb-2">Active Staff Members</h3>
            
            <?php
            $workers = $db->fetchAll("SELECT * FROM \"User\" WHERE role != 'admin' ORDER BY username ASC");
            if ($workers):
                foreach ($workers as $worker): ?>
                <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm flex items-center justify-between hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-lg font-bold text-gray-500">
                            <?= strtoupper(substr($worker['username'], 0, 2)) ?>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-800"><?= htmlspecialchars($worker['username']) ?></h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded">Active</span>
                                <span class="text-xs text-gray-500">ID: <?= $worker['id'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="index.php?page=staff_edit&id=<?= $worker['id'] ?>" class="p-2 text-gray-400 hover:text-[#1a7eb5] transition-colors" title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="index.php?page=staff_delete&id=<?= $worker['id'] ?>" onclick="return confirm('Are you sure you want to delete this staff member?')" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach;
            else: ?>
                <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                    <p class="text-gray-500 font-medium">No Staff Records Found</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm text-center">
                <p class="text-xs font-bold uppercase text-gray-500 mb-2">Today's Attendance</p>
                <h2 class="text-4xl font-bold text-[#1a7eb5]">0%</h2>
                <p class="text-sm text-gray-500 mt-2">All positions currently vacant</p>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <h4 class="text-xs font-bold uppercase text-gray-500 border-b border-gray-200 pb-3 mb-4">Security Logs</h4>
                <div class="space-y-4">
                    <div class="flex gap-3 items-start">
                        <div class="mt-1 w-2 h-2 bg-emerald-500 rounded-full"></div>
                        <div>
                            <p class="text-xs font-bold text-emerald-600">System Audit</p>
                            <p class="text-xs text-gray-600 mt-1">Personnel registry initialized successfully.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
