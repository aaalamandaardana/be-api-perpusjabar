<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;  
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory, HasUuids;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'books';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'release_year',
        'summary',
        'image_url',
        'image_public_id',
        'stok',
        'category_id'
    ];


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Get the category that being reference of the book.
     * [Book is The Child Model]
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    /**
     * Get the list borrows based on book.
     * [Book is The Parent Model]
     */
    public function listBorrows(): HasMany
    {
        return $this->hasMany(Borrow::class, 'book_id');
    }


    /**
     * Decrease Book's Stock.
     */
    public function decreaseStock()
    {
        $this->stok--;
        $this->save();
    }


    /**
     * Increase Book's Stock.
     */
    public function increaseStock()
    {
        $this->stok++;
        $this->save();
    }
}