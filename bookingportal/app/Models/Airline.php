<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    
    protected $table = 'airline';
    protected $primaryKey = 'id';
//      protected $guarded = [];
//      protected $fillable = [];

    const COL_ID = 'id';
    const COL_AIRLINENAME = 'airlineName';
    const COL_IATADESIGNATOR = 'IataDesignator';
    const COL_THREEDIGITAIRLINECODE = 'threeDigitAirlineCode';
    const COL_IATACODE = 'IataCode';
    const COL_COUNTRY = 'country';
    const COL_CREATED_AT = 'created_at';
    const COL_UPDATED_AT = 'updated_at';

    /*
     * Eloquent Scopes
     */

    public function scopeById($query, $val) {
            $query->where('id', $val);
    }

    public function scopeByIataDesignator($query, $val) {
        $query->where('IataDesignator', $val);
    }

    public function scopeByThreeDigitAirlineCode($query, $val) {
        $query->where('threeDigitAirlineCode', $val);
    }
    /*
     * GET / SET
     */

    public function getAirlineId() {
            return $this->id;
    }

    public function getAirlineName() {
            return $this->airlineName;
    }

    public function setAirlineName($value) {
            $this->airlineName = $value;
    }

    public function getIataDesignator() {
            return $this->IataDesignator;
    }

    public function setIataDesignator($value) {
            $this->IataDesignator = $value;
    }

    public function ByThreeDigitAirlineCode() {
            return $this->threeDigitAirlineCode;
    }

    public function setThreeDigitAirlineCode($value) {     
            $this->threeDigitAirlineCode = $value;
    }

    public function getIataCode() {
            return $this->IataCode;
    }

    public function setIataCode($value) {
            $this->IataCode = $value;
    }

    public function getCountry() {
            return $this->country;
    }

    public function setCountry($value) {
            $this->country = $value;
    }

    public function getCreatedAt() {
            return $this->created_at;
    }

    public function getUpdatedAt() {
            return $this->updated_at;
    }
}
