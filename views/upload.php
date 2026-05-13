<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">Data Synchronization</h1>
            <p class="text-sm text-gray-500 mt-1">MARG ERP 9+ Secure Data Ingestion</p>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
        <form action="index.php?page=upload" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Reporting Month</label>
                    <select name="month" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                        <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?= $i ?>" <?= $i == date('n') ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$i,1)) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Fiscal Year</label>
                    <input type="number" name="year" value="<?= date('Y') ?>" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Select Excel File</label>
                <div class="flex items-center gap-4">
                    <input type="file" name="excel_file" id="file_input" class="w-full bg-gray-50 border border-gray-300 rounded px-3 py-2 text-sm text-gray-600 file:mr-4 file:py-1 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#1a7eb5] file:text-white hover:file:bg-[#156695] transition-all" accept=".xls,.xlsx" required>
                    <button type="submit" class="bg-[#800000] text-white px-6 py-2.5 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors whitespace-nowrap">
                        <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Execute Sync
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Upload History View -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mt-8">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700">Historical Data Uploads</h3>
            <div class="text-xs text-gray-500">All synchronized reports</div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="px-6 py-3 font-semibold">Upload ID</th>
                        <th class="px-6 py-3 font-semibold">Fiscal Period</th>
                        <th class="px-6 py-3 font-semibold">Original Filename</th>
                        <th class="px-6 py-3 font-semibold">Sync Date</th>
                        <th class="px-6 py-3 font-semibold text-center">Status</th>
                        <th class="px-6 py-3 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    $reports = $db->fetchAll("SELECT * FROM \"Report\" ORDER BY date DESC, year DESC, month DESC");
                    if ($reports):
                        foreach ($reports as $report): 
                            $monthName = date('F', mktime(0, 0, 0, $report['month'], 10));
                    ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-gray-500">
                                <?= $report['id'] ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800"><?= $monthName ?> <?= $report['year'] ?></span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <i class="fa-solid fa-file-excel text-emerald-600 mr-2"></i> <?= htmlspecialchars($report['filename']) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs">
                                <?= date('d M Y, h:i A', strtotime($report['date'])) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded">Processed</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-[#1a7eb5] hover:underline text-xs font-semibold">View Data</button>
                            </td>
                        </tr>
                    <?php 
                        endforeach;
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-server text-3xl mb-3"></i>
                                <p class="text-sm">No historical data syncs found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
