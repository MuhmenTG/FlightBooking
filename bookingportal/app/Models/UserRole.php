<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;
    
    protected $table = 'user_roles';
    protected $primaryKey = 'id';
//      protected $guarded = [];
//      protected $fillable = [];

    const COL_ID = 'id';
    const COL_ROLENAME = 'roleName';
    const COL_ROLECODE = 'roleCode';
    const COL_ROLEDESCRIPTION = 'roleDescription';
    const COL_CREATED_AT = 'created_at';
    const COL_UPDATED_AT = 'updated_at';

    /*
     * Eloquent Scopes
     */

    public function scopeById($query, $val) {
            $query->where('id', $val);
    }

    /*
     * GET / SET
     */

    public function getUserRoleId() {
            return $this->id;
    }

    public function getRoleName() {
            return $this->roleName;
    }

    public function setRoleName($value) {
            $this->roleName = $value;
    }

    public function getRoleCode() {
            return $this->roleCode;
    }

    public function setRoleCode($value) {
            $this->roleCode = $value;
    }

    public function getRoleDescription() {
            return $this->roleDescription;
    }

    public function setRoleDescription($value) {
            $this->roleDescription = $value;
    }

    public function getCreatedAt() {
            return $this->created_at;
    }

    public function getUpdatedAt() {
            return $this->updated_at;
    }

}
