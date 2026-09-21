<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="font-semibold mb-3">নতুন ফি টাইপ যোগ করুন</h3>
    <form action="/fees/fee-types" method="post" class="flex gap-2 items-end">
        <?= csrf_field() ?>
        <div>
            <label class="block text-xs mb-1">নাম</label>
            <input type="text" name="name" required placeholder="যেমন: ভর্তি ফি" class="border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs mb-1">ধরন</label>
            <select name="frequency" class="border rounded px-3 py-2 text-sm">
                <option value="monthly">মাসিক</option>
                <option value="one_time">একবার</option>
            </select>
        </div>
        <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded text-sm hover:bg-slate-700">যোগ করুন</button>
    </form>
</div>

<form method="get" class="mb-4">
    <select name="class_id" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">-- ক্লাস নির্বাচন করুন --</option>
        <?php foreach ($classes as $c): ?>
            <option value="<?= esc($c['id']) ?>" <?= (string) $selectedClassId === (string) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (! $currentYear): ?>
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">চলতি শিক্ষাবর্ষ নির্ধারিত নেই।</div>
<?php elseif (! $selectedClassId): ?>
    <div class="bg-white rounded-lg shadow p-6 text-gray-500 text-center">একটি ক্লাস নির্বাচন করুন।</div>
<?php else: ?>

<form action="/fees/settings" method="post" class="bg-white rounded-lg shadow p-6">
    <?= csrf_field() ?>
    <input type="hidden" name="class_id" value="<?= esc($selectedClassId) ?>">

    <p class="text-sm text-gray-500 mb-4">এই রেট <?= esc($currentYear['year']) ?> শিক্ষাবর্ষের জন্য এই ক্লাসের ডিফল্ট রেট (মাসিক ফি হলে প্রতি মাসের রেট)।</p>

    <table class="min-w-full text-sm mb-4">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-3 py-2">ফি টাইপ</th>
                <th class="px-3 py-2">ধরন</th>
                <th class="px-3 py-2">রেট (টাকা)</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($feeTypes as $ft): ?>
                <tr>
                    <td class="px-3 py-2"><?= esc($ft['name']) ?></td>
                    <td class="px-3 py-2"><?= $ft['frequency'] === 'monthly' ? 'মাসিক' : 'একবার' ?></td>
                    <td class="px-3 py-2">
                        <input type="number" step="0.01" name="amount[<?= esc($ft['id']) ?>]"
                               value="<?= esc($rates[$ft['id']]['amount'] ?? '') ?>"
                               class="border rounded px-3 py-1 w-32">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded hover:bg-slate-700">সংরক্ষণ করুন</button>
</form>

<p class="text-xs text-gray-400 mt-3">কোনো নির্দিষ্ট ছাত্রের জন্য আলাদা রেট (ছাড়) দিতে চাইলে সেই ছাত্রের প্রোফাইল পেজ থেকে করুন।</p>

<?php endif; ?>

<?= $this->endSection() ?>
