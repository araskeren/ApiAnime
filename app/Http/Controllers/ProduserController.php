<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Produser;
use App\AnimeProduser;
use App\Anime;
class ProduserController extends Controller
{
    /**
     * Display all listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $produser=Produser::select('id','nama')->get();
      foreach($produser as $i){
        $i->view_produser=[
          'href'=>'/api/v1/produser/'.$i->id,
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh produser',
        'data'=>$produser,
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
      $produser= Produser::onlyTrashed()->select('id','nama')->get();
      if($produser!=null){
        foreach($produser as $i){
          $i->view_produser=[
            'href'=>'/api/v1/produser/'.$i->id.'/delete',
            'method'=>'GET',
          ];
        }
        $response=[
          'message'=>'Daftar Seluruh produser Yang di Hapus',
          'data'=>$produser
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
        $produser= new Produser([
          'nama'=>$request->produser
        ]);
        if($produser->save()){
          $produser->view_produser=[
            'href'=>'/api/v1/produser/'.$produser->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'produser '.$produser->produser.' Berhasil Di Tambahkan',
            'data'=>$produser
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
      $produser=Produser::select('id','nama')->where('id',$id)->first();
      $pivot_anime=AnimeProduser::select('anime_id')->where('produser_id',$id)->get();
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
          'message'=>'Detail produser',
          'data'=>$produser,
          'anime'=>$anime,
        ];
      }else{
        $response=[
          'message'=>'Detail produser',
          'data'=>$produser,
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
      $produser= Produser::onlyTrashed()->select('id','nama')->where('id',$id)->first();
      if($produser!=null){
        $produser->restore=[
          'href'=>'/api/v1/produser/'.$produser->id.'/restore',
          'method'=>'GET',
        ];
        $response=[
          'message'=>'Detail produser',
          'data'=>$produser
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
        $produser= Produser::select('id','nama')->where('id',$id)->first();
        $produser->nama=$request->produser;
        if($produser->update()){
          $produser->view_produser=[
            'href'=>'/api/v1/produser/'.$produser->id,
            'method'=>'GET',
          ];
          $response=[
            'message'=>'produser Berhasil di ubah ke '.$produser->produser,
            'data'=>$produser
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
      $produser= Produser::onlyTrashed()->select('id','nama')->where('id',$id)->first();
      if($produser->restore()){
        $produser->view_produser=[
          'href'=>'/api/v1/produser/'.$produser->id,
          'method'=>'GET',
        ];
        $response=[
          'message'=>'produser '.$produser->produser.' Berhasil di Restore',
          'data'=>$produser
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
        $produser= Produser::select('id','nama')->where('id',$id)->first();
        if($produser->delete()){
          $produser->restore=[
            'href'=>'/api/v1/produser/'.$produser->id.'/restore',
            'method'=>'GET',
          ];
          $response=[
            'message'=>'produser '.$produser->produser.' Berhasil di Hapus',
            'data'=>$produser
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
      $produser= Produser::onlyTrashed()->select('id','nama')->where('id',$id)->first();
      if($produser->forceDelete()){
        $produser->create=[
          'href'=>'/api/v1/produser/',
          'param'=>[
            'produser'=>'text'
          ],
          'method'=>'POST',
        ];
        $response=[
          'message'=>'produser '.$produser->produser.' Berhasil di Hapus Secara Permanent',
          'data'=>$produser
        ];
        return response()->json($response,201);
      }
      $response=[
        'message'=>'Telah terjadi kesalahan'
      ];
      return response()->json($response,404);
    }
}
