<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolSettingModel extends Model
{
    protected $table         = 'school_settings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'logo_path', 'address', 'phone'];
    protected $useTimestamps = true;
    protected $updatedField  = 'updated_at';

    /**
     * There is only ever one settings row. Returns it, creating a default
     * one if somehow missing (e.g. migration hasn't seeded it yet).
     */
    public function getSettings(): array
    {
        $row = $this->find(1);

        if (! $row) {
            $id  = $this->insert(['name' => 'স্কুল ম্যানেজমেন্ট সিস্টেম'], true);
            $row = $this->find($id);
        }

        return $row;
    }

    public function updateSettings(array $data): void
    {
        $existing = $this->getSettings();
        $this->update($existing['id'], $data);
    }
}
