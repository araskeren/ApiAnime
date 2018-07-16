<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Subscribe;
use App\Anime;
class SubscribeController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //user sesuai dengan token
        $user=$request->header('Authorization');
        $subscribe=Subscribe::select('id','anime')->where('user',$user)->get();
        foreach($subscribe as $i){
          $anime=Anime::select('id','judul')->where('id',$i->anime)->first();
          $i->anime=$anime;
          $i->cover=[
            'href'=>'/api/v1/anime/'.$anime->id.'/cover',
            'method'=>'GET',
          ];
        }
        $response=[
          'message'=>'Daftar Subscribe',
          'data'=>$subscribe,
        ];
        return $response;
    }

    /**
     * Display all listing of the resource where get delete by softdelete.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDelete()
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
        $user=$request->header('Authorization');
        $subscribe= new Subscribe([
          'user'=>$user,
          'anime'=>$request->anime,
        ]);
        if($subscribe->save()){
          $subscribe->view_history=[
            'href'=>'/api/v1/history/',
            'method'=>'GET',
          ];
          $anime=Anime::select('judul')->where('id',$subscribe->anime)->first();
          $subscribe->anime=$anime;
          $response=[
            'message'=>'Berhasil Subscribe Anime '.$anime->judul,
            'data'=>$subscribe
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
    public function show($id){
        //
    }

    /**
    *Show Detail Delete with soft delete
    */
    public function showDelete($id){
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
    public function restore($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    /**
     * Remove the specified resource from storage with hard delete *Permanent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function harddestroy($id)
    {
      $subscribe= Subscribe::select('id')->where('id',$id)->first();
      if($subscribe->forceDelete()){
        $subscribe->create=[
          'href'=>'/api/v1/subscribe/',
          'param'=>[
            'anime'=>'integer|required'
          ],
          'method'=>'POST',
        ];
        $response=[
          'message'=>'Berhasil Unsubscribe',
          'data'=>$subscribe
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }
    //Validasi Inputan
    private function validasi($request){
      return $this->validate($request, [
        'anime'=>'required|integer|min:1',
      ]);
    }
}
