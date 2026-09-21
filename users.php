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

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="font-semibold mb-3">নতুন ইউজার (Admin/Teacher) তৈরি করুন</h3>
    <form action="/settings/users" method="post" class="grid grid-cols-2 gap-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm mb-1">নাম</label>
            <input type="text" name="name" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">ইমেইল</label>
            <input type="email" name="email" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">ফোন</label>
            <input type="text" name="phone" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">ভূমিকা (Role)</label>
            <select name="role" class="w-full border rounded px-3 py-2">
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-sm mb-1">পাসওয়ার্ড</label>
            <input type="password" name="password" required minlength="6" class="w-full border rounded px-3 py-2">
        </div>
        <div class="col-span-2">
            <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded hover:bg-slate-700">তৈরি করুন</button>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">নাম</th>
                <th class="px-4 py-3">ইমেইল</th>
                <th class="px-4 py-3">ভূমিকা</th>
                <th class="px-4 py-3">অবস্থা</th>
                <th class="px-4 py-3">কার্যক্রম</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($users as $u): ?>
                <tr>
                    <td class="px-4 py-2"><?= esc($u['name']) ?></td>
                    <td class="px-4 py-2"><?= esc($u['email']) ?></td>
                    <td class="px-4 py-2"><?= esc($u['role']) ?></td>
                    <td class="px-4 py-2">
                        <?= $u['status'] === 'active' ? '<span class="text-green-600">সক্রিয়</span>' : '<span class="text-red-600">নিষ্ক্রিয়</span>' ?>
                    </td>
                    <td class="px-4 py-2">
                        <form action="/settings/users/<?= esc($u['id']) ?>/toggle" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="text-blue-600 text-xs hover:underline">
                                <?= $u['status'] === 'active' ? 'নিষ্ক্রিয় করুন' : 'সক্রিয় করুন' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
