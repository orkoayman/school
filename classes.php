<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="grid grid-cols-2 gap-6 mb-6">
    <!-- Academic Years -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-3">শিক্ষাবর্ষ</h3>
        <table class="min-w-full text-sm mb-4">
            <tbody class="divide-y">
                <?php foreach ($years as $y): ?>
                    <tr>
                        <td class="px-2 py-2 font-medium"><?= esc($y['year']) ?></td>
                        <td class="px-2 py-2">
                            <?php if ($y['is_current']): ?>
                                <span class="text-green-600 text-xs font-semibold">চলতি বছর</span>
                            <?php else: ?>
                                <form action="/settings/years/<?= esc($y['id']) ?>/activate" method="post">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-blue-600 text-xs hover:underline">চলতি হিসেবে সেট করুন</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form action="/settings/years" method="post" class="flex gap-2">
            <?= csrf_field() ?>
            <input type="text" name="year" placeholder="যেমন: 2027" required class="border rounded px-3 py-2 text-sm w-32">
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded text-sm hover:bg-slate-700">নতুন বছর যোগ করুন</button>
        </form>
        <p class="text-xs text-gray-400 mt-2">নতুন বছর তৈরি করলে স্বয়ংক্রিয়ভাবে ৩টি পরীক্ষা (১ম সাময়িক, ২য় সাময়িক, বার্ষিক) তৈরি হয়ে যাবে।</p>
    </div>

    <!-- Classes -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-3">ক্লাস তালিকা</h3>
        <table class="min-w-full text-sm mb-4">
            <tbody class="divide-y">
                <?php foreach ($classes as $c): ?>
                    <tr>
                        <td class="px-2 py-2"><?= esc($c['numeric_order']) ?></td>
                        <td class="px-2 py-2 font-medium"><?= esc($c['name']) ?></td>
                        <td class="px-2 py-2">
                            <a href="/settings/classes?class_id=<?= esc($c['id']) ?>" class="text-blue-600 text-xs hover:underline">বিষয় দেখুন</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form action="/settings/classes" method="post" class="flex gap-2">
            <?= csrf_field() ?>
            <input type="number" name="numeric_order" placeholder="ক্রম" required class="border rounded px-3 py-2 text-sm w-20">
            <input type="text" name="name" placeholder="ক্লাসের নাম (যেমন: ক্লাস ১)" required class="border rounded px-3 py-2 text-sm flex-1">
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded text-sm hover:bg-slate-700">যোগ করুন</button>
        </form>
    </div>
</div>

<!-- Subjects for selected class -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="font-semibold mb-3">বিষয় তালিকা</h3>

    <form method="get" class="mb-4">
        <select name="class_id" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
            <option value="">-- ক্লাস নির্বাচন করুন --</option>
            <?php foreach ($classes as $c): ?>
                <option value="<?= esc($c['id']) ?>" <?= (string) $selectedClassId === (string) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($selectedClassId): ?>
        <table class="min-w-full text-sm mb-4">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-3 py-2">বিষয়</th>
                    <th class="px-3 py-2">পূর্ণ নাম্বার</th>
                    <th class="px-3 py-2">পাশ নাম্বার</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($subjects)): ?>
                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400">এখনো কোনো বিষয় যোগ করা হয়নি।</td></tr>
                <?php endif; ?>
                <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td class="px-3 py-2"><?= esc($s['name']) ?></td>
                        <td class="px-3 py-2"><?= esc($s['full_marks']) ?></td>
                        <td class="px-3 py-2"><?= esc($s['pass_marks']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form action="/settings/subjects" method="post" class="flex gap-2 items-end flex-wrap">
            <?= csrf_field() ?>
            <input type="hidden" name="class_id" value="<?= esc($selectedClassId) ?>">
            <div>
                <label class="block text-xs mb-1">বিষয়ের নাম</label>
                <input type="text" name="name" required class="border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs mb-1">পূর্ণ নাম্বার</label>
                <input type="number" name="full_marks" value="100" required class="border rounded px-3 py-2 text-sm w-24">
            </div>
            <div>
                <label class="block text-xs mb-1">পাশ নাম্বার</label>
                <input type="number" name="pass_marks" value="33" required class="border rounded px-3 py-2 text-sm w-24">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded text-sm hover:bg-slate-700">বিষয় যোগ করুন</button>
        </form>
    <?php else: ?>
        <p class="text-gray-400 text-sm">বিষয় দেখতে/যোগ করতে উপর থেকে একটি ক্লাস নির্বাচন করুন।</p>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
