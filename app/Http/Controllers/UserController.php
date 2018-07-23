<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Display all listing of the resource where get delete by softdelete.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDelete(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validasi($request);
        $user=new User();
        $user->nama=$request->nama;
        $user->username=$request->username;
        $user->email=$request->email;
        $user->password=app('hash')->make($request->password);
        $user->level=9;
        if($user->save()){
          $param=[
            'grant_type'=>'password',
            'client_id'=>'2',
            'client_secret'=>'0osZa2yn03OQrAAReMObur4Sx9mDgRByl66q9o2n',
            'username'=>'user@mail.com',
            'password'=>'userpassword'
          ];
          $response=[
            'message'=>'Berhasil Registrasi',
            'data'=>[
              'login'=>'/oauth/token',
              'method'=>'post',
              'param'=>$param,
            ],
          ];
          return response()->json($response,201);
        }

        $response=[
          'message'=>'Telah terjadi kesalahan'
        ];
        return response()->json($response,404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
    }

    /**
    *Show Detail Delete with soft delete
    */
    public function showDelete(Request $request,$id)
    {
        //
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
        //
    }

    /**
     * Restore the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request,$id)
    {
        //
    }

    /**
     * Remove the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        //
    }

    /**
     * Remove the specified resource from storage with hard delete *Permanent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function harddestroy(Request $request,$id)
    {
        //
    }

    private function validasi($request){
      return $this->validate($request, [
        'username'=>'required|required_without:space|max:50',
        'nama'=>'required|max:50',
        'email'=>'unique:users|required|email|max:50',
        'password'=>'required|max:50',
        'confirm_password' => 'required|same:password',
        'level'=>'integer|min:1',
      ]);
    }

    private function validasiUpdate($request){
      return $this->validate($request, [
        'required'=>'required|max:255',
        'angka'=>'integer|min:1',
        'date'=>'date_format:d-m-Y',
        'string'=>'alpha|max:15',
        'array'=>'array|array|min:1',
        'image'=>'image',
      ]);
    }
}
