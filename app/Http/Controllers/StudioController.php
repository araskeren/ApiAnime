<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Studio;
use App\Anime;
use App\Genre;
use App\Season;
use App\AnimeGenre;
use Illuminate\Support\Facades\Auth;
class StudioController extends Controller
{
  public function __construct(){
    $this->middleware('auth:api',['only'=>[
      'store',
      'update',
      'delete',
      'indexDelete',
      'harddestroy',
      'destroy',
      'restore',
      ]]);
      $this->middleware('role',['only'=>[
        'store',
        'update',
        'delete',
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
      $studio=Studio::select('id','nama','slug')->get();
      foreach($studio as $i){
        $i->view_studio=[
          'href'=>'/api/v1/studio/'.$i->slug,
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh studio',
        'data'=>$studio,
      ];
      return response()->json($response,200);

    }

    /**
     * Display all listing of the resource where get delete by softdelete.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDelete(Request $request)
    {
      $user=$request->header('Authorization');
      $studio= Studio::onlyTrashed()->select('id','nama','slug','suka','tidak_suka')->get();
      if($studio!=null){
        foreach($studio as $i){
          $i->restore=[
            'href'=>'/api/v1/studio/'.$i->slug.'/restore',
            'method'=>'GET',
          ];
        }
        $response=[
          'message'=>'Daftar Seluruh studio Yang di Hapus',
          'data'=>$studio
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
        $this->validasi($request);
        $studio=new Studio([
          'user_id'=>Auth::user()->id,
          'nama'=>$request->nama,
          'slug'=>Str::slug($request->nama),
          'suka'=>0,
          'tidak_suka'=>0,
          'total_anime'=>0
        ]);
        if($studio->save()){
          $studio->view_studio=[
            'href'=>'/api/v1/studio/'.$studio->slug,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'studio '.$studio->studio.' Berhasil Di Tambahkan',
            'data'=>$studio
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
      $studio=Studio::select('id','nama','slug','suka','tidak_suka','total_anime')->where('slug',$slug)->first();
      if($studio!=null){
        $season=Season::
        select('id','anime_id','season','slug','durasi','episode','tanggal_tayang','tanggal_end','musim','type','broadcast','cover','sinopsis')
        ->where('studio_id',$studio->id)->get();
        if(count($season)!=$studio->total_anime){
          $studio->update([
            'total_anime'=>count($season)
          ]);
        }
        if(count($season)>0){
          foreach($season as $i){
            $pivot_genre=AnimeGenre::where('anime_id',$i->anime_id)->get();
            $anime=Anime::select('judul','slug','sumber','cover')->where('id',$i->anime_id)->first();
            $i->anime=$anime;
            $genre=null;
            foreach($pivot_genre as $j){
              $datagenre=Genre::select('id','genre')->where('id',$j->genre_id)->first();
              $datagenre->view_genre='/api/v1/genre/'.$datagenre->id;
              $genre[]=$datagenre;
            }

            $i->genre=$genre;
            $i->cover=[
              'href'=>'/api/v1/anime/'.$i->id.'/cover',
              'method'=>'GET',
            ];
            $i->view_anime=[
              'href'=>'/api/v1/anime/'.$i->id,
              'method'=>'GET',
            ];
          }
        }
        $response=[
          'message'=>'Detail studio',
          'studio'=>$studio,
          'data'=>$season,
        ];
        return response()->json($response,200);
      }
      return response()->json('Telah Terjadi Kesalahan!',404);
    }

    /**
    *Show Detail Delete with soft delete
    */
    public function showDelete($id)
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
    public function update(Request $request, $slug)
    {
        $this->validasiUpdate($request);
        $user=$request->header('Authorization');
        $studio= Studio::select('id','nama','slug','suka','tidak_suka')->where('slug',$slug)->first();
        if($studio!=null){
          if($request->has('nama')){
            $studio->nama=$request->nama;
            $studio->slug=Str::slug($request->nama);
          }
          if($request->has('suka')){
            $studio->suka=$request->suka;
          }
          if($request->has('tidak_suka')){
            $studio->tidak_suka=$request->tidak_suka;
          }
          if($studio->update()){
            $studio->view_studio=[
              'href'=>'/api/v1/studio/'.$studio->id,
              'method'=>'GET',
            ];
            $response=[
              'message'=>'Studio '.$studio->nama.' Berhasil di Ubah',
              'data'=>$studio
            ];
            return response()->json($response,201);
          }
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
    public function restore(Request $request,$slug)
    {
      $studio=Studio::onlyTrashed()->select('id','nama')->where('slug',$slug)->first();
      if($studio!=null){
        if($studio->restore()){
          $studio->view_studio=[
            'href'=>'/api/v1/studio/'.$studio->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'studio '.$studio->nama.' Berhasil di Restore',
            'data'=>$studio
          ];
          return response()->json($response,201);
        }
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
    public function destroy(Request $request,$slug)
    {
        $user=$request->header('Authorization');
        $studio= Studio::select('id','nama')->where('slug',$slug)->first();
        if($studio!=null){
          if($studio->delete()){
            $studio->restore=[
              'href'=>'/api/v1/studio/'.$studio->id.'/restore',
              'method'=>'GET',
            ];
            $response=[
              'message'=>'studio '.$studio->studio.' Berhasil di Hapus',
              'data'=>$studio
            ];
            return response()->json($response,201);
          }
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
      $studio= Studio::onlyTrashed()->select('id','nama')->where('slug',$slug)->first();
      if($studio!=null){
        if($studio->forceDelete()){
          $studio->create=[
            'href'=>'/api/v1/studio/',
            'param'=>[
              'studio'=>'text'
            ],
            'method'=>'POST',
          ];
          $response=[
            'message'=>'studio '.$studio->studio.' Berhasil di Hapus Secara Permanent',
            'data'=>$studio
          ];
          return response()->json($response,201);
        }
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }

    private function validasi($request){
      return $this->validate($request, [
        'nama'=>'required|max:15',
        'user_id'=>'integer|min:1',
        'suka'=>'integer|min:0',
        'tidak_suka'=>'integer|min:0',
        'total_anime'=>'integer|min:1',
      ]);
    }
    private function validasiUpdate($request){
      return $this->validate($request, [
        'nama'=>'string|max:15',
        'user_id'=>'integer|min:1',
        'suka'=>'integer|min:0',
        'tidak_suka'=>'integer|min:0',
        'total_anime'=>'integer|min:1',
      ]);
    }
}
