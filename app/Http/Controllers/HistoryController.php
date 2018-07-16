<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\History;
use App\Episode;
use App\Anime;
class HistoryController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //user sesuai dengan token
        $user=1;
        $history=History::select('id','episode')->where('user',$user)->get();
        foreach($history as $i){
          $episode=Episode::select('episode','anime')->where('id',$i->episode)->first();
          $anime=Anime::select('judul','status')->where('id',$episode->anime)->first();
          $i->episode=$episode->episode;
          $i->anime=$anime;
          $i->view_episode=[
            'href'=>'/api/v1/anime/'.$episode->anime.'/episode/'.$episode->episode,
            'method'=>'GET',
          ];
          $i->cover=[
            'href'=>'/api/v1/anime/'.$episode->anime.'/episode/'.$episode->episode.'/cover',
            'method'=>'GET',
          ];
        }
        $response=[
          'message'=>'Daftar History',
          'data'=>$history,
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
        $user=1;
        $history=History::onlyTrashed()->select('id','episode')->where('user',$user)->get();
        foreach($history as $i){
          $episode=Episode::select('episode','anime')->where('id',$i->episode)->first();
          $anime=Anime::select('judul','status')->where('id',$episode->anime)->first();
          $i->episode=$episode->episode;
          $i->anime=$anime;
          $i->view_episode=[
            'href'=>'/api/v1/anime/'.$episode->anime.'/episode/'.$episode->episode,
            'method'=>'GET',
          ];
          $i->cover=[
            'href'=>'/api/v1/anime/'.$episode->anime.'/episode/'.$episode->episode.'/cover',
            'method'=>'GET',
          ];
          $i->restore=[
            'href'=>'/api/v1/history/'.$i->id.'/restore',
            'method'=>'POST',
          ];
        }
        $response=[
          'message'=>'Daftar History Yang di hapus',
          'data'=>$history,
        ];
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $history= new History([
          'user'=>$request->user,
          'episode'=>$request->episode,
        ]);
        if($history->save()){
          $history->view_history=[
            'href'=>'/api/v1/history/',
            'method'=>'GET',
          ];
          $response=[
            'message'=>'History Berhasil Di Tambahkan',
            'data'=>$history
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
        $history= History::select('id','episode')->where('id',$id)->first();
        if($history->delete()){
          $history->restore=[
            'href'=>'/api/v1/history/'.$history->id.'/restore',
            'method'=>'GET',
          ];
          $response=[
            'message'=>'History Berhasil di Hapus',
            'data'=>$history
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
      $history= History::onlyTrashed()->select('id','episode')->where('id',$id)->first();
      if($history->forceDelete()){
        $history->create=[
          'href'=>'/api/v1/history/',
          'param'=>[
            'episode'=>'integer|required'
          ],
          'method'=>'POST',
        ];
        $response=[
          'message'=>'History Berhasil di Hapus Secara Permanent',
          'data'=>$history
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }
}
