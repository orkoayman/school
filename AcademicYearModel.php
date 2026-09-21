<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademicYearModel extends Model
{
    protected $table            = 'academic_years';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['year', 'is_current'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $validationRules = [
        'year' => 'required|is_unique[academic_years.year,id,{id}]',
    ];

    public function getCurrent()
    {
        return $this->where('is_current', 1)->first();
    }

    /**
     * Set a year as current and unset all others.
     */
    public function setCurrent(int $id): bool
    {
        $this->db->transStart();
        $this->where('id !=', $id)->set(['is_current' => 0])->update();
        $this->update($id, ['is_current' => 1]);
        $this->db->transComplete();

        return $this->db->transStatus();
    }
}
