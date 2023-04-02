<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerInfo extends Model
{
 

    const PASSENGERS_ARRAY = 'passengers';
    const VALIDATINGAIRLINE = 'validatingAirlineCodes';
    protected $table = 'passenger_info';
    protected $primaryKey = 'id';
//      protected $guarded = [];
//      protected $fillable = [];

    const COL_ID = 'id';
    const COL_PNR = 'PNR';
    const COL_PAYMENTINFOID = 'PaymentInfoId';
    const COL_FIRSTNAME = 'firstName';
    const COL_LASTNAME = 'lastName';
    const COL_DATEOFBIRTH = 'dateOfBirth';
    const COL_EMAIL = 'email';
    const COL_PASSENGERTYPE = 'passengerType';
    const COL_TICKETNUMBER = 'ticketNumber';
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
        $query->where('PNR', $val);
}

public function scopeByIsCancelled($query, $val) {
        $query->where('isCancelled', $val);
}

    /*
     * GET / SET
     */

    public function getPassengerInfoId() {
            return $this->id;
    }

    public function getPNR() {
            return $this->PNR;
    }

    public function setPNR($value) {
            $this->PNR = $value;
    }

    public function getPaymentInfoId() {
            return intval($this->PaymentInfoId);
    }

    public function setPaymentInfoId($value) {
            $this->PaymentInfoId = $value;
    }

    public function getFirstName() {
            return $this->firstName;
    }

    public function setFirstName($value) {
            $this->firstName = $value;
    }

    public function getLastName() {
            return $this->lastName;
    }

    public function setLastName($value) {
            $this->lastName = $value;
    }

    public function getDateOfBirth() {
            return $this->dateOfBirth;
    }

    public function setDateOfBirth($value) {
            $this->dateOfBirth = $value;
    }

    public function getEmail() {
            return $this->email;
    }

    public function setEmail($value) {
            $this->email = $value;
    }

    public function getPassengerType() {
            return $this->passengerType;
    }

    public function setPassengerType($value) {
            $this->passengerType = $value;
    }

    public function getTicketNumber() {
            return $this->ticketNumber;
    }

    public function setTicketNumber($value) {
            $this->ticketNumber = $value;
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
