<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex justify-between items-center mb-4">
    <form method="get" class="flex gap-2">
        <input type="text" name="q" value="<?= esc($search) ?>" placeholder="নাম / কোড / কার্ড নম্বর / ফোন দিয়ে খুঁজুন"
               class="border rounded px-3 py-2 text-sm w-72">
        <select name="class_id" class="border rounded px-3 py-2 text-sm">
            <option value="">সব ক্লাস</option>
            <?php foreach ($classes as $c): ?>
                <option value="<?= esc($c['id']) ?>" <?= (string) $selectedClassId === (string) $c['id'] ? 'selected' : '' ?>>
                    <?= esc($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-slate-700 text-white px-4 py-2 rounded text-sm">খুঁজুন</button>
    </form>
    <a href="/students/create" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
        + নতুন ছাত্র/ছাত্রী
    </a>
</div>

<?php if (! $currentYear && empty($search)): ?>
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4">
        চলতি শিক্ষাবর্ষ নির্ধারিত নেই, তাই তালিকা দেখানো যাচ্ছে না। <a href="/settings/classes" class="underline">এখানে সেট করুন</a>।
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 text-left">
            <tr>
                <th class="px-4 py-3">নাম</th>
                <th class="px-4 py-3">স্টুডেন্ট কোড</th>
                <th class="px-4 py-3">ক্লাস</th>
                <th class="px-4 py-3">রোল</th>
                <th class="px-4 py-3">কার্ড নম্বর</th>
                <th class="px-4 py-3">অভিভাবকের ফোন</th>
                <th class="px-4 py-3">কার্যক্রম</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($students)): ?>
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">কোনো ছাত্র/ছাত্রী পাওয়া যায়নি।</td></tr>
            <?php endif; ?>
            <?php foreach ($students as $s): ?>
                <?php $sid = $s['student_id'] ?? $s['id']; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-medium"><a href="/students/<?= esc($sid) ?>" class="text-slate-800 hover:underline"><?= esc($s['name']) ?></a></td>
                    <td class="px-4 py-2"><?= esc($s['student_code']) ?></td>
                    <td class="px-4 py-2"><?= esc($s['class_name'] ?? '-') ?></td>
                    <td class="px-4 py-2"><?= esc($s['roll_in_class'] ?? '-') ?></td>
                    <td class="px-4 py-2"><?= esc($s['card_number'] ?? '-') ?></td>
                    <td class="px-4 py-2"><?= esc($s['parent_phone']) ?></td>
                    <td class="px-4 py-2">
                        <a href="/students/<?= esc($sid) ?>/edit" class="text-blue-600 hover:underline">সম্পাদনা</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
