<?php
require_once __DIR__ . '/../src/Database.php';
$db = new App\Database();

// Find the record marked around 05:00 UTC (which is 10:30 IST)
$res = $db->query("DELETE FROM \"Attendance\" WHERE timestamp >= '2026-05-13 05:00:00' AND timestamp <= '2026-05-13 06:00:00'");
echo "Deleted incorrect records.";
