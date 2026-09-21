<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-4 text-sm">
        <ul class="list-disc list-inside">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/students/<?= esc($student['id']) ?>" method="post" class="bg-white rounded-lg shadow p-6 max-w-2xl space-y-4">
    <?= csrf_field() ?>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">নাম *</label>
            <input type="text" name="name" required value="<?= esc(old('name') ?? $student['name']) ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">স্টুডেন্ট কোড *</label>
            <input type="text" name="student_code" required value="<?= esc(old('student_code') ?? $student['student_code']) ?>" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <?php if ($currentYear): ?>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">ক্লাস (<?= esc($currentYear['year']) ?>)</label>
            <select name="class_id" class="w-full border rounded px-3 py-2">
                <option value="">-- নির্বাচন করুন --</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= esc($c['id']) ?>" <?= ($enrollment['class_id'] ?? null) == $c['id'] ? 'selected' : '' ?>>
                        <?= esc($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm mb-1">রোল</label>
                <input type="number" name="roll_in_class" value="<?= esc($enrollment['roll_in_class'] ?? '') ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">সেকশন</label>
                <input type="text" name="section" value="<?= esc($enrollment['section'] ?? '') ?>" class="w-full border rounded px-3 py-2">
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">কার্ড নম্বর</label>
            <input type="text" name="card_number" value="<?= esc($student['card_number'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">জন্ম তারিখ</label>
            <input type="date" name="date_of_birth" value="<?= esc($student['date_of_birth'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm mb-1">লিঙ্গ</label>
            <select name="gender" class="w-full border rounded px-3 py-2">
                <option value="">-- নির্বাচন --</option>
                <option value="male" <?= ($student['gender'] ?? '') === 'male' ? 'selected' : '' ?>>ছেলে</option>
                <option value="female" <?= ($student['gender'] ?? '') === 'female' ? 'selected' : '' ?>>মেয়ে</option>
                <option value="other" <?= ($student['gender'] ?? '') === 'other' ? 'selected' : '' ?>>অন্যান্য</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">বাবার নাম</label>
            <input type="text" name="father_name" value="<?= esc($student['father_name'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">মায়ের নাম</label>
            <input type="text" name="mother_name" value="<?= esc($student['mother_name'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm mb-1">অভিভাবকের ফোন নম্বর *</label>
        <input type="text" name="parent_phone" required value="<?= esc(old('parent_phone') ?? $student['parent_phone']) ?>" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm mb-1">ঠিকানা</label>
        <textarea name="address" rows="2" class="w-full border rounded px-3 py-2"><?= esc($student['address'] ?? '') ?></textarea>
    </div>

    <div>
        <label class="block text-sm mb-1">অবস্থা</label>
        <select name="status" class="w-full border rounded px-3 py-2">
            <option value="active" <?= $student['status'] === 'active' ? 'selected' : '' ?>>সক্রিয়</option>
            <option value="inactive" <?= $student['status'] === 'inactive' ? 'selected' : '' ?>>নিষ্ক্রিয়</option>
            <option value="left" <?= $student['status'] === 'left' ? 'selected' : '' ?>>স্কুল ত্যাগ করেছে</option>
        </select>
    </div>

    <div class="pt-2 flex gap-2">
        <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded hover:bg-slate-700">হালনাগাদ করুন</button>
        <a href="/students" class="px-5 py-2 rounded border">বাতিল</a>
    </div>
</form>

<?= $this->endSection() ?>
