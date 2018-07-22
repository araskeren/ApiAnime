<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Playlist;
use App\Episode;
use App\Anime;
class PlaylistController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validasi($request);
        $user=$request->header('Authorization');
        $playlist= Playlist::select('id','episode')->where('user',$user)->get();
        foreach($playlist as $i){
          $episode=Episode::select('id','')
        }
        $response=[
          'message'=>'Daftar playlist',
          'data'=>$playlist
        ];
        return response()->json($response,201);
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
        $user=$request->header('Authorization');
        $playlist= new Playlist([
          'user'=>$user,
          'episode'=>$request->episode,
        ]);
        if($playlist->save()){
          $playlist->view_playlist=[
            'href'=>'/api/v1/playlist/',
            'method'=>'GET',
          ];
          $response=[
            'message'=>'Berhasil di tambahkan ke playlist',
            'data'=>$playlist
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
        'episode'=>'integer|min:0',
      ]);
    }
}
