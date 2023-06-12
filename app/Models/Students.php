<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'class', 'level', 'parent_contact'];

    public function retrieveExistingRecord($pagination = false, $name = '', $class = '', $level = 0, $parentContact = '') {
        return $this
                ->when(!$pagination, function ($query) use($name, $class, $level, $parentContact) {
                    return
                        $query
                        ->where('name', $name)
                        ->where('class', $class)
                        ->where('level', $level)
                        ->where('parent_contact', $parentContact)
                        ->get();
                })
                ->when($pagination, function ($query) {
                    return $query->paginate(10);
                });

    }
}
