<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Genre;
use App\AnimeGenre;
use App\Anime;
class GenreController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $genre=Genre::select('id','genre')->get();
      foreach($genre as $i){
        $i->view_genre=[
          'href'=>'/api/v1/genre/'.$i->id,
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh Genre',
        'data'=>$genre,
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
      $genre= Genre::onlyTrashed()->select('id','genre')->get();
      if($genre!=null){
        foreach($genre as $i){
          $i->view_genre=[
            'href'=>'/api/v1/genre/'.$i->id.'/delete',
            'method'=>'GET',
          ];
        }
        $response=[
          'message'=>'Daftar Seluruh Genre Yang di Hapus',
          'data'=>$genre
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
        $genre= new Genre([
          'genre'=>$request->genre
        ]);
        if($genre->save()){
          $genre->view_genre=[
            'href'=>'/api/v1/genre/'.$genre->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'Genre '.$genre->genre.' Berhasil Di Tambahkan',
            'data'=>$genre
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
      $genre=Genre::select('id','genre')->where('id',$id)->first();
      $pivot_anime=AnimeGenre::select('anime_id')->where('genre_id',$id)->get();
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
          'message'=>'Detail Genre',
          'data'=>$genre,
          'anime'=>$anime,
        ];
      }else{
        $response=[
          'message'=>'Detail Genre',
          'data'=>$genre,
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
      $genre= Genre::onlyTrashed()->select('id','genre')->where('id',$id)->first();
      if($genre!=null){
        $genre->restore=[
          'href'=>'/api/v1/genre/'.$genre->id.'/restore',
          'method'=>'GET',
        ];
        $response=[
          'message'=>'Detail Genre',
          'data'=>$genre
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
        $genre= Genre::select('id','genre')->where('id',$id)->first();
        $genre->genre=$request->genre;
        if($genre->update()){
          $genre->view_genre=[
            'href'=>'/api/v1/genre/'.$genre->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'Genre Berhasil di ubah ke '.$genre->genre,
            'data'=>$genre
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
      $genre= Genre::onlyTrashed()->select('id','genre')->where('id',$id)->first();
      if($genre->restore()){
        $genre->view_genre=[
          'href'=>'/api/v1/genre/'.$genre->id,
          'method'=>'GET',
        ];
        $response=[
          'message'=>'Genre '.$genre->genre.' Berhasil di Restore',
          'data'=>$genre
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
        $genre= Genre::select('id','genre')->where('id',$id)->first();
        if($genre->delete()){
          $genre->restore=[
            'href'=>'/api/v1/genre/'.$genre->id.'/restore',
            'method'=>'GET',
          ];
          $response=[
            'message'=>'Genre '.$genre->genre.' Berhasil di Hapus',
            'data'=>$genre
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
      $genre= Genre::select('id','genre')->where('id',$id)->first();
      if($genre->forceDelete()){
        $genre->create=[
          'href'=>'/api/v1/genre/',
          'param'=>[
            'genre'=>'text'
          ],
          'method'=>'POST',
        ];
        $response=[
          'message'=>'Genre '.$genre->genre.' Berhasil di Hapus Secara Permanent',
          'data'=>$genre
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }
}
