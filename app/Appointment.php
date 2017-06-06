<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
	/**
	 * Not used timestemps, set false.
	 */

	public $timestamps = false;

    /**
	* The attributes that are mass assignable.
	*
	* @var array
	*/
	protected $fillable = [
	'date_appointment', 'time_appointment', 'email'
	];
}
