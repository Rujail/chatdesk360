<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'feature_list', 'price', 'per_agent', 'recommended'];
    protected $casts = [
        'feature_list' => 'array',
        'per_agent' => 'boolean',
        'recommended' => 'boolean',
    ];

    public function getFormattedPriceAttribute() {
        return '$' . number_format($this->price / 100, 2);
    }

    // Discount percent as a clean float (field is stored as varchar)
    public function getAnnualDiscountAttribute() {
        return $this->annual_discount_percent ? (float) $this->annual_discount_percent : 0;
    }

    // Full annual price with NO discount applied (monthly price x 12)
    public function getFullAnnualPriceAttribute() {
        return $this->price * 12;
    }

    // Actual discounted annual price (what gets charged if discount exists)
    public function getDiscountedAnnualPriceAttribute() {
        if ($this->annual_discount > 0) {
            return (int) round($this->full_annual_price * (1 - ($this->annual_discount / 100)));
        }
        return $this->full_annual_price;
    }

    // Dollar amount saved per year by choosing annual over monthly x12
    public function getAnnualSavingsAttribute() {
        return $this->full_annual_price - $this->discounted_annual_price;
    }

    public function getHasAnnualDiscountAttribute() {
        return $this->annual_discount > 0;
    }
}