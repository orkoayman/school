<?= $this->extend('layouts/main') ?>

<?php helper('school'); $school = school_info(); $logoUrl = school_logo_url(); ?>

<?= $this->section('content') ?>

<div class="bg-white rounded-lg shadow p-8 max-w-2xl mx-auto print:shadow-none">
    <div class="text-center mb-6 border-b pb-4">
        <?php if ($logoUrl): ?>
            <img src="<?= esc($logoUrl) ?>" alt="Logo" class="w-14 h-14 mx-auto mb-2 rounded object-cover">
        <?php endif; ?>
        <h2 class="text-xl font-bold"><?= esc($school['name']) ?></h2>
        <p class="text-sm text-gray-500 mt-1"><?= esc($exam['name'] ?? '') ?> — <?= esc($enrollment['year_name'] ?? '') ?></p>
    </div>

    <div class="grid grid-cols-2 gap-3 text-sm mb-6">
        <div><span class="text-gray-500">নাম:</span> <?= esc($enrollment['name'] ?? '') ?></div>
        <div><span class="text-gray-500">স্টুডেন্ট কোড:</span> <?= esc($enrollment['student_code'] ?? '') ?></div>
        <div><span class="text-gray-500">ক্লাস:</span> <?= esc($enrollment['class_name'] ?? '') ?></div>
        <div><span class="text-gray-500">রোল:</span> <?= esc($enrollment['roll_in_class'] ?? '') ?></div>
    </div>

    <table class="min-w-full text-sm mb-6">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-3 py-2">বিষয়</th>
                <th class="px-3 py-2 text-center">পূর্ণ নাম্বার</th>
                <th class="px-3 py-2 text-center">প্রাপ্ত নাম্বার</th>
                <th class="px-3 py-2 text-center">গ্রেড</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($results as $r): ?>
                <tr>
                    <td class="px-3 py-2"><?= esc($r['subject_name']) ?></td>
                    <td class="px-3 py-2 text-center"><?= esc($r['full_marks']) ?></td>
                    <td class="px-3 py-2 text-center"><?= esc($r['marks_obtained'] ?? '-') ?></td>
                    <td class="px-3 py-2 text-center"><?= esc($r['grade'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($summary): ?>
    <div class="border-t pt-4 flex justify-between items-center">
        <div>
            <p class="text-sm"><span class="text-gray-500">মোট নাম্বার:</span> <?= esc($summary['total_marks_obtained']) ?></p>
            <p class="text-sm"><span class="text-gray-500">GPA:</span> <span class="font-semibold"><?= esc($summary['gpa']) ?></span></p>
        </div>
        <div>
            <?php if ($summary['is_failed'] && ! $summary['manual_pass_override']): ?>
                <span class="px-3 py-1 rounded bg-red-100 text-red-700 font-medium">Fail</span>
            <?php elseif ($summary['is_failed'] && $summary['manual_pass_override']): ?>
                <span class="px-3 py-1 rounded bg-yellow-100 text-yellow-700 font-medium">Pass (বিশেষ অনুমোদন)</span>
            <?php else: ?>
                <span class="px-3 py-1 rounded bg-green-100 text-green-700 font-medium">Pass</span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session('role') === 'admin' && $summary['is_failed']): ?>
    <form action="/results/override" method="post" class="mt-4 border-t pt-4 print:hidden">
        <?= csrf_field() ?>
        <input type="hidden" name="enrollment_id" value="<?= esc($enrollment['id']) ?>">
        <input type="hidden" name="exam_id" value="<?= esc($exam['id']) ?>">
        <input type="hidden" name="override" value="<?= $summary['manual_pass_override'] ? '0' : '1' ?>">
        <label class="block text-xs text-gray-500 mb-1">মন্তব্য (ঐচ্ছিক)</label>
        <input type="text" name="note" class="border rounded px-2 py-1 text-sm w-64 mb-2" placeholder="কারণ লিখুন">
        <button type="submit" class="block bg-yellow-600 text-white px-4 py-2 rounded text-sm hover:bg-yellow-700">
            <?= $summary['manual_pass_override'] ? 'Override বাতিল করুন' : 'ম্যানুয়াল Pass দিন' ?>
        </button>
    </form>
    <?php endif; ?>
    <?php endif; ?>

    <div class="mt-6 print:hidden">
        <button onclick="window.print()" class="text-sm text-blue-600 hover:underline">প্রিন্ট করুন</button>
    </div>
</div>

<?= $this->endSection() ?>
