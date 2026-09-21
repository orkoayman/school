<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'students';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'name', 'student_code', 'card_number', 'date_of_birth', 'gender',
        'father_name', 'mother_name', 'parent_phone', 'address', 'photo', 'status',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'          => 'required|min_length[2]|max_length[150]',
        'student_code'  => 'required|is_unique[students.student_code,id,{id}]',
        'parent_phone'  => 'required|min_length[11]|max_length[20]',
        'card_number'   => 'permit_empty|is_unique[students.card_number,id,{id}]',
    ];

    public function findByCard(string $cardNumber)
    {
        return $this->where('card_number', $cardNumber)->first();
    }

    /**
     * Search students by name, code, or card number - for admin quick search.
     */
    public function search(string $term)
    {
        return $this->groupStart()
            ->like('name', $term)
            ->orLike('student_code', $term)
            ->orLike('card_number', $term)
            ->orLike('parent_phone', $term)
            ->groupEnd()
            ->findAll();
    }
}
