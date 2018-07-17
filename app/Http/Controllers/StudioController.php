<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Studio;
use App\Anime;
use App\Genre;
use App\AnimeGenre;
class StudioController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $studio=Studio::select('id','nama')->get();
      foreach($studio as $i){
        $i->view_studio=[
          'href'=>'/api/v1/studio/'.$i->id,
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
      $studio= Studio::onlyTrashed()->select('id','nama','suka','tidak_suka')->get();
      if($studio!=null){
        foreach($studio as $i){
          $i->restore=[
            'href'=>'/api/v1/studio/'.$i->id.'/restore',
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
        $user=$request->header('Authorization');
        $studio=new Studio([
          'user_id'=>$user,
          'nama'=>$request->nama
        ]);
        if($studio->save()){
          $studio->view_studio=[
            'href'=>'/api/v1/studio/'.$studio->id,
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
    public function show($id)
    {
      $studio=Studio::select('id','nama')->where('id',$id)->first();
      $anime=Anime::
      select('id','judul','studio_id','durasi','episode','sumber','musim','type','tanggal_tayang')
      ->where('studio_id',$id)->get();

      if(count($anime)>0){
        foreach($anime as $i){
          $pivot_genre=AnimeGenre::where('anime_id',$i->id)->get();
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
        'data'=>$studio,
        'anime'=>$anime,
      ];
      return response()->json($response,200);
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
    public function update(Request $request, $id)
    {
        $this->validasiUpdate($request);
        $user=$request->header('Authorization');
        $studio= Studio::select('id','nama','suka','tidak_suka')->where('id',$id)->first();
        if($studio!=null){
          if($request->has('nama')){
            $studio->nama=$request->nama;
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
    public function restore(Request $request,$id)
    {
      $user=$request->header('Authorization');
      $studio=Studio::onlyTrashed()->select('id','nama')->where('id',$id)->first();
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
    public function destroy(Request $request,$id)
    {
        $user=$request->header('Authorization');
        $studio= Studio::select('id','nama')->where('id',$id)->first();
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
    public function harddestroy(Request $request,$id)
    {
      $user=$request->header('Authorization');
      $studio= Studio::onlyTrashed()->select('id','nama')->where('id',$id)->first();
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
