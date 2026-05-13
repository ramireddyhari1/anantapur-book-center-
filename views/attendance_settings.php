<?php
$settings = $db->fetch("SELECT * FROM \"AttendanceSettings\" WHERE id='default'");
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8 pb-4 border-b">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Attendance Settings</h1>
            <p class="text-sm text-gray-500">Configure daily attendance slots for staff members</p>
        </div>
        <a href="index.php?page=staff_attendance" class="text-sm text-gray-600 hover:text-[#1a7eb5] flex items-center gap-1">
            <i class="fa-solid fa-arrow-left"></i> Back to Logs
        </a>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> Settings updated successfully!
        </div>
    <?php endif; ?>

    <form action="index.php?page=update_attendance_settings" method="POST" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Slot 1 -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold">1</div>
                    <h3 class="font-bold text-gray-800">Morning Slot</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label>
                        <input type="time" name="slot1_start" value="<?= $settings['slot1_start'] ?>" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label>
                        <input type="time" name="slot1_end" value="<?= $settings['slot1_end'] ?>" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Slot 2 -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center font-bold">2</div>
                    <h3 class="font-bold text-gray-800">Afternoon Slot</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label>
                        <input type="time" name="slot2_start" value="<?= $settings['slot2_start'] ?>" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label>
                        <input type="time" name="slot2_end" value="<?= $settings['slot2_end'] ?>" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Slot 3 -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center font-bold">3</div>
                    <h3 class="font-bold text-gray-800">Evening Slot</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Time</label>
                        <input type="time" name="slot3_start" value="<?= $settings['slot3_start'] ?>" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Time</label>
                        <input type="time" name="slot3_end" value="<?= $settings['slot3_end'] ?>" class="w-full border rounded-lg px-3 py-2 outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-300 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                <i class="fa-solid fa-circle-info mr-1"></i> These timings will be strictly enforced for staff attendance uploads.
            </p>
            <button type="submit" class="bg-[#1a7eb5] text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-blue-900/20 hover:bg-[#156695] transition-all">
                Save Timing Settings
            </button>
        </div>
    </form>
</div>
