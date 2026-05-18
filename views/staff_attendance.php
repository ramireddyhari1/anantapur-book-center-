<?php
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$userId = $_SESSION['user_id'] ?? null;
$settings = $db->fetch("SELECT * FROM \"AttendanceSettings\" WHERE id='default'");
$today = date('Y-m-d');

// Helper to check if current time is within a slot
function isWithinSlot($start, $end) {
    $now = date('H:i');
    return ($now >= $start && $now <= $end);
}

// Fetch existing logs for today
$logs = $db->fetchAll("SELECT a.*, u.username FROM \"Attendance\" a JOIN \"User\" u ON a.\"userId\" = u.id WHERE DATE(a.timestamp) = ? ORDER BY a.timestamp DESC", [$today]);
$myLogs = array_filter($logs, fn($l) => $l['userId'] === $userId);

// Slot definitions
$slots = [
    1 => ['name' => 'Half Day', 'icon' => 'fa-cloud-sun', 'iconBg' => 'bg-amber-100', 'iconColor' => 'text-amber-600', 'desc' => 'Morning shift only', 'start' => $settings['slot1_start'], 'end' => $settings['slot1_end']],
    2 => ['name' => 'Full Day', 'icon' => 'fa-sun', 'iconBg' => 'bg-blue-100', 'iconColor' => 'text-blue-600', 'desc' => 'Entire working day', 'start' => $settings['slot2_start'], 'end' => $settings['slot2_end']],
];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Attendance Portal</h1>
            <p class="text-sm text-gray-500 mt-1">Photo & GPS verified daily reporting</p>
        </div>
        <div class="flex items-center gap-3">
            <?php if ($isAdmin): ?>
                <a href="index.php?page=attendance_settings" class="bg-gray-100 text-gray-600 px-4 py-2 rounded border border-gray-200 text-sm font-semibold hover:bg-gray-200 transition-colors">
                    <i class="fa-solid fa-clock mr-1"></i> Set Timings
                </a>
            <?php endif; ?>
            <div class="text-sm text-gray-500 font-semibold bg-white px-4 py-2 border border-gray-200 rounded">
                <i class="fa-regular fa-calendar mr-1"></i> <?= date('F j, Y') ?>
            </div>
        </div>
    </div>

    <!-- Attendance Slot Cards (Half Day / Full Day) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($slots as $slotNum => $slot):
            $isActive = isWithinSlot($slot['start'], $slot['end']);
            $hasLog = !empty(array_filter($myLogs, fn($l) => (int)$l['slot'] === $slotNum));
        ?>
        <div class="bg-white rounded-xl border <?= $hasLog ? 'border-emerald-200 bg-emerald-50/30' : ($isActive ? 'border-blue-200 shadow-lg shadow-blue-900/5' : 'border-gray-200 opacity-60') ?> overflow-hidden transition-all">
            <div class="p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 <?= $slot['iconBg'] ?> <?= $slot['iconColor'] ?> rounded-lg flex items-center justify-center">
                            <i class="fa-solid <?= $slot['icon'] ?> text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-800"><?= $slot['name'] ?></h3>
                            <p class="text-[11px] text-gray-400"><?= $slot['desc'] ?></p>
                        </div>
                    </div>
                    <?php if ($hasLog): ?>
                        <span class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold">COMPLETED</span>
                    <?php elseif ($isActive): ?>
                        <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold animate-pulse">ACTIVE NOW</span>
                    <?php endif; ?>
                </div>
                <p class="text-xs text-gray-500 mb-6"><?= date('h:i A', strtotime($slot['start'])) ?> - <?= date('h:i A', strtotime($slot['end'])) ?></p>

                <?php if ($hasLog): ?>
                    <div class="flex items-center gap-2 text-emerald-600 text-sm font-semibold">
                        <i class="fa-solid fa-circle-check"></i> Marked at <?= date('h:i A', strtotime(array_values(array_filter($myLogs, fn($l) => (int)$l['slot'] === $slotNum))[0]['timestamp'])) ?>
                    </div>
                <?php elseif ($isActive): ?>
                    <button onclick="openAttendanceModal(<?= $slotNum ?>)" class="w-full bg-[#1a7eb5] text-white py-3 rounded-lg font-bold text-sm hover:bg-[#156695] transition-colors shadow-lg shadow-blue-900/20">
                        Mark Attendance
                    </button>
                <?php else: ?>
                    <button disabled class="w-full bg-gray-100 text-gray-400 py-3 rounded-lg font-bold text-sm cursor-not-allowed">
                        Outside Slot Time
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    </div>

    <!-- Attendance Logs (Admin View or Staff History) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="font-bold text-gray-800"><?= $isAdmin ? 'All Staff Logs' : 'My Recent Activity' ?></h2>
            <span class="text-xs text-gray-500">Showing records for today</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="px-6 py-3 font-semibold">Staff</th>
                        <th class="px-6 py-3 font-semibold text-center">Type</th>
                        <th class="px-6 py-3 font-semibold">Time</th>
                        <th class="px-6 py-3 font-semibold text-center">Photo</th>
                        <th class="px-6 py-3 font-semibold text-center">Location</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php 
                    $displayLogs = $isAdmin ? $logs : $myLogs;
                    if ($displayLogs):
                        foreach ($displayLogs as $log): ?>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold">
                                        <?= strtoupper(substr($log['username'], 0, 2)) ?>
                                    </div>
                                    <span class="font-bold text-gray-800"><?= htmlspecialchars($log['username']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ((int)$log['slot'] === 1): ?>
                                    <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded text-[10px] font-bold">HALF DAY</span>
                                <?php else: ?>
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-[10px] font-bold">FULL DAY</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-medium">
                                <?= date('h:i A', strtotime($log['timestamp'])) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="viewPhoto('/uploads/attendance/<?= $log['photo'] ?>')" class="text-blue-500 hover:text-blue-700">
                                    <i class="fa-solid fa-image text-lg"></i>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($log['lat']): ?>
                                <a href="https://www.google.com/maps?q=<?= $log['lat'] ?>,<?= $log['lng'] ?>" target="_blank" class="text-emerald-500 hover:text-emerald-700">
                                    <i class="fa-solid fa-location-dot text-lg"></i>
                                </a>
                                <?php else: ?>
                                <span class="text-gray-300"><i class="fa-solid fa-location-slash text-lg"></i></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium italic">No attendance records found for today.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Attendance Submission Modal -->
<div id="attendanceModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[200] hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl scale-95 transition-all duration-300">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Mark Attendance</h3>
                <button onclick="closeAttendanceModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <!-- Attendance Type Indicator -->
            <div id="modal-type-badge" class="mb-4 text-center">
                <span id="modal-type-label" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold"></span>
            </div>

            <!-- Camera Preview -->
            <div class="relative aspect-video bg-gray-900 rounded-xl overflow-hidden mb-6 group">
                <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                <canvas id="canvas" class="hidden"></canvas>
                <div id="camera-loading" class="absolute inset-0 flex items-center justify-center text-white text-sm bg-black/50">
                    <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Initializing camera...
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg text-blue-700">
                    <i class="fa-solid fa-location-crosshairs"></i>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider opacity-60">Location Status</p>
                        <p id="location-status" class="text-sm font-bold">Acquiring GPS coordinates...</p>
                    </div>
                </div>

                <form id="attendanceForm" action="index.php?page=attendance_submit" method="POST">
                    <input type="hidden" name="slot" id="modal-slot">
                    <input type="hidden" name="lat" id="modal-lat">
                    <input type="hidden" name="lng" id="modal-lng">
                    <input type="hidden" name="photo" id="modal-photo">
                    
                    <button type="button" id="captureBtn" onclick="captureAndSubmit()" disabled class="w-full bg-[#1a7eb5] text-white py-4 rounded-xl font-bold shadow-lg shadow-blue-900/20 hover:bg-[#156695] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-camera mr-2"></i> Capture & Submit
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Photo Viewer Modal -->
<div id="photoModal" class="fixed inset-0 bg-black/90 z-[300] hidden items-center justify-center p-4" onclick="this.classList.add('hidden')">
    <img id="modalImg" class="max-w-full max-h-full rounded shadow-2xl" src="" alt="Attendance Photo">
</div>

<script>
let stream = null;
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureBtn = document.getElementById('captureBtn');
const locationStatus = document.getElementById('location-status');

const slotLabels = {
    1: { name: 'Half Day', bg: 'bg-amber-100', color: 'text-amber-700' },
    2: { name: 'Full Day', bg: 'bg-blue-100', color: 'text-blue-700' }
};

async function openAttendanceModal(slot) {
    document.getElementById('modal-slot').value = slot;
    const modal = document.getElementById('attendanceModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Update type badge
    const label = slotLabels[slot];
    const badge = document.getElementById('modal-type-label');
    badge.className = `inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold ${label.bg} ${label.color}`;
    badge.innerHTML = `<i class="fa-solid ${slot === 1 ? 'fa-cloud-sun' : 'fa-sun'}"></i> ${label.name}`;
    
    // Start Camera
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        video.srcObject = stream;
        document.getElementById('camera-loading').classList.add('hidden');
        checkSubmitReady();
    } catch (err) {
        alert("Camera access denied or not available. Please enable camera permissions.");
        closeAttendanceModal();
    }

    // Start GPS
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            document.getElementById('modal-lat').value = position.coords.latitude;
            document.getElementById('modal-lng').value = position.coords.longitude;
            locationStatus.innerText = "GPS Verified (" + position.coords.latitude.toFixed(4) + ", " + position.coords.longitude.toFixed(4) + ")";
            locationStatus.parentElement.parentElement.classList.replace('bg-blue-50', 'bg-emerald-50');
            locationStatus.parentElement.parentElement.classList.replace('text-blue-700', 'text-emerald-700');
            checkSubmitReady();
        }, err => {
            locationStatus.innerText = "GPS Error: " + err.message;
            locationStatus.parentElement.parentElement.classList.replace('bg-blue-50', 'bg-red-50');
            locationStatus.parentElement.parentElement.classList.replace('text-blue-700', 'text-red-700');
        });
    } else {
        locationStatus.innerText = "GPS not supported by this browser.";
    }
}

function checkSubmitReady() {
    if (stream && document.getElementById('modal-lat').value) {
        captureBtn.disabled = false;
    }
}

function captureAndSubmit() {
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    const photoData = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('modal-photo').value = photoData;
    
    // Show loading state
    captureBtn.disabled = true;
    captureBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Submitting...';
    
    document.getElementById('attendanceForm').submit();
}

function closeAttendanceModal() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    const modal = document.getElementById('attendanceModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function viewPhoto(url) {
    const modal = document.getElementById('photoModal');
    document.getElementById('modalImg').src = url;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
</script>
