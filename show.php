<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-start">
        <div>
            <h2 class="text-xl font-semibold"><?= esc($student['name']) ?></h2>
            <p class="text-sm text-gray-500">স্টুডেন্ট কোড: <?= esc($student['student_code']) ?></p>
        </div>
        <a href="/students/<?= esc($student['id']) ?>/edit" class="text-blue-600 text-sm hover:underline">সম্পাদনা</a>
    </div>

    <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
        <div><span class="text-gray-500">কার্ড নম্বর:</span> <?= esc($student['card_number'] ?? '-') ?></div>
        <div><span class="text-gray-500">অভিভাবকের ফোন:</span> <?= esc($student['parent_phone']) ?></div>
        <div><span class="text-gray-500">অবস্থা:</span> <?= esc($student['status']) ?></div>
        <div><span class="text-gray-500">বাবার নাম:</span> <?= esc($student['father_name'] ?? '-') ?></div>
        <div><span class="text-gray-500">মায়ের নাম:</span> <?= esc($student['mother_name'] ?? '-') ?></div>
        <div><span class="text-gray-500">জন্ম তারিখ:</span> <?= esc($student['date_of_birth'] ?? '-') ?></div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="font-semibold mb-3">একাডেমিক ইতিহাস</h3>
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 text-left">
            <tr>
                <th class="px-4 py-2">শিক্ষাবর্ষ</th>
                <th class="px-4 py-2">ক্লাস</th>
                <th class="px-4 py-2">রোল</th>
                <th class="px-4 py-2">সেকশন</th>
                <th class="px-4 py-2">ফলাফল</th>
                <th class="px-4 py-2">ফি</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($history)): ?>
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">কোনো ভর্তির রেকর্ড নেই।</td></tr>
            <?php endif; ?>
            <?php foreach ($history as $h): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2"><?= esc($h['year_name']) ?></td>
                    <td class="px-4 py-2"><?= esc($h['class_name']) ?></td>
                    <td class="px-4 py-2"><?= esc($h['roll_in_class'] ?? '-') ?></td>
                    <td class="px-4 py-2"><?= esc($h['section'] ?? '-') ?></td>
                    <td class="px-4 py-2">
                        <a href="/results?enrollment_id=<?= esc($h['id']) ?>" class="text-blue-600 hover:underline">দেখুন</a>
                    </td>
                    <td class="px-4 py-2">
                        <a href="/fees/student/<?= esc($student['id']) ?>?year=<?= esc($h['academic_year_id']) ?>" class="text-blue-600 hover:underline">দেখুন</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
