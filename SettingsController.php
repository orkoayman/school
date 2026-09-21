<?php

namespace App\Controllers;

use App\Models\AcademicYearModel;
use App\Models\ClassModel;
use App\Models\ExamModel;
use App\Models\SchoolSettingModel;
use App\Models\SubjectModel;

class SettingsController extends BaseController
{
    protected AcademicYearModel $academicYearModel;
    protected ClassModel $classModel;
    protected SubjectModel $subjectModel;
    protected ExamModel $examModel;
    protected SchoolSettingModel $schoolSettingModel;

    public function __construct()
    {
        $this->academicYearModel = new AcademicYearModel();
        $this->classModel        = new ClassModel();
        $this->subjectModel      = new SubjectModel();
        $this->examModel         = new ExamModel();
        $this->schoolSettingModel = new SchoolSettingModel();
    }

    public function schoolInfo()
    {
        $data = [
            'title'    => 'স্কুলের তথ্য ও লোগো',
            'settings' => $this->schoolSettingModel->getSettings(),
        ];

        return view('settings/school', $data, ['saveData' => false]);
    }

    public function saveSchoolInfo()
    {
        $name    = trim($this->request->getPost('name'));
        $address = $this->request->getPost('address');
        $phone   = $this->request->getPost('phone');

        if (! $name) {
            return redirect()->back()->with('error', 'স্কুলের নাম দিন।');
        }

        $data = ['name' => $name, 'address' => $address, 'phone' => $phone];

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && ! $logo->hasMoved()) {
            if (! in_array($logo->getExtension(), ['jpg', 'jpeg', 'png', 'webp', 'svg'], true)) {
                return redirect()->back()->with('error', 'শুধুমাত্র jpg/png/webp/svg ছবি আপলোড করা যাবে।');
            }

            $newName = $logo->getRandomName();
            $logo->move(FCPATH . 'uploads/logo', $newName);

            // Remove the old logo file if one existed, to avoid piling up unused files.
            $existing = $this->schoolSettingModel->getSettings();
            if (! empty($existing['logo_path'])) {
                $oldPath = FCPATH . 'uploads/logo/' . $existing['logo_path'];
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            $data['logo_path'] = $newName;
        }

        $this->schoolSettingModel->updateSettings($data);

        return redirect()->to('/settings/school')->with('message', 'স্কুলের তথ্য হালনাগাদ করা হয়েছে।');
    }

    public function classes()
    {
        $classId = $this->request->getGet('class_id');

        $data = [
            'title'       => 'ক্লাস ও বিষয় সেটিংস',
            'years'       => $this->academicYearModel->orderBy('year', 'DESC')->findAll(),
            'classes'     => $this->classModel->getOrdered(),
            'selectedClassId' => $classId,
            'subjects'    => $classId ? $this->subjectModel->forClass((int) $classId) : [],
        ];

        return view('settings/classes', $data, ['saveData' => false]);
    }

    /**
     * Creates a new academic year AND auto-creates its 3 standard exams,
     * so the admin doesn't need a separate step for that.
     */
    public function saveYear()
    {
        $year = trim($this->request->getPost('year'));

        if (! $year) {
            return redirect()->back()->with('error', 'সাল লিখুন।');
        }

        $existing = $this->academicYearModel->where('year', $year)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'এই সাল আগে থেকেই আছে।');
        }

        $yearId = $this->academicYearModel->insert(['year' => $year], true);
        $this->examModel->createStandardExamsForYear($yearId);

        return redirect()->to('/settings/classes')->with('message', "শিক্ষাবর্ষ {$year} তৈরি করা হয়েছে, সাথে ৩টি পরীক্ষাও যোগ হয়েছে।");
    }

    public function activateYear(int $id)
    {
        $this->academicYearModel->setCurrent($id);

        return redirect()->to('/settings/classes')->with('message', 'চলতি শিক্ষাবর্ষ পরিবর্তন করা হয়েছে।');
    }

    public function saveClass()
    {
        $name  = trim($this->request->getPost('name'));
        $order = $this->request->getPost('numeric_order');

        if (! $name || ! $order) {
            return redirect()->back()->with('error', 'ক্লাসের নাম ও ক্রম নম্বর দিন।');
        }

        $this->classModel->insert(['name' => $name, 'numeric_order' => $order]);

        return redirect()->to('/settings/classes')->with('message', 'নতুন ক্লাস যোগ করা হয়েছে।');
    }

    public function saveSubject()
    {
        $classId   = $this->request->getPost('class_id');
        $name      = trim($this->request->getPost('name'));
        $fullMarks = $this->request->getPost('full_marks') ?: 100;
        $passMarks = $this->request->getPost('pass_marks') ?: 33;

        if (! $classId || ! $name) {
            return redirect()->back()->with('error', 'ক্লাস ও বিষয়ের নাম দিন।');
        }

        $this->subjectModel->insert([
            'class_id'   => $classId,
            'name'       => $name,
            'full_marks' => $fullMarks,
            'pass_marks' => $passMarks,
        ]);

        return redirect()->to('/settings/classes?class_id=' . $classId)->with('message', 'বিষয় যোগ করা হয়েছে।');
    }
}
