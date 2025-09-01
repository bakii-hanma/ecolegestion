<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentModel extends Model
{
    protected $table = 'parents';
    
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'phone_2',
        'gender',
        'address',
        'profession',
        'workplace',
        'relationship',
        'is_primary_contact',
        'can_pickup',
        'user_id'
    ];
    
    protected $casts = [
        'is_primary_contact' => 'boolean',
        'can_pickup' => 'boolean'
    ];
    
    // Relation many-to-many avec Student
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_parent', 'parent_id', 'student_id');
    }
}
