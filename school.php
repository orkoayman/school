<?= $this->extend('layouts/main') ?>

<?php helper('school') ?>

<?= $this->section('content') ?>

<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h3 class="font-semibold mb-4">স্কুলের তথ্য ও লোগো</h3>

    <div class="flex items-center gap-4 mb-6">
        <?php $logoUrl = school_logo_url(); ?>
        <?php if ($logoUrl): ?>
            <img src="<?= esc($logoUrl) ?>" alt="বর্তমান লোগো" class="w-20 h-20 rounded object-cover border">
        <?php else: ?>
            <div class="w-20 h-20 rounded border flex items-center justify-center text-gray-300 text-xs">লোগো নেই</div>
        <?php endif; ?>
        <p class="text-sm text-gray-500">বর্তমান লোগো — নিচের ফর্ম থেকে নতুন ছবি আপলোড করলে এটি বদলে যাবে।</p>
    </div>

    <form action="/settings/school" method="post" enctype="multipart/form-data" class="space-y-4">
        <?= csrf_field() ?>

        <div>
            <label class="block text-sm mb-1">স্কুলের নাম *</label>
            <input type="text" name="name" required value="<?= esc($settings['name']) ?>" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm mb-1">লোগো (jpg/png/webp/svg)</label>
            <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.svg" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm mb-1">ঠিকানা</label>
            <input type="text" name="address" value="<?= esc($settings['address'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm mb-1">ফোন নম্বর</label>
            <input type="text" name="phone" value="<?= esc($settings['phone'] ?? '') ?>" class="w-full border rounded px-3 py-2">
        </div>

        <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded hover:bg-slate-700">সংরক্ষণ করুন</button>
    </form>
</div>

<?= $this->endSection() ?>
