<?php

namespace App\Models;

use CodeIgniter\Model;

class SmsLogModel extends Model
{
    protected $table            = 'sms_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['student_id', 'phone', 'message', 'type', 'status', 'response'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
}
