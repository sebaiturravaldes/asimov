<?php

namespace App\Http\Controllers;

use App\Appointment;
use Illuminate\Http\Request;
use Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Get all Appointments for test.
        $data = Appointment::all();
        
        return $data;
        //return response()->json(['response' => $data]);
    }

    /**
     * Store a new appointment.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // $rules
        // date_appointment, required, date format and custom validator date_week.
        // time_appointment, required, Custom validator unique_with, custom validator datetime_appointment, and regex the schedule must be set for office hours (9am to 6pm).
        // email, not null, required, email vwrify and unique in table.
        $rules = [
            'date_appointment' => 'required|date|date_week',
            'time_appointment' => [
                                    'required', 
                                    'unique_with:appointments,date_appointment,'.$request->date_appointment,
                                    'datetime_appointment:'.$request->date_appointment,
                                    'regex:/(09|1[0-8]):00:00/'
                                    ],
            'email'            => 'nullable|required|email|unique:appointments',
        ];

        //Custom message in response.
        $messages = [
            'date_appointment.date_week'            => 'Sorry, Death only works in Monday to Friday.',
            'time_appointment.regex'                => 'Sorry, Death only works in office hour (9am to 18pm).',
            'time_appointment.datetime_appointment' => 'The chosen time is already over.',
            'email.unique'                          => 'The Death does not want anymore appointments with you.',

        ];

        //Validating the information.
        Validator::make($request->all(), $rules, $messages)->validate();
        
        //Instance and Insert in Database
        $data                   = new Appointment;
        $data->date_appointment = $request->date_appointment;
        $data->time_appointment = $request->time_appointment;
        $data->email            = $request->email;
        $data->save();

        return ['message' => 'Appointment created.'];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show($date)
    {
        
        $data = Appointment::select('time_appointment')->where('date_appointment', $date)->orderBy('time_appointment', 'asc')->get();
        
        return $data;
    }
}
