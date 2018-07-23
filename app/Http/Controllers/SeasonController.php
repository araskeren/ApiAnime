<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Anime;
use App\Season;
use App\Licensor;
use App\Produser;
use App\SeasonLicensor;
use App\SeasonProduser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class SeasonController extends Controller
{
    public function __construct(){
      $this->middleware('auth:api',['only'=>[
        'store',
        'update',
        'delete',
        'harddestroy',
        'destroy',
        'restore',
        ]]);
        $this->middleware('role',['only'=>[
          'store',
          'update',
          'delete',
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
    public function index(Request $request)
    {
        //
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
    public function store($slug_anime,Request $request)
    {
        $this->validasi($request);
        $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
        if($anime!=null){
          $user = Auth::user();
          $judul_anime=str_replace(' ', '_', $anime->judul);
          if($request->has('cover')){
            $season=str_replace(' ', '_', $request->season);
            $namafile=$judul_anime.'_'.$season.'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
            $request->file('cover')->move(storage_path('cover_season/'.$judul_anime),$namafile);
          }
          $season = new Season([
            'user_id'=>$user->id,
            'anime_id'=>$anime->id,
            'studio_id'=>$request->has('studio_id') ?$request->studio_id:null,
            'season'=>$request->season,
            'slug'=>$request->slug,
            'durasi'=>$request->has('durasi') ?$request->durasi:null,
            'episode'=>$request->has('episode') ?$request->episode:null,
            'tanggal_tayang'=>$request->has('tanggal_tayang') ?Carbon::parse($request->tanggal_tayang):null,
            'tanggal_end'=>$request->has('tanggal_end') ?Carbon::parse($request->tanggal_end):null,
            'musim'=>$request->has('musim') ?$request->musim:null,
            'broadcast'=>$request->has('broadcast') ?$request->broadcast:null,
            'type'=>$request->has('type') ?$request->type:null,
            'cover'=>$request->has('cover') ?$namafile:null,
            'sinopsis'=>$request->has('sinopsis') ?$request->sinopsis:null,
          ]);
          if($season->save()){
            if($request->has('licensor')){
              $season->Licensor()->attach($request->licensor);
            }
            if($request->has('produser')){
              $season->Produser()->attach($request->produser);
            }
            $response=[
              'message'=>'Anime '.$anime->judul.' '.$season->season.' Berhasil Di Tambahkan',
              'data'=>$season
            ];
            return response()->json($response,201);
          }

          $response=[
            'message'=>'Telah terjadi kesalahan'
          ];
          return response()->json($response,404);
        }
        return response()->json([
          'message'=>'Anime Tidak ditemukan'
        ],404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug_anime,$slug_season)
    {
      $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select([
          'id','studio_id','season','slug','durasi','episode','tanggal_tayang','tanggal_end','musim','broadcast','type','cover','sinopsis'
          ])->where('anime_id',$anime->id)->where('slug','=',$slug_season)->first();
        if($season!=null){
          $seasonlicensor=SeasonLicensor::where('season_id',$season->id)->get();
          $licensor=null;
          if($seasonlicensor!=null){
            foreach($seasonlicensor as $i){
              $licensor[]=Licensor::select('nama','slug')->where('id',$i->licensor_id)->first();
            }
          }
          $season->licensor=$licensor;

          $seasonproduser=SeasonProduser::where('season_id',$season->id)->get();
          $produser=null;
          if($seasonproduser!=null){
            foreach($seasonproduser as $i){
              $produser[]=Produser::select('nama','slug')->where('id',$i->produser_id)->first();
            }
          }
          $season->produser=$produser;

          $episode=null;
          $response=[
            'message'=>'Detail '.$season->season.' Anime '.$anime->judul,
            'data'=>[
              'anime'=>$anime,
              'season'=>$season,
              'episode'=>$episode,
            ],
          ];
          return response()->json($response,200);
        }else{
          return response()->json([
            'message'=>'Season '.$slug_season.' Anime '.$anime->judul.' Tidak ditemukan'
          ],404);
        }
        $response=[
          'message'=>'Telah terjadi kesalahan'
        ];
        return response()->json($response,404);
      }
      return response()->json([
        'message'=>'Anime Tidak ditemukan'
      ],404);
    }

    /**
    *Show Detail Delete with soft delete
    */
    public function showDelete(Request $request)
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
    public function update($slug_anime,$slug_season,Request $request)
    {
      $this->validasiUpdate($request);
      $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $judul_anime=str_replace(' ', '_', $anime->judul);

        $season = Season::where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        $season=$this->ubah($season,$request);
        if($request->has('cover')){
          $judul_anime=str_replace(' ', '_', $anime->judul);
          $_season=str_replace(' ', '_', $season->season);
          $namafile=$judul_anime.'_'.$_season.'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
          $request->file('cover')->move(storage_path('cover_season/'.$judul_anime),$namafile);
          $season->cover=$namafile;
        }

        if($season->save()){
          if($request->has('licensor')){
            $season->Licensor()->sync($request->licensor);
          }
          if($request->has('produser')){
            $season->Produser()->sync($request->produser);
          }
          $seasonlicensor=SeasonLicensor::where('season_id',$season->id)->get();
          $licensor=null;
          if($seasonlicensor!=null){
            foreach($seasonlicensor as $i){
              $licensor[]=Licensor::select('nama','slug')->where('id',$i->licensor_id)->first();
            }
          }
          $season->licensor=$licensor;

          $seasonproduser=SeasonProduser::where('season_id',$season->id)->get();
          $produser=null;
          if($seasonproduser!=null){
            foreach($seasonproduser as $i){
              $produser[]=Produser::select('nama','slug')->where('id',$i->produser_id)->first();
            }
          }
          $season->produser=$produser;
          $response=[
            'message'=>'Anime '.$anime->judul.' '.$season->season.' Berhasil Di Update',
            'data'=>$season
          ];
          return response()->json($response,201);
        }

        $response=[
          'message'=>'Telah terjadi kesalahan'
        ];
        return response()->json($response,404);
      }
      return response()->json([
        'message'=>'Anime Tidak ditemukan'
      ],404);
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
        'season'=>'required|max:10',
        'slug'=>'required|alpha_dash|max:10',
        'studio_id'=>'integer|min:1',
        'episode'=>'integer|min:1',
        'tanggal_tayang'=>'date_format:d-m-Y',
        'tanggal_end'=>'date_format:d-m-Y',
        'musim'=>'max:11',
        'broadcast'=>'max:255',
        'cover'=>'image',
        'sinopsis'=>'',
        'licensor'=>'array|min:1',
        'licensor.*'=>'integer|distinct|min:1',
        'produser'=>'array|min:1',
        'produser.*'=>'integer|distinct|min:1',
      ]);
    }

    private function validasiUpdate($request){
      return $this->validate($request, [
        'season'=>'max:10',
        'slug'=>'max:10',
        'episode'=>'integer|min:1',
        'tanggal_tayang'=>'date_format:d-m-Y',
        'tanggal_end'=>'date_format:d-m-Y',
        'musim'=>'max:11',
        'broadcast'=>'max:255',
        'cover'=>'image',
        'sinopsis'=>'',
        'licensor'=>'array|min:1',
        'licensor.*'=>'integer|distinct|min:1',
        'produser'=>'array|min:1',
        'produser.*'=>'integer|distinct|min:1',
      ]);
    }

    private function ubah($season,$request){
      if($request->has('studio_id')){
        $season->studio_id=$request->studio_id;
      }
      if($request->has('season')){
        $season->season=$request->season;
      }
      if($request->has('slug')){
        $season->slug=$request->slug;
      }
      if($request->has('durasi')){
        $season->durasi=$request->durasi;
      }
      if($request->has('episode')){
        $season->episode=$request->episode;
      }
      if($request->has('tanggal_tayang')){
        $season->tanggal_tayang=Carbon::parse($request->tanggal_tayang);
      }
      if($request->has('tanggal_end')){
        $season->tanggal_end=Carbon::parse($request->tanggal_end);
      }
      if($request->has('musim')){
        $season->musim=$request->musim;
      }
      if($request->has('broadcast')){
        $season->broadcast=$request->broadcast;
      }
      if($request->has('type')){
        $season->type=$request->type;
      }
      if($request->has('sinopsis')){
        $season->sinopsis=$request->sinopsis;
      }
      return $season;
    }
}
