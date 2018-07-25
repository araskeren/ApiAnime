<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Licensor;
use App\SeasonLicensor;
use App\Anime;
use App\Season;
use Illuminate\Support\Facades\Auth;
class LicensorController extends Controller
{
  public function __construct(){
    $this->middleware('auth:api',['only'=>[
      'store',
      'update',
      'delete',
      'indexDelete',
      'showDelete',
      'harddestroy',
      'destroy',
      'restore',
    ]]);
    $this->middleware('role',['only'=>[
      'store',
      'update',
      'delete',
      'showDelete',
      'indexDelete',
      'harddestroy',
      'destroy',
      'restore',
    ]]);
  }
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $licensor=Licensor::select('id','nama','slug')->get();
      foreach($licensor as $i){
        $i->view_licensor=[
          'href'=>'/api/v1/licensor/'.$i->slug,
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
      $licensor= Licensor::onlyTrashed()->select('id','nama','slug')->get();
      if($licensor!=null){
        foreach($licensor as $i){
          $i->view_licensor=[
            'href'=>'/api/v1/licensor/'.$i->slug.'/delete',
            'method'=>'GET',
          ];
          $i->restore=[
            'href'=>'/api/v1/licensor/'.$i->slug.'/restore',
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
          'nama'=>$request->licensor,
          'slug'=>Str::slug($request->licensor)
        ]);
        if($licensor->save()){
          $licensor->view_licensor=[
            'href'=>'/api/v1/licensor/'.$licensor->slug,
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
    public function show($slug)
    {
      $licensor=Licensor::select('id','nama','slug')->where('slug',$slug)->first();
      if($licensor!=null){
        $pivot_licensor=SeasonLicensor::select('season_id')->where('licensor_id',$licensor->id)->get();
        //return isset($pivot_anime);
        if(count($pivot_licensor)>0){
          foreach($pivot_licensor as $i){
            $season[]=Season::
            select('id','anime_id','season','slug','durasi','episode','tanggal_tayang','tanggal_end','musim','type','broadcast','cover','sinopsis')
            ->where('id',$i->season_id)->first();
          }
          foreach($season as $i){
            $anime=Anime::select('judul','slug','sumber','cover')->where('id',$i->anime_id)->first();
            $anime->cover='/api/v1/'.$anime->slug.'/cover';
            $i->anime=$anime;
          }
        }
        if(count($pivot_licensor)>0){
          $response=[
            'message'=>'Detail licensor',
            'licensor'=>$licensor,
            'data'=>$season,
          ];
        }else{
          $response=[
            'message'=>'Detail licensor',
            'licensor'=>$licensor,
            'data'=>[],
          ];
        }
        return response()->json($response,200);
      }
      return response()->json('Data Tidak Ditemukan!',404);
    }

    /**
    *Show Detail Delete with soft delete
    */
    public function showDelete($slug)
    {
      $licensor= Licensor::onlyTrashed()->select('id','nama','slug')->where('slug',$slug)->first();
      if($licensor!=null){
        $licensor->restore=[
          'href'=>'/api/v1/licensor/'.$slug.'/restore',
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
    public function update(Request $request, $slug)
    {
        $licensor= Licensor::select('id','nama')->where('slug',$slug)->first();
        if($licensor!=null){
          $licensor->nama=$request->licensor;
          $licensor->slug=Str::slug($request->licensor);
          if($licensor->update()){
            $licensor->view_licensor=[
              'href'=>'/api/v1/licensor/'.$licensor->slug,
              'method'=>'GET',
            ];
            $response=[
              'message'=>'licensor Berhasil di ubah ke '.$licensor->nama,
              'data'=>$licensor
            ];
            return response()->json($response,201);
          }

        }
        return response()->json(['message'=>'Telah terjadi kesalahan'],404);
    }

    /**
     * Restore the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($slug)
    {
      $licensor= Licensor::onlyTrashed()->select('id','nama')->where('slug',$slug)->first();
      if($licensor!=null && $licensor->restore()){
        $licensor->view_licensor=[
          'href'=>'/api/v1/licensor/'.$slug,
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
    public function destroy($slug)
    {
        $licensor= Licensor::select('id','nama')->where('slug',$slug)->first();
        if($licensor!=null && $licensor->delete()){
          $licensor->restore=[
            'href'=>'/api/v1/licensor/'.$slug.'/restore',
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
    public function harddestroy($slug)
    {
      $licensor= Licensor::onlyTrashed()->select('id','nama')->where('slug',$slug)->first();
      if($licensor!=null&&$licensor->forceDelete()){
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
