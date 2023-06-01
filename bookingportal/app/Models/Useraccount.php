<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class UserAccount extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use HasFactory;
    protected $table = 'user_accounts';
        protected $primaryKey = 'id';
//      protected $guarded = [];
//      protected $fillable = [];

        const COL_ID = 'id';
        const COL_FIRSTNAME = 'firstName';
        const COL_LASTNAME = 'lastName';
        const COL_EMAIL = 'email';
        const COL_PASSWORD = 'password';
        const COL_EMAILCONFIRMATION = 'emailConfirmation';
        const COL_STATUS = 'status';
        const COL_ISAGENT = 'isAgent';
        const COL_ISADMIN = 'isAdmin';
        const COL_FIRSTTIMELOGGEDIN = 'firstTimeLoggedIn';
        const COL_REGISTEREDAT = 'registeredAt';
        const COL_DEACTIVATEDAT = 'deactivatedAt';
        const COL_CREATED_AT = 'created_at';
        const COL_UPDATED_AT = 'updated_at';

        /*
         * Eloquent Scopes
         */

        public function scopeById($query, $val) {
                $query->where('id', $val);
        }

        public function scopeByStatus($query, $val) {
                $query->where('status', $val);
        }
    
        public function scopeByEmail($query, $val) {
                $query->where('email', $val);
        }

        /*
         * GET / SET
         */

        public function getUserAccountId() {
                return $this->id;
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

        public function getEmail() {
                return $this->email;
        }

        public function setEmail($value) {
                $this->email = $value;
        }

        public function getPassword() {
                return $this->password;
        }

        public function setPassword($value) {
                $this->password = $value;
        }

        public function getEmailConfirmation() {
                return $this->emailConfirmation;
        }

        public function setEmailConfirmation($value) {
                $this->emailConfirmation = $value;
        }

        public function getStatus() {
                return $this->status;
        }

        public function setStatus($value) {
                $this->status = $value;
        }

        public function getIsAgent() {
                return $this->isAgent;
        }

        public function setIsAgent($value) {
                $this->isAgent = $value;
        }

        public function getIsAdmin() {
                return $this->isAdmin;
        }

        public function setIsAdmin($value) {
                $this->isAdmin = $value;
        }

        public function getFirstTimeLoggedIn() {
                return intval($this->firstTimeLoggedIn);
        }

        public function setFirstTimeLoggedIn($value) {
                $this->firstTimeLoggedIn = $value;
        }

        public function getRegisteredAt() {
                return $this->registeredAt;
        }

        public function setRegisteredAt($value) {
                $this->registeredAt = $value;
        }

        public function getDeactivatedAt() {
                return intval($this->deactivatedAt);
        }

        public function setDeactivatedAt($value) {
                $this->deactivatedAt = $value;
        }

        public function getCreatedAt() {
                return $this->created_at;
        }

        public function getUpdatedAt() {
                return $this->updated_at;
        }

}
