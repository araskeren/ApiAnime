<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Komentar;
use App\User;
use App\Anime;
use App\Episode;
class KomentarController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $episode=$request->header('episode');
        $komentar=Komentar::select('episode','user','komentar')->where('episode',$episode)->get();
        foreach($komentar as $i){
          $user=User::select('id','nama')->where('id',$i->user)->first();
          $i->user=$user;
          $i->user->view_profile=[
            'href'=>'/api/v1/user/'.$user->id,
            'method'=>'GET'
          ];
        }
        $response=[
          'message'=>'Daftar Komentar',
          'data'=>$komentar,
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
        $komentar=Komentar::onlyTrashed()->select('id','episode','user','komentar')->orderBy('deleted_at','desc')->get();
        foreach($komentar as $i){
          $user=User::select('id','nama')->where('id',$i->user)->first();
          $episode=Episode::select('anime','episode')->where('id',$i->episode)->first();
          $anime=Anime::select('judul')->where('id',$episode->anime)->first();
          $i->user=$user;
          $i->anime=[
            'judul'=>$anime->judul,
            'episode'=>$episode->episode,
          ];
          $i->user->view_profile=[
            'href'=>'/api/v1/user/'.$user->id,
            'method'=>'GET'
          ];
          $i->restore=[
            'href'=>'/api/v1/komentar/'.$user->id.'/restore',
            'method'=>'GET'
          ];
          $i->delete=[
            'href'=>'/api/v1/komentar/'.$i->id.'/destroy',
            'method'=>'DELETE',
          ];
          unset($i->episode);
        }
        $response=[
          'message'=>'Daftar Komentar yang di hapus',
          'data'=>$komentar,
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
        $episode=$request->header('episode');
        $users=1;
        $komentar= new Komentar([
          'user'=>$users,
          'episode'=>$episode,
          'komentar'=>$request->komentar
        ]);
        if($komentar->save()){
          $komentar->view_komentar=[
            'href'=>'/api/v1/komentar/'.$komentar->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'Komentar Berhasil Di Tambahkan',
            'data'=>$komentar
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
        //
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
        $episode=$request->header('episode');
        $komentar=Komentar::select('id','komentar')->where('id',$id)->first();
        $komentar->komentar=$request->komentar;
        if($komentar->update()){
          $response=[
            'message'=>'Komentar Berhasil Di Update',
            'data'=>$komentar
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
        $komentar= Komentar::onlyTrashed()->select('id')->where('id',$id)->first();
        if($komentar->restore()){
          $response=[
            'message'=>'Komentar Berhasil di Restore',
            'data'=>$komentar
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
        $komentar= Komentar::select('id')->where('id',$id)->first();
        if($komentar->delete()){
          $komentar->restore=[
            'href'=>'/api/v1/komentar/'.$komentar->id.'/restore',
            'method'=>'GET',
          ];
          $komentar->delete=[
            'href'=>'/api/v1/komentar/'.$komentar->id.'/destroy',
            'method'=>'DELETE',
          ];
          $response=[
            'message'=>'Komentar Berhasil di Hapus',
            'data'=>$komentar
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
        $komentar= Komentar::onlyTrashed()->select('id')->where('id',$id)->first();
        if($komentar->forceDelete()){
          $response=[
            'message'=>'Komentar Berhasil di Hapus Permanent',
            'data'=>$komentar
          ];
          return response()->json($response,201);
        }
        $response=[
          'message'=>'Telah terjadi kesalahan'
        ];
        return response()->json($response,404);
    }
}
