<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">মোট সক্রিয় ছাত্র/ছাত্রী</p>
        <p class="text-2xl font-semibold mt-1"><?= esc($totalStudents) ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">চলতি শিক্ষাবর্ষ</p>
        <p class="text-2xl font-semibold mt-1"><?= esc($currentYear['year'] ?? '- নির্ধারিত নেই -') ?></p>
    </div>
    <div class="bg-white rounded-lg shadow p-5">
        <p class="text-sm text-gray-500">এই বছর ভর্তিকৃত</p>
        <p class="text-2xl font-semibold mt-1"><?= esc($enrolledThisYear) ?></p>
    </div>
</div>

<?php if (! $currentYear): ?>
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">
        কোনো শিক্ষাবর্ষ চলতি হিসেবে নির্ধারিত নেই। শুরু করতে <a href="/settings/classes" class="underline font-medium">ক্লাস ও বিষয়</a> পাতায় গিয়ে একটি শিক্ষাবর্ষ তৈরি করুন।
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
