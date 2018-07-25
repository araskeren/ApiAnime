<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Produser;
use App\SeasonProduser;
use App\Anime;
use App\Season;
use Illuminate\Support\Facades\Auth;
class ProduserController extends Controller
{
  public function __construct(){
    $this->middleware('auth:api',['only'=>[
      'store',
      'update',
      'delete',
      'showDelete',
      'indexDelete',
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
      $produser=Produser::select('id','nama','slug')->get();
      foreach($produser as $i){
        $i->view_produser=[
          'href'=>'/api/v1/produser/'.$i->slug,
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
      $produser= Produser::onlyTrashed()->select('id','nama','slug')->get();
      if($produser!=null){
        foreach($produser as $i){
          $i->view_produser=[
            'href'=>'/api/v1/produser/'.$i->slug.'/delete',
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
          'nama'=>$request->produser,
          'slug'=>Str::slug($request->produser)
        ]);
        if($produser->save()){
          $produser->view_produser=[
            'href'=>'/api/v1/produser/'.$produser->slug,
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
    public function show($slug)
    {
      $produser=Produser::select('id','nama','slug')->where('slug',$slug)->first();
      if($produser!=null){
        $pivot_produser=SeasonProduser::select('season_id')->where('produser_id',$produser->id)->get();
        //return isset($pivot_anime);
        if(count($pivot_produser)>0){
          foreach($pivot_produser as $i){
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
        if(count($pivot_produser)>0){
          $response=[
            'message'=>'Detail produser',
            'produser'=>$produser,
            'data'=>$season,
          ];
        }else{
          $response=[
            'message'=>'Detail produser',
            'produser'=>$produser,
            'data'=>[],
          ];
        }
        return response()->json($response,200);
      }
      return response()->json(['Data Tidak ditemukan'],404);
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
    public function update(Request $request, $slug)
    {
        $produser= Produser::select('id','nama','slug')->where('slug',$slug)->first();
        if($produser!=null){
          $produser->nama=$request->produser;
          $produser->slug=Str::slug($request->produser);
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
    public function restore($slug)
    {
      $produser= Produser::onlyTrashed()->select('id','nama')->where('slug',$slug)->first();
      if($produser!=null && $produser->restore()){
        $produser->view_produser=[
          'href'=>'/api/v1/produser/'.$slug,
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
    public function destroy($slug)
    {
        $produser= Produser::select('id','nama')->where('slug',$slug)->first();
        if($produser!=null && $produser->delete()){
          $produser->restore=[
            'href'=>'/api/v1/produser/'.$slug.'/restore',
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
    public function harddestroy($slug)
    {
      $produser= Produser::onlyTrashed()->select('id','nama')->where('slug',$slug)->first();
      if($produser!=null && $produser->forceDelete()){
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
