<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-[#1a7eb5]">New Staff Registration</h1>
            <p class="text-sm text-gray-500 mt-1">Enroll a new personnel member into the ERP system</p>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm max-w-2xl">
        <form action="index.php?page=staff_save" method="POST" class="p-6 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" required class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Contact Number</label>
                    <input type="tel" name="phone" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Email Address</label>
                <input type="email" name="email" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Role Type</label>
                    <select name="role" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                        <option value="worker">Staff / Worker</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Date of Joining</label>
                    <input type="date" name="doj" value="<?= date('Y-m-d') ?>" class="w-full bg-white border border-gray-300 rounded px-3 py-2 text-sm outline-none focus:border-[#1a7eb5] focus:ring-1 focus:ring-[#1a7eb5] transition-all">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="reset" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded shadow-sm text-sm font-semibold hover:bg-gray-50 transition-colors">
                    Reset
                </button>
                <button type="submit" class="bg-[#800000] text-white px-6 py-2 rounded shadow-sm text-sm font-semibold hover:bg-red-900 transition-colors">
                    <i class="fa-solid fa-user-check mr-1"></i> Register Staff
                </button>
            </div>
        </form>
    </div>
</div>
