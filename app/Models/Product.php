<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'author_id',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'sku',
        'price',
        'sale_price',
        'stock',
        'status',
    ];

    protected $appends = [
        'has_discount',
        'discount_value',
        'discount_percent',
        'badge',
        'main_image'
    ];

    public function category()
    {
        return $this->BelongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function images()
    {
        return $this->hasMany(Image::class)->orderByDesc('id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->orderBy('rating', 'desc');
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /** scope for active items */
    public function scopeActiveEntries($query)
    {
        return $query->where([
            'status' => 1,
        ]);
    }

    public function getHasDiscountAttribute()
    {
        return $this->stock > 0 && $this->sale_price && $this->sale_price < $this->price;
    }

    public function getDiscountValueAttribute()
    {
        return $this->has_discount ? $this->price - $this->sale_price : 0;
    }

    public function getDiscountPercentAttribute()
    {
        return $this->has_discount ? round(($this->discount_value / $this->price) * 100) : 0;
    }


    public function getBadgeAttribute()
    {
        if ($this->stock == 0) {
            return ['text' => 'Sold', 'class' => 'badge badge-light text-danger'];
        } elseif ($this->has_discount) {
            return ['text' => 'Sale', 'class' => 'badge badge-light text-warning'];
        } elseif ($this->created_at >= now()->subDays(7)) {
            return ['text' => 'New', 'class' => 'badge badge-light text-success'];
        }
        /** badge text-white badge-success */
        return null;
    }

    public function getMainImageAttribute()
    {
        return $this->images->firstWhere('is_main', 1);
    }

    // تنفع كدا برده
    //   public function mainImage()
    // {
    //     return $this->hasOne(ProductImage::class)->where('is_main', 1);
    // }

}
