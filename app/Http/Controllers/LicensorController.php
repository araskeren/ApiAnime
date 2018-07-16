<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Licensor;
use App\AnimeLicensor;
use App\Anime;
class LicensorController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $licensor=Licensor::select('id','nama')->get();
      foreach($licensor as $i){
        $i->view_licensor=[
          'href'=>'/api/v1/licensor/'.$i->id,
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh licensor',
        'data'=>$licensor,
      ];
      return response()->json($response,200);

    }

    /**
     * Display all listing of the resource where get delete by softdelete.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDelete()
    {
      $licensor= Licensor::onlyTrashed()->select('id','nama')->get();
      if($licensor!=null){
        foreach($licensor as $i){
          $i->view_licensor=[
            'href'=>'/api/v1/licensor/'.$i->id.'/delete',
            'method'=>'GET',
          ];
        }
        $response=[
          'message'=>'Daftar Seluruh licensor Yang di Hapus',
          'data'=>$licensor
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $licensor= new licensor([
          'nama'=>$request->licensor
        ]);
        if($licensor->save()){
          $licensor->view_licensor=[
            'href'=>'/api/v1/licensor/'.$licensor->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'licensor '.$licensor->licensor.' Berhasil Di Tambahkan',
            'data'=>$licensor
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
    public function show($id)
    {
      $licensor=Licensor::select('id','nama')->where('id',$id)->first();
      $pivot_anime=AnimeLicensor::select('anime_id')->where('licensor_id',$id)->get();
      //return isset($pivot_anime);
      if(count($pivot_anime)>0){
        foreach($pivot_anime as $i){
          $anime[]=Anime::select('id','judul','status','cover')->where('id',$i->anime_id)->first();
        }
        foreach($anime as $i){
          $i->cover='/api/v1/anime/'.$i->id.'/cover';
        }
      }
      if(count($pivot_anime)>0){
        $response=[
          'message'=>'Detail licensor',
          'data'=>$licensor,
          'anime'=>$anime,
        ];
      }else{
        $response=[
          'message'=>'Detail licensor',
          'data'=>$licensor,
          'anime'=>null,
        ];
      }
      return response()->json($response,200);
    }

    /**
    *Show Detail Delete with soft delete
    */
    public function showDelete($id)
    {
      $licensor= Licensor::onlyTrashed()->select('id','nama')->where('id',$id)->first();
      if($licensor!=null){
        $licensor->restore=[
          'href'=>'/api/v1/licensor/'.$licensor->id.'/restore',
          'method'=>'GET',
        ];
        $response=[
          'message'=>'Detail licensor',
          'data'=>$licensor
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
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
        $licensor= Licensor::select('id','nama')->where('id',$id)->first();
        $licensor->nama=$request->licensor;
        if($licensor->update()){
          $licensor->view_licensor=[
            'href'=>'/api/v1/licensor/'.$licensor->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'licensor Berhasil di ubah ke '.$licensor->licensor,
            'data'=>$licensor
          ];
          return response()->json($response,201);
        }
        $response=[
          'message'=>'Telah terjadi kesalahan'
        ];
        return response()->json($response,404);
    }

    /**
     * Restore the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id)
    {
      $licensor= Licensor::onlyTrashed()->select('id','nama')->where('id',$id)->first();
      if($licensor->restore()){
        $licensor->view_licensor=[
          'href'=>'/api/v1/licensor/'.$licensor->id,
          'method'=>'GET',
        ];
        $response=[
          'message'=>'licensor '.$licensor->licensor.' Berhasil di Restore',
          'data'=>$licensor
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }

    /**
     * Remove the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $licensor= Licensor::select('id','nama')->where('id',$id)->first();
        if($licensor->delete()){
          $licensor->restore=[
            'href'=>'/api/v1/licensor/'.$licensor->id.'/restore',
            'method'=>'GET',
          ];
          $response=[
            'message'=>'licensor '.$licensor->licensor.' Berhasil di Hapus',
            'data'=>$licensor
          ];
          return response()->json($response,201);
        }
        $response=[
          'message'=>'Telah terjadi kesalahan'
        ];
        return response()->json($response,404);
    }

    /**
     * Remove the specified resource from storage with hard delete *Permanent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function harddestroy($id)
    {
      $licensor= Licensor::onlyTrashed()->select('id','nama')->where('id',$id)->first();
      if($licensor->forceDelete()){
        $licensor->create=[
          'href'=>'/api/v1/licensor/',
          'param'=>[
            'licensor'=>'text'
          ],
          'method'=>'POST',
        ];
        $response=[
          'message'=>'licensor '.$licensor->licensor.' Berhasil di Hapus Secara Permanent',
          'data'=>$licensor
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }
}
