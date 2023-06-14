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
        const COL_AIRLINE = 'airline';
        const COL_FLIGHTNUMBER = 'flightNumber';
        const COL_DEPARTUREFROM = 'departureFrom';
        const COL_DEPARTURETERMINAL = 'departureTerminal';     
        const COL_DEPARTUREDATETIME = 'departureDateTime';     
        const COL_ARRIVALTO = 'arrivalTo';
        const COL_ARRIVALTERMINAL = 'arrivalTerminal';
        const COL_ARRIVALDATE = 'arrivalDate';
        const COL_FLIGHTDURATION = 'flightDuration';
        const COL_CABIN = 'cabin';
        const COL_FAREBASIS = 'fareBasis';
        const COL_INCLUDEDCHECKEDBAGS = 'includedCheckedBags'; 
        const COL_ISBOOKINGCONFIRMED = 'isBookingConfirmed';   
        const COL_ISPAID = 'isPaid';
        const COL_ISCANCELLED = 'isCancelled';
        const COL_CREATED_AT = 'created_at';
        const COL_UPDATED_AT = 'updated_at';

        /*
         * Eloquent Scopes
         */

        public function scopeById($query, $val) {
                $query->where('id', $val);
        }

                
        public function scopeByBookingReference($query, $val) {
                $query->where('bookingReference', $val);
        }

        public function scopeByIsCancelled($query, $val) {
                $query->where('isCancelled', $val);
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

        public function getArrivalTo() {
                return $this->arrivalTo;
        }

        public function setArrivalTo($value) {
                $this->arrivalTo = $value;
        }

        public function getArrivalTerminal() {
                return $this->arrivalTerminal;
        }

        public function setArrivalTerminal($value) {
                $this->arrivalTerminal = $value;
        }

        public function getArrivalDate() {
                return $this->arrivalDate;
        }

        public function setArrivalDate($value) {
                $this->arrivalDate = $value;
        }

        public function getFlightDuration() {
                return $this->flightDuration;
        }

        public function setFlightDuration($value) {
                $this->flightDuration = $value;
        }

        public function getCabin() {
                return $this->cabin;
        }

        public function setCabin($value) {
                $this->cabin = $value;
        }

        public function getFareBasis() {
                return $this->fareBasis;
        }

        public function setFareBasis($value) {
                $this->fareBasis = $value;
        }

        public function getIncludedCheckedBags() {
                return $this->includedCheckedBags;
        }

        public function setIncludedCheckedBags($value) {       
                $this->includedCheckedBags = $value;
        }

        public function getIsBookingConfirmed() {
                return $this->isBookingConfirmed;
        }

        public function setIsBookingConfirmed($value) {        
                $this->isBookingConfirmed = $value;
        }

        public function getIsPaid() {
                return $this->isPaid;
        }

        public function setIsPaid($value) {
                $this->isPaid = $value;
        }

        public function getIsCancelled() {
                return $this->isCancelled;
        }

        public function setIsCancelled($value) {
                $this->isCancelled = $value;
        }

        public function getCreatedAt() {
                return $this->created_at;
        }

        public function getUpdatedAt() {
                return $this->updated_at;
        }
}
