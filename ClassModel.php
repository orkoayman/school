<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table            = 'classes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'numeric_order'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $validationRules = [
        'name'          => 'required|max_length[50]',
        'numeric_order' => 'required|is_natural_no_zero',
    ];

    public function getOrdered()
    {
        return $this->orderBy('numeric_order', 'ASC')->findAll();
    }

    /**
     * Get the next class up (for promotion), or null if this is the highest class.
     */
    public function getNextClass(int $classId)
    {
        $current = $this->find($classId);
        if (! $current) {
            return null;
        }

        return $this->where('numeric_order', $current['numeric_order'] + 1)->first();
    }
}
