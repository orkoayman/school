<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<form method="get" class="flex gap-2 mb-4">
    <select name="class_id" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">-- ক্লাস --</option>
        <?php foreach ($classes as $c): ?>
            <option value="<?= esc($c['id']) ?>" <?= (string) $selectedClassId === (string) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="exam_id" class="border rounded px-3 py-2 text-sm" onchange="this.form.submit()">
        <option value="">-- পরীক্ষা --</option>
        <?php foreach ($exams as $e): ?>
            <option value="<?= esc($e['id']) ?>" <?= (string) $selectedExamId === (string) $e['id'] ? 'selected' : '' ?>><?= esc($e['name']) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<?php if (! $selectedClassId || ! $selectedExamId): ?>
    <div class="bg-white rounded-lg shadow p-6 text-gray-500 text-center">ক্লাস ও পরীক্ষা নির্বাচন করুন।</div>
<?php elseif (empty($subjects)): ?>
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded">
        এই ক্লাসের জন্য কোনো বিষয় (subject) তৈরি করা হয়নি। <a href="/settings/classes" class="underline">এখানে যোগ করুন</a>।
    </div>
<?php else: ?>

<form action="/results/entry" method="post" class="bg-white rounded-lg shadow overflow-x-auto">
    <?= csrf_field() ?>
    <input type="hidden" name="exam_id" value="<?= esc($selectedExamId) ?>">

    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 text-left">
            <tr>
                <th class="px-4 py-3 sticky left-0 bg-gray-50">রোল/নাম</th>
                <?php foreach ($subjects as $subj): ?>
                    <th class="px-4 py-3 text-center">
                        <?= esc($subj['name']) ?><br>
                        <span class="text-xs font-normal text-gray-400">(পূর্ণ: <?= esc($subj['full_marks']) ?> / পাশ: <?= esc($subj['pass_marks']) ?>)</span>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($roster)): ?>
                <tr><td colspan="<?= count($subjects) + 1 ?>" class="px-4 py-6 text-center text-gray-400">এই ক্লাসে কোনো ছাত্র/ছাত্রী নেই।</td></tr>
            <?php endif; ?>
            <?php foreach ($roster as $r): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 sticky left-0 bg-white font-medium">
                        <?= esc($r['roll_in_class'] ?? '-') ?> - <?= esc($r['name']) ?>
                    </td>
                    <?php foreach ($subjects as $subj): ?>
                        <td class="px-2 py-2 text-center">
                            <input type="number" step="0.5" min="0" max="<?= esc($subj['full_marks']) ?>"
                                   name="marks[<?= esc($r['id']) ?>][<?= esc($subj['id']) ?>]"
                                   value="<?= esc($r['marks'][$subj['id']] ?? '') ?>"
                                   class="w-16 border rounded px-2 py-1 text-center">
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (! empty($roster)): ?>
    <div class="p-4 border-t">
        <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded hover:bg-slate-700">
            সংরক্ষণ করুন
        </button>
        <span class="text-xs text-gray-400 ml-2">খালি রেখে দিলে সেই বিষয়ের নাম্বার এন্ট্রি হবে না।</span>
    </div>
    <?php endif; ?>
</form>

<?php endif; ?>

<?= $this->endSection() ?>
