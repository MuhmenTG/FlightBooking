<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
        
	protected $table = 'payments';
	protected $primaryKey = 'id';
//	protected $guarded = [];
//	protected $fillable = [];

	const COL_ID = 'id';
	const COL_TRANSACTIONDATE = 'transactionDate';
	const COL_PAYMENTAMOUNT = 'paymentAmount';
	const COL_PAYMENTCURRENCY = 'paymentCurrency';
	const COL_PAYMENTTYPE = 'paymentType';
	const COL_PAYMENTSTATUS = 'paymentStatus';
	const COL_PAYMENTTRANSACTIONID = 'paymentTransactionId';
	const COL_PAYMENTMETHOD = 'paymentMethod';
	const COL_PAYMENTGATEWAYPROCESSOR = 'paymentGatewayProcessor';
	const COL_CONNECTEDBOOKINGREFERENCE = 'connectedBookingReference';
	const COL_CREATED_AT = 'created_at';
	const COL_UPDATED_AT = 'updated_at';

	/*
	 * Eloquent Scopes
	 */

	public function scopeById($query, $val) {
		$query->where('id', $val);
	}

        
	public function scopeByConnectedBookingReference($query, $val) {
		$query->where('connectedBookingReference', $val);
	}

	public function scopeByPaymentTransactionId($query, $val) {
		$query->where('paymentTransactionId', $val);
	}

	/*
	 * GET / SET
	 */

	public function getPaymentId() {
		return $this->id;
	}

	public function getTransactionDate() {
		return $this->transactionDate;
	}

	public function setTransactionDate($value) {
		$this->transactionDate = $value;
	}

	public function getPaymentAmount() {
		return $this->paymentAmount;
	}

	public function setPaymentAmount($value) {
		$this->paymentAmount = $value;
	}

	public function getPaymentCurrency() {
		return $this->paymentCurrency;
	}

	public function setPaymentCurrency($value) {
		$this->paymentCurrency = $value;
	}

	public function getPaymentType() {
		return $this->paymentType;
	}

	public function setPaymentType($value) {
		$this->paymentType = $value;
	}

	public function getPaymentStatus() {
		return $this->paymentStatus;
	}

	public function setPaymentStatus($value) {
		$this->paymentStatus = $value;
	}

	public function getPaymentTransactionId() {
		return $this->paymentTransactionId;
	}

	public function setPaymentTransactionId($value) {
		$this->paymentTransactionId = $value;
	}

	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	public function setPaymentMethod($value) {
		$this->paymentMethod = $value;
	}

	public function getPaymentGatewayProcessor() {
		return $this->paymentGatewayProcessor;
	}

	public function setPaymentGatewayProcessor($value) {
		$this->paymentGatewayProcessor = $value;
	}

	public function getConnectedBookingReference() {
		return $this->connectedBookingReference;
	}

	public function setConnectedBookingReference($value) {
		if (is_array($value)) $value = json_encode($value);
		$this->connectedBookingReference = $value;
	}

	public function getCreatedAt() {
		return $this->created_at;
	}

	public function getUpdatedAt() {
		return $this->updated_at;
	}


}
