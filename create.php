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

<?php if (! $currentYear): ?>
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4">
        প্রথমে একটি চলতি শিক্ষাবর্ষ নির্ধারণ করতে হবে। <a href="/settings/classes" class="underline">এখানে যান</a>।
    </div>
<?php else: ?>

<form action="/students" method="post" class="bg-white rounded-lg shadow p-6 max-w-2xl space-y-4">
    <?= csrf_field() ?>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">নাম *</label>
            <input type="text" name="name" required value="<?= esc(old('name')) ?>" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">স্টুডেন্ট কোড (ইউনিক আইডি) *</label>
            <input type="text" name="student_code" required value="<?= esc(old('student_code')) ?>" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">ক্লাস *</label>
            <select name="class_id" required class="w-full border rounded px-3 py-2">
                <option value="">-- নির্বাচন করুন --</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= esc($c['id']) ?>"><?= esc($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm mb-1">রোল</label>
                <input type="number" name="roll_in_class" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">সেকশন</label>
                <input type="text" name="section" class="w-full border rounded px-3 py-2">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm mb-1">কার্ড নম্বর (থাকলে)</label>
            <input type="text" name="card_number" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">জন্ম তারিখ</label>
            <input type="date" name="date_of_birth" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm mb-1">লিঙ্গ</label>
            <select name="gender" class="w-full border rounded px-3 py-2">
                <option value="">-- নির্বাচন --</option>
                <option value="male">ছেলে</option>
                <option value="female">মেয়ে</option>
                <option value="other">অন্যান্য</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">বাবার নাম</label>
            <input type="text" name="father_name" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">মায়ের নাম</label>
            <input type="text" name="mother_name" class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm mb-1">অভিভাবকের ফোন নম্বর (SMS পাঠানো হবে এই নম্বরে) *</label>
        <input type="text" name="parent_phone" required value="<?= esc(old('parent_phone')) ?>" placeholder="01XXXXXXXXX" class="w-full border rounded px-3 py-2">
    </div>

    <div>
        <label class="block text-sm mb-1">ঠিকানা</label>
        <textarea name="address" rows="2" class="w-full border rounded px-3 py-2"></textarea>
    </div>

    <div class="pt-2 flex gap-2">
        <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded hover:bg-slate-700">সংরক্ষণ করুন</button>
        <a href="/students" class="px-5 py-2 rounded border">বাতিল</a>
    </div>
</form>

<?php endif; ?>

<?= $this->endSection() ?>
