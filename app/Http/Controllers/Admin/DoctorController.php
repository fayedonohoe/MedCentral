<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Doctor; //may just be use Doctor
use App\User;
use App\Role;

class DoctorController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
      $this->middleware('role:admin');
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $doctors = Doctor::all()->paginate(10);

        return view('admin.doctors.index')->with([
          'doctors' => $doctors
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // vid 2, 23:35
        return view('admin.doctors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
          'fname' => 'required|max:191',
          'lname' => 'required|max:191',
          'eircode' => 'required|alpha_num|size:7',
          'num' => 'required|size:10',
          'email' => 'required|max:191',
          'password' => 'required|max:191',
        ]);

        $user = new User();
        $user->firstName = $request->input('fname');
        $user->lastName = $request->input('lname');
        $user->eircode = $request->input('eircode');
        $user->phoneNumber = $request->input('num');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password') );

        $user->save();

        $doctor = new Doctor();
        $doctor->startofEmployment = $request->input('started');
        $doctor->user_id = $user->id;
        $doctor->save();


        // $newrole = User
        return redirect()->route('admin.doctors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $doctor = Doctor::findOrFail($id);

        return view('admin.doctors.show')->with([
          'doctor' => $doctor
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $doctor = Doctor::findOrFail($id);

      return view('admin.doctors.edit')->with([
        'doctor' => $doctor
      ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $doctor = Doctor::findOrFail($id);

      $request->validate([
        'fname' => 'required|max:191',
        'lname' => 'required|max:191',
        'eircode' => 'required|alpha_num|size:7',
        'num' => 'required|size:10',
        'email' => 'required|max:191|unique:users,email,'.$doctor->id,
        'password' => 'required|max:191',
      ]);

      $doctor = new User();
      $doctor->firstName = $request->input('fname');
      $doctor->lastName = $request->input('lname');
      $doctor->eircode = $request->input('eircode');
      $doctor->phoneNumber = $request->input('num');
      $doctor->email = $request->input('email');
      $doctor->password = $request->input('password');

      $doctor->update();

      return redirect()->route('admin.doctors.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $doctor = Doctor::findOrFail($id);

      $doctor->delete();

      return redirect()->route('admin.doctors.index');
    }
}
