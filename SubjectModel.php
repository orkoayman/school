<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table            = 'subjects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['class_id', 'name', 'full_marks', 'pass_marks'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $validationRules = [
        'name'       => 'required|max_length[100]',
        'full_marks' => 'required|is_natural_no_zero',
        'pass_marks' => 'required|is_natural',
    ];

    public function forClass(int $classId)
    {
        return $this->where('class_id', $classId)->findAll();
    }
}
