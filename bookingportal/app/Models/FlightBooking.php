<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightBooking extends Model
{
    
    protected $table = 'flights_bookings';
    protected $primaryKey = 'id';
//      protected $guarded = [];
//      protected $fillable = [];

    const COL_ID = 'id';
    const COL_BOOKINGREFERENCE = 'bookingReference';
    const COL_TIME = 'time';
    const COL_AIRLINE = 'airline';
    const COL_FLIGHTNUMBER = 'flightNumber';
    const COL_DEPARTUREFROM = 'departureFrom';
    const COL_DEPARTURETERMINAL = 'departureTerminal';
    const COL_DEPARTUREDATETIME = 'departureDateTime';
    const COL_ARRIVELTO = 'arrivelTo';
    const COL_ARRIVELTERMINAL = 'arrivelTerminal';
    const COL_ARRIVELDATE = 'arrivelDate';
    const COL_FLIGHTDURATION = 'flightDuration';
    const COL_ISBOOKINGCONFIRMED = 'isBookingConfirmed';
    const COL_CREATED_AT = 'created_at';
    const COL_UPDATED_AT = 'updated_at';

    /*
     * Eloquent Scopes
     */

    public function scopeById($query, $val) {
            $query->where('id', $val);
    }

    public function scopeFromTime($query, $val) {
            $query->where('time', '>=', $val);
    }

    public function scopeToTime($query, $val) {
            $query->where('time', '<', $val);
    }

    /*
     * GET / SET
     */

    public function getFlightsBookingId() {
            return $this->id;
    }

    public function getBookingReference() {
            return $this->bookingReference;
    }

    public function setBookingReference($value) {
            $this->bookingReference = $value;
    }

    public function getTime() {
            return $this->time;
    }

    public function setTime($value) {
            $this->time = $value;
    }

    public function getAirline() {
            return $this->airline;
    }

    public function setAirline($value) {
            $this->airline = $value;
    }

    public function getFlightNumber() {
            return $this->flightNumber;
    }

    public function setFlightNumber($value) {
            $this->flightNumber = $value;
    }

    public function getDepartureFrom() {
            return $this->departureFrom;
    }

    public function setDepartureFrom($value) {
            $this->departureFrom = $value;
    }

    public function getDepartureTerminal() {
            return $this->departureTerminal;
    }

    public function setDepartureTerminal($value) {
            $this->departureTerminal = $value;
    }

    public function getDepartureDateTime() {
            return $this->departureDateTime;
    }

    public function setDepartureDateTime($value) {
            $this->departureDateTime = $value;
    }

    public function getArrivelTo() {
            return $this->arrivelTo;
    }

    public function setArrivelTo($value) {
            $this->arrivelTo = $value;
    }

    public function getArrivelTerminal() {
            return $this->arrivelTerminal;
    }

    public function setArrivelTerminal($value) {
            $this->arrivelTerminal = $value;
    }

    public function getArrivelDate() {
            return $this->arrivelDate;
    }

    public function setArrivelDate($value) {
            $this->arrivelDate = $value;
    }

    public function getFlightDuration() {
            return $this->flightDuration;
    }

    public function setFlightDuration($value) {
            $this->flightDuration = $value;
    }

    public function getIsBookingConfirmed() {
            return $this->isBookingConfirmed;
    }

    public function setIsBookingConfirmed($value) {
            $this->isBookingConfirmed = $value;
    }

    public function getCreatedAt() {
            return $this->created_at;
    }

    public function getUpdatedAt() {
            return $this->updated_at;
    }
}
