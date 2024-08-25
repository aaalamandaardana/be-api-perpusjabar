<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasUuids;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';


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
     * Get the list books based on category.
     * [Category is The Parent Model]
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'category_id');
    }


    /**
     * Sorting List Books From Newest to Oldest
     */
    public function listBooks()
    {
        return $this->books()->orderBy('release_year', 'desc')->orderBy('created_at', 'desc');
    }
}