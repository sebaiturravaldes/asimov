<?php

namespace App\Providers;

use DB;
use DateTime;
use Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Use custom validation to demonstrate that the framework does not limit the knowledge base in php.
        Validator::extend('date_week', function ($attribute, $value, $parameters, $validator) {
            //Parse datetime into a Unix timestamp
            $value = strtotime($value);

            //Format a local time/date with 'w' format
            // 'w' is Numeric representation of the day of the week
            // 0 (for Sunday) through 6 (for Saturday)
            $value = date('w', $value);


            // Returns true if it corresponds to a weekday otherwise false. 
            return ($value > 0 && $value < 6);
        });

 
        /**
         * Multiple validate unique in one table.
         */
        Validator::extend('unique_with', function ($attribute, $value, $parameters) {
            $result = DB::table($parameters[0])->where($attribute, $value)->where($parameters[1], $parameters[2])->count();

            return $result === 0;
        });

        /**
         * Validate that the datetime of the appointment is greater than the current datetime.
         */
        Validator::extend('datetime_appointment', function ($attribute, $value, $parameters, $validator) {
            $current_datetime = new DateTime('now');            
            $selected_date    = new DateTime($parameters[0]);        
            $selected_date    = $selected_date->format('Y-m-d');

            if($current_datetime->format('Y-m-d') == $selected_date){
                $selected_time = new DateTime($value);
                return ($current_datetime->format('H') < $selected_time->format('H'));
            }

            return ($current_datetime->format('Y-m-d') < $selected_date);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
