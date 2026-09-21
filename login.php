<?php helper('school'); $school = school_info(); $logoUrl = school_logo_url(); ?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন - <?= esc($school['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Hind Siliguri', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
    <div class="flex flex-col items-center mb-6">
        <?php if ($logoUrl): ?>
            <img src="<?= esc($logoUrl) ?>" alt="Logo" class="w-16 h-16 rounded object-cover mb-3">
        <?php endif; ?>
        <h1 class="text-xl font-semibold text-center"><?= esc($school['name']) ?></h1>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4 text-sm">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('message')): ?>
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded mb-4 text-sm">
            <?= esc(session()->getFlashdata('message')) ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="post">
        <?= csrf_field() ?>

        <label class="block text-sm mb-1">ইমেইল</label>
        <input type="email" name="email" required value="<?= esc(old('email')) ?>"
               class="w-full border rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-slate-500">

        <label class="block text-sm mb-1">পাসওয়ার্ড</label>
        <input type="password" name="password" required
               class="w-full border rounded px-3 py-2 mb-6 focus:outline-none focus:ring-2 focus:ring-slate-500">

        <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded hover:bg-slate-700">
            লগইন
        </button>
    </form>
</div>

</body>
</html>
