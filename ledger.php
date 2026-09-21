<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4"><?= esc($student['name'] ?? '') ?> - ফি খতিয়ান</h2>

    <?php if (empty($breakdown)): ?>
        <p class="text-gray-400">এই বছরের জন্য কোনো ফি রেট সেট করা নেই।</p>
    <?php else: ?>
    <table class="min-w-full text-sm mb-4">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-3 py-2">ফি টাইপ</th>
                <th class="px-3 py-2">ধরন</th>
                <th class="px-3 py-2 text-right">মোট প্রাপ্য</th>
                <th class="px-3 py-2 text-right">পরিশোধিত</th>
                <th class="px-3 py-2 text-right">বাকি</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php foreach ($breakdown['items'] as $item): ?>
                <tr>
                    <td class="px-3 py-2"><?= esc($item['fee_type_name']) ?></td>
                    <td class="px-3 py-2"><?= $item['frequency'] === 'monthly' ? 'মাসিক' : 'একবার' ?></td>
                    <td class="px-3 py-2 text-right"><?= number_format($item['total_due'], 2) ?></td>
                    <td class="px-3 py-2 text-right"><?= number_format($item['total_paid'], 2) ?></td>
                    <td class="px-3 py-2 text-right <?= $item['balance'] > 0 ? 'text-red-600 font-medium' : 'text-green-600' ?>">
                        <?= number_format($item['balance'], 2) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="font-semibold border-t-2">
                <td class="px-3 py-2" colspan="4">সর্বমোট বাকি</td>
                <td class="px-3 py-2 text-right text-red-700"><?= number_format($breakdown['grand_total_balance'], 2) ?> ৳</td>
            </tr>
        </tfoot>
    </table>
    <?php endif; ?>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="font-semibold mb-3">এই ছাত্রের জন্য বিশেষ রেট (ছাড়/ওভাররাইড)</h3>
    <form action="/fees/student-override" method="post" class="flex gap-2 items-end flex-wrap">
        <?= csrf_field() ?>
        <input type="hidden" name="student_id" value="<?= esc($student['id']) ?>">
        <input type="hidden" name="academic_year_id" value="<?= esc($yearId) ?>">
        <div>
            <label class="block text-xs mb-1">ফি টাইপ</label>
            <select name="fee_type_id" class="border rounded px-3 py-2 text-sm">
                <?php foreach ($feeTypes as $ft): ?>
                    <option value="<?= esc($ft['id']) ?>"><?= esc($ft['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs mb-1">নতুন রেট (টাকা)</label>
            <input type="number" step="0.01" name="amount" required class="border rounded px-3 py-2 text-sm w-32">
        </div>
        <div>
            <label class="block text-xs mb-1">কারণ (ঐচ্ছিক)</label>
            <input type="text" name="note" placeholder="যেমন: ৫০% ছাড়" class="border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded text-sm hover:bg-slate-700">সংরক্ষণ করুন</button>
    </form>
</div>

<div class="grid grid-cols-2 gap-6">
    <!-- Record payment -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-3">পেমেন্ট এন্ট্রি করুন</h3>
        <form action="/fees/payment" method="post" class="space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="student_id" value="<?= esc($student['id']) ?>">
            <input type="hidden" name="academic_year_id" value="<?= esc($yearId) ?>">

            <div>
                <label class="block text-sm mb-1">ফি টাইপ</label>
                <select name="fee_type_id" required class="w-full border rounded px-3 py-2">
                    <?php foreach ($feeTypes as $ft): ?>
                        <option value="<?= esc($ft['id']) ?>"><?= esc($ft['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm mb-1">টাকার পরিমাণ</label>
                    <input type="number" step="0.01" name="amount" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-1">মাস (মাসিক ফি হলে, ঐচ্ছিক)</label>
                    <select name="for_month" class="w-full border rounded px-3 py-2">
                        <option value="">-</option>
                        <?php foreach (['১','২','৩','৪','৫','৬','৭','৮','৯','১০','১১','১২'] as $i => $label): ?>
                            <option value="<?= $i + 1 ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm mb-1">তারিখ</label>
                <input type="date" name="paid_date" value="<?= date('Y-m-d') ?>" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">মন্তব্য</label>
                <input type="text" name="note" class="w-full border rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded hover:bg-slate-700">সংরক্ষণ করুন</button>
        </form>
    </div>

    <!-- Payment history -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-3">পেমেন্টের ইতিহাস</h3>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-3 py-2">তারিখ</th>
                    <th class="px-3 py-2">ফি টাইপ</th>
                    <th class="px-3 py-2 text-right">টাকা</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if (empty($payments)): ?>
                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400">কোনো পেমেন্ট রেকর্ড নেই।</td></tr>
                <?php endif; ?>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td class="px-3 py-2"><?= esc($p['paid_date']) ?></td>
                        <td class="px-3 py-2"><?= esc($p['fee_type_name']) ?></td>
                        <td class="px-3 py-2 text-right"><?= number_format($p['amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
