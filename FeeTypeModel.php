<?php

namespace App\Models;

use CodeIgniter\Model;

class FeeTypeModel extends Model
{
    protected $table            = 'fee_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'frequency'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $validationRules = [
        'name'      => 'required|max_length[100]',
        'frequency' => 'required|in_list[monthly,one_time]',
    ];
}
