<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Anime;
use App\Episode;
use Carbon\Carbon;

class EpisodeController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $episode=Episode::where('anime',$id)->select('anime','episode','cover','keterangan')->get();
        foreach($episode as $i){
          $i->anime=Anime::findOrFail($id)->judul;
          $i->view_episode=[
            'href'=>'/api/v1/anime/'.$id.'/episode/'.$i->episode,
            'method'=>'GET',
          ];
          $i->cover=[
            'href'=>'/api/v1/anime/'.$id.'/episode/'.$i->episode.'/cover',
            'method'=>'GET',
          ];
        }
        $response=[
          'message'=>'Daftar Seluruh Episode',
          'data'=>$episode,
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
      $episode=Episode::onlyTrashed()->select('anime','episode','cover')->get();
      foreach($episode as $i){
        $i->genre=$i->Genre;
        $i->view_deleted_episode=[
          'href'=>'/api/v1/anime/'.$i->anime.'/episode/'.$i->episode.'/delete',
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh Anime Yang Di Hapus',
        'data'=>$episode,
      ];
      return response()->json($response,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($id,Request $request)
    {
      $this->validasi($request);
      $judul=str_replace(' ', '_',Anime::select('judul')->where('id',$id)->first()->judul);
      $namafile=null;
      if($request->has('cover')){
        $namafile=$judul.'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
        $request->file('cover')->move(storage_path('cover_episode/'.$judul.'/'),$namafile);
      }
      $episode = new Episode([
        'user'=>$request->user_id,
        'anime'=>$id,
        'episode'=>$request->episode,
        'cover'=>$request->has('cover') ?$namafile:null,
        'keterangan'=>$request->has('keterangan') ?$request->keterangan:null,
      ]);

      if($episode->save()){
        $episode->view_anime=[
          'href'=>'/api/v1/anime/episode/'.$episode->id,
          'method'=>'GET',
        ];
        $response=[
          'message'=>'Episode Ke'.$episode->episode.' dengan judul '.$judul.' Berhasil Di Tambahkan',
          'data'=>$episode
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
    public function show($id,$ep)
    {
      $episode=Episode::where('anime',$id)->where('episode',$ep)->get();
      foreach($episode as $i){
        $i->anime=Anime::findOrFail($id)->judul;
        $i->view_episode=[
          'href'=>'/api/v1/anime/'.$id.'/episode/'.$i->episode,
          'method'=>'GET',
        ];
        $i->cover=[
          'href'=>'/api/v1/anime/'.$id.'/episode/'.$i->episode.'/cover',
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Detail Episode',
        'data'=>$episode,
      ];
      return response()->json($response,200);
    }

    /**
    *Show Detail Delete with soft delete
    */
    public function showDelete($id,$ep)
    {
      $episode=Episode::onlyTrashed()->where('anime',$id)->where('episode',$ep)->get();
      foreach($episode as $i){
        $i->anime=Anime::findOrFail($id)->judul;
        $i->restore=[
          'href'=>'/api/v1/anime/'.$id.'/episode/'.$i->episode.'/restore',
          'method'=>'POST',
        ];
        $i->cover=[
          'href'=>'/api/v1/anime/'.$id.'/episode/'.$i->episode.'/cover',
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Detail Episode',
        'data'=>$episode,
      ];
      return response()->json($response,200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id,$ep)
    {
        $this->validasi($request);
        $episode=Episode::where('anime',$id)->where('episode',$ep)->first();
        if($request->has('cover')){
          $judul=str_replace(' ', '_',Anime::select('judul')->where('id',$id)->first()->judul);
          if($episode->cover!=null){
            $image_path = storage_path()."/cover_episode/".$judul."/".$episode->cover;
            if(file_exists($image_path)) {
                unlink($image_path);
            }
          }
          $namafile=$judul.'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
          $request->file('cover')->move(storage_path('cover_episode/'.$judul.'/'),$namafile);
          $episode->cover=$namafile;
        }
        if($request->has('user')){
          $episode->user=$request->user;
        }
        if($request->has('anime')){
          $episode->anime=$request->anime;
        }
        if($request->has('episode')){
          $episode->episode=$request->episode;
        }
        if(!$episode->update()){
          return response()->json(['message'=>'Gagal Mengupdate Data !',404]);
        }
        if($request->has('genre')){
          $anime->Genre()->sync($request->genre);
        }
        $episode->view_anime=[
          'href'=>'/api/v1/anime/'.$id.'/episode/'.$episode->episode,
          'method'=>'GET'
        ];
        $response=[
          'message'=> 'Episode Telah Berhasil di update',
          'episode'=>$episode
        ];
        return response()->json([$response,401]);
    }

    /**
     * Restore the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id,$ep)
    {
      $episode=Episode::onlyTrashed()->where('anime',$id)->where('episode',$ep)->first();
      if(Episode::onlyTrashed()->where('anime',$id)->where('episode',$ep)->restore()){
        $response=[
          'message'=>'Anime Berhasil Direstore',
          'data'=>$episode,
        ];
        return response()->json($response,200);
      }
      $response=[
        'message'=>'Episode Gagal Di Restore',
        'data'=>null
      ];
      return response()->json($response,200);
    }

    /**
     * Remove the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$ep)
    {
        $episode = Episode::where('anime',$id)->where('episode',$ep)->first();
        if(!$episode->delete()){
          return response()->json([
            'message'=>'Prosess Delete Episode gagal'
          ],404);
        }
        $response=[
          'message'=>'Episode berhasil di hapus',
          'create'=>[
            'href'=>'/api/v1/anime'.$id.'/episode',
            'method'=>'POST',
            'params'=>[
              'user_id'=>'required|integer|min:1',
              'episode'=>'required|integer|min:0',
              'cover'=>'image',
              'keterangan'=>'',
            ]
          ]
        ];
        return response()->json($response,200);
        return $episode;

    }

    /**
     * Remove the specified resource from storage with hard delete *Permanent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function harddestroy($id,$ep)
    {
      $episode = Episode::where('anime',$id)->where('episode',$ep)->first();
      if(!$episode->forceDelete()){
        return response()->json([
          'message'=>'Prosess Delete Episode gagal'
        ],404);
      }
      $response=[
        'message'=>'Episode berhasil di hapus',
        'create'=>[
          'href'=>'/api/v1/anime'.$id.'/episode',
          'method'=>'POST',
          'params'=>[
            'user_id'=>'required|integer|min:1',
            'episode'=>'required|integer|min:0',
            'cover'=>'image',
            'keterangan'=>'',
          ]
        ]
      ];
      return response()->json($response,200);
      return $episode;
    }

    public function viewImage($id,$ep){
      $episode=Episode::select('anime','cover')->where('anime',$id)->where('episode',$ep)->first();
      $judul=str_replace(' ', '_',$episode->Anime->judul);
      $cover = storage_path()."/cover_episode/".$judul."/".$episode->cover;
      if (file_exists($cover)) {
        $file = file_get_contents($cover);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
      }
      $res['success'] = false;
      $res['message'] = "Avatar not found";

      return $res;
    }
    //Validasi Inputan
    private function validasi($request){
      return $this->validate($request, [
        'user_id'=>'required|integer|min:1',
        'episode'=>'required|integer|min:0',
        'cover'=>'image',
        'keterangan'=>'',
      ]);
    }


}
