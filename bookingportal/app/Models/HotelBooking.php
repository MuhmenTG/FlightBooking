<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{

        protected $table = 'hotels_bookings';
        protected $primaryKey = 'id';
        //      protected $guarded = [];
        //      protected $fillable = [];

        const COL_ID = 'id';
        const COL_HOTELBOOKINGREFERENCE = 'hotelBookingReference';
        const COL_ISSUEDATE = 'issueDate';
        const COL_HOTELNAME = 'hotelName';
        const COL_HOTELLOCATION = 'hotelLocation';
        const COL_HOTELCITY = 'hotelCity';
        const COL_HOTELCONTACT = 'hotelContact';
        const COL_CHECKINDATE = 'checkInDate';
        const COL_CHECKOUTDATE = 'checkOutDate';
        const COL_ROOMTYPE = 'roomType';
        const COL_MAINGUESTFIRSTNAME = 'mainGuestFirstName';
        const COL_MAINGUESTLASNAME = 'mainGuestLasName';
        const COL_MAINGUESTEMAIL = 'mainGuestEmail';
        const COL_NUMBEROFADULTS = 'numberOfAdults';
        const COL_NUMBEROFCHILDREN = 'numberOfChildren';
        const COL_POLICIESCHECKINOUTCHECKIN = 'policiesCheckInOutCheckIn';
        const COL_POLICIESCHECKINOUTCHECKOUT = 'policiesCheckInOutCheckOut';
        const COL_POLICIESCANCELLATIONDEADLINE = 'policiesCancellationDeadline';
        const COL_PAYMENTINFOID = 'paymentInfoId';
        const COL_DESCRIPTION = 'description';
        const COL_CREATED_AT = 'created_at';
        const COL_UPDATED_AT = 'updated_at';

        /*
     * Eloquent Scopes
     */

        public function scopeById($query, $val)
        {
                $query->where('id', $val);
        }
        
        public function scopeByHotelBookingReference($query, $val)
        {
                $query->where('hotelBookingReference', $val);
        }


        /*
     * GET / SET
     */

        public function getHotelsBookingId()
        {
                return $this->id;
        }

        public function getHotelBookingReference()
        {
                return $this->hotelBookingReference;
        }

        public function setHotelBookingReference($value)
        {
                $this->hotelBookingReference = $value;
        }

        public function getIssueDate()
        {
                return $this->issueDate;
        }

        public function setIssueDate($value)
        {
                $this->issueDate = $value;
        }

        public function getHotelName()
        {
                return $this->hotelName;
        }

        public function setHotelName($value)
        {
                $this->hotelName = $value;
        }

        public function getHotelLocation()
        {
                return $this->hotelLocation;
        }

        public function setHotelLocation($value)
        {
                $this->hotelLocation = $value;
        }

        public function getHotelCity()
        {
                return $this->hotelCity;
        }

        public function setHotelCity($value)
        {
                $this->hotelCity = $value;
        }

        public function getHotelContact()
        {
                return $this->hotelContact;
        }

        public function setHotelContact($value)
        {
                $this->hotelContact = $value;
        }

        public function getCheckInDate()
        {
                return $this->checkInDate;
        }

        public function setCheckInDate($value)
        {
                $this->checkInDate = $value;
        }

        public function getCheckOutDate()
        {
                return $this->checkOutDate;
        }

        public function setCheckOutDate($value)
        {
                $this->checkOutDate = $value;
        }

        public function getRoomType()
        {
                return $this->roomType;
        }

        public function setRoomType($value)
        {
                $this->roomType = $value;
        }

        public function getMainGuestFirstName()
        {
                return $this->mainGuestFirstName;
        }

        public function setMainGuestFirstName($value)
        {
                $this->mainGuestFirstName = $value;
        }

        public function getMainGuestLasName()
        {
                return $this->mainGuestLasName;
        }

        public function setMainGuestLasName($value)
        {
                $this->mainGuestLasName = $value;
        }

        public function getMainGuestEmail()
        {
                return $this->mainGuestEmail;
        }

        public function setMainGuestEmail($value)
        {
                $this->mainGuestEmail = $value;
        }

        public function getNumberOfAdults()
        {
                return intval($this->numberOfAdults);
        }

        public function setNumberOfAdults($value)
        {
                $this->numberOfAdults = $value;
        }

        public function getNumberOfChildren()
        {
                return $this->numberOfChildren;
        }

        public function setNumberOfChildren($value)
        {
                $this->numberOfChildren = $value;
        }

        public function getPoliciesCheckInOutCheckIn()
        {
                return $this->policiesCheckInOutCheckIn;
        }

        public function setPoliciesCheckInOutCheckIn($value)
        {
                $this->policiesCheckInOutCheckIn = $value;
        }

        public function getPoliciesCheckInOutCheckOut()
        {
                return $this->policiesCheckInOutCheckOut;
        }

        public function setPoliciesCheckInOutCheckOut($value)
        {
                $this->policiesCheckInOutCheckOut = $value;
        }

        public function getPoliciesCancellationDeadline()
        {
                return $this->policiesCancellationDeadline;
        }

        public function setPoliciesCancellationDeadline($value)
        {
                $this->policiesCancellationDeadline = $value;
        }

        public function getDescription()
        {
                return $this->description;
        }

        public function setDescription($value)
        {
                if (is_array($value)) $value = json_encode($value);
                $this->description = $value;
        }

        public function getCreatedAt()
        {
                return $this->created_at;
        }

        public function getUpdatedAt()
        {
                return $this->updated_at;
        }

        public function getPaymentInfoId()
        {
                return $this->paymentInfoId;
        }

        public function setPaymentInfoId($value)
        {
                $this->paymentInfoId = $value;
        }
}
