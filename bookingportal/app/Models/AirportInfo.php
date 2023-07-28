<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirportInfo extends Model
{
    use HasFactory;

    
	protected $table = 'airports_info';
	protected $primaryKey = 'id';
	public $timestamps = false;
//	protected $guarded = [];
//	protected $fillable = [];

	const COL_ID = 'id';
	const COL_AIRPORTICAO = 'airportIcao';
	const COL_AIRPORTNAME = 'airportName';
	const COL_CITY = 'city';
	const COL_COUNTRY = 'country';

	/*
	 * Eloquent Scopes
	 */

	public function scopeById($query, $val) {
		$query->where('id', $val);
	}

    public function scopeByAirportIcao($query, $val) {
		$query->where('airportIcao', $val);
	}

	/*
	 * GET / SET
	 */

	public function getAirportsInfoId() {
		return $this->id;
	}

	public function getAirportIcao() {
		return $this->airportIcao;
	}

	public function setAirportIcao($value) {
		$this->airportIcao = $value;
	}

	public function getAirportName() {
		return $this->airportName;
	}

	public function setAirportName($value) {
		$this->airportName = $value;
	}

	public function getCity() {
		return $this->city;
	}

	public function setCity($value) {
		$this->city = $value;
	}

	public function getCountry() {
		return $this->country;
	}

	public function setCountry($value) {
		$this->country = $value;
	}


}
