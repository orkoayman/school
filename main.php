<?php helper('school'); $school = school_info(); $logoUrl = school_logo_url(); ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? '') ?> - <?= esc($school['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Bengali', 'Hind Siliguri', sans-serif; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Hind Siliguri', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-gray-800">

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-800 text-white flex-shrink-0">
        <div class="p-4 flex items-center gap-2 border-b border-slate-700">
            <?php if ($logoUrl): ?>
                <img src="<?= esc($logoUrl) ?>" alt="Logo" class="w-8 h-8 rounded object-cover">
            <?php endif; ?>
            <span class="text-lg font-semibold"><?= esc($school['name']) ?></span>
        </div>
        <nav class="p-3 space-y-1 text-sm">
            <a href="/dashboard" class="block px-3 py-2 rounded hover:bg-slate-700">ড্যাশবোর্ড</a>
            <a href="/students" class="block px-3 py-2 rounded hover:bg-slate-700">ছাত্র/ছাত্রী</a>
            <a href="/attendance" class="block px-3 py-2 rounded hover:bg-slate-700">উপস্থিতি</a>
            <a href="/results" class="block px-3 py-2 rounded hover:bg-slate-700">ফলাফল</a>
            <?php if (session('role') === 'admin'): ?>
            <a href="/fees" class="block px-3 py-2 rounded hover:bg-slate-700">ফি ব্যবস্থাপনা</a>
            <a href="/settings/classes" class="block px-3 py-2 rounded hover:bg-slate-700">ক্লাস ও বিষয়</a>
            <a href="/settings/school" class="block px-3 py-2 rounded hover:bg-slate-700">স্কুলের তথ্য ও লোগো</a>
            <a href="/settings/users" class="block px-3 py-2 rounded hover:bg-slate-700">ইউজার ব্যবস্থাপনা</a>
            <?php endif; ?>
            <a href="/logout" class="block px-3 py-2 rounded hover:bg-red-700 mt-4">লগআউট</a>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow px-6 py-3 flex justify-between items-center">
            <h1 class="text-lg font-semibold"><?= esc($title ?? '') ?></h1>
            <span class="text-sm text-gray-500"><?= esc(session('user_name')) ?> (<?= esc(session('role')) ?>)</span>
        </header>

        <main class="p-6 flex-1">
            <?php if (session()->getFlashdata('message')): ?>
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4">
                    <?= esc(session()->getFlashdata('message')) ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

</body>
</html>
