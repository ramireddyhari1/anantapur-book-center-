<?php
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'];

if ($isAdmin) {
    $tasks = $db->fetchAll("SELECT t.*, u.username FROM \"Task\" t JOIN \"User\" u ON t.\"userId\" = u.id ORDER BY t.created_at DESC");
    $staff = $db->fetchAll("SELECT * FROM \"User\" WHERE role != 'admin'");
} else {
    $tasks = $db->fetchAll("SELECT * FROM \"Task\" WHERE \"userId\" = ? ORDER BY created_at DESC", [$userId]);
}

$pendingCount = count(array_filter($tasks, fn($t) => $t['status'] === 'Pending'));
$completedCount = count(array_filter($tasks, fn($t) => $t['status'] === 'Completed'));
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]"><?= $isAdmin ? 'Staff Task Allocation' : 'My Tasks' ?></h1>
            <p class="text-sm text-gray-500 mt-1"><?= $isAdmin ? 'Assign and monitor daily personnel objectives' : 'View and update your assigned tasks' ?></p>
        </div>
        <?php if ($isAdmin): ?>
        <div class="mt-4 md:mt-0">
            <button onclick="document.getElementById('taskModal').classList.remove('hidden')" class="bg-[#800000] text-white px-4 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors">
                <i class="fa-solid fa-list-check mr-1"></i> Assign New Task
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Task Board -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700">Active Tasks</h3>
            <div class="flex gap-2">
                <span class="text-xs font-semibold px-2 py-1 bg-yellow-100 text-yellow-700 rounded border border-yellow-200">Pending: <?= $pendingCount ?></span>
                <span class="text-xs font-semibold px-2 py-1 bg-emerald-100 text-emerald-700 rounded border border-emerald-200">Completed: <?= $completedCount ?></span>
            </div>
        </div>
        
        <div class="divide-y divide-gray-100">
            <?php if ($tasks): 
                foreach ($tasks as $task): 
                    $priorityColor = match($task['priority']) {
                        'High' => 'bg-rose-100 text-rose-700 border-rose-200',
                        'Medium' => 'bg-blue-100 text-blue-700 border-blue-200',
                        'Low' => 'bg-gray-100 text-gray-700 border-gray-200',
                        default => 'bg-gray-100 text-gray-700'
                    };
                    $statusColor = match($task['status']) {
                        'Completed' => 'bg-emerald-500 text-white',
                        'In Progress' => 'bg-blue-500 text-white',
                        default => 'bg-gray-400 text-white'
                    };
                ?>
                <div class="p-6 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-1">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border <?= $priorityColor ?>"><?= $task['priority'] ?></span>
                                <h4 class="font-bold text-gray-900"><?= htmlspecialchars($task['title']) ?></h4>
                                <?php if ($isAdmin): ?>
                                    <span class="text-xs text-gray-400 font-medium">— Assigned to <span class="text-blue-600 font-bold"><?= htmlspecialchars($task['username']) ?></span></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($task['description']) ?></p>
                            <div class="mt-3 text-[10px] text-gray-400 flex items-center gap-3">
                                <span><i class="fa-solid fa-clock mr-1"></i> <?= date('d M, h:i A', strtotime($task['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            <form action="index.php?page=task_update_status" method="POST">
                                <input type="hidden" name="taskId" value="<?= $task['id'] ?>">
                                <select onchange="this.form.submit()" name="status" class="text-xs font-bold rounded-lg px-3 py-1.5 outline-none border-none <?= $statusColor ?> cursor-pointer shadow-sm">
                                    <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>PENDING</option>
                                    <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>IN PROGRESS</option>
                                    <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>COMPLETED</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach;
            else: ?>
                <div class="p-12 text-center text-gray-400">
                    <i class="fa-solid fa-clipboard-list text-4xl mb-3"></i>
                    <p class="text-sm font-medium">No tasks assigned for today.</p>
                    <p class="text-xs mt-1">Assignments will appear here once created.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($isAdmin): ?>
<!-- Task Modal -->
<div id="taskModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
        <form action="index.php?page=task_add" method="POST" class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Assign New Task</h3>
                <button type="button" onclick="document.getElementById('taskModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Staff Member</label>
                    <select name="userId" required class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500 bg-white">
                        <?php foreach ($staff as $user): ?>
                            <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Task Title</label>
                    <input type="text" name="title" required placeholder="e.g., Inventory Audit" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Priority</label>
                    <div class="flex gap-4">
                        <label class="flex-1">
                            <input type="radio" name="priority" value="Low" class="hidden peer">
                            <div class="text-center py-2 border rounded-lg peer-checked:bg-gray-100 peer-checked:border-gray-500 text-xs font-bold cursor-pointer">LOW</div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="priority" value="Medium" checked class="hidden peer">
                            <div class="text-center py-2 border rounded-lg peer-checked:bg-blue-50 peer-checked:border-blue-500 text-xs font-bold cursor-pointer">MEDIUM</div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="priority" value="High" class="hidden peer">
                            <div class="text-center py-2 border rounded-lg peer-checked:bg-rose-50 peer-checked:border-rose-500 text-xs font-bold cursor-pointer text-rose-600">HIGH</div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Details (Optional)</label>
                    <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500"></textarea>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#1a7eb5] text-white py-4 rounded-xl font-bold mt-8 shadow-lg shadow-blue-900/20 hover:bg-[#156695] transition-all">
                Assign Objective
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
