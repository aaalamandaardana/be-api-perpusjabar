<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, HasUuids;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Get the list users based on role.
     * [Role is The Parent Model]
     */
    public function listUsers(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}