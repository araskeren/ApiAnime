<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Anime;
use App\Season;
use App\Episode;
use App\ServerList;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class ServerController extends Controller
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
    public function index($slug_anime,$slug_season,$ep)
    {
      $anime=Anime::select('id')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            $server=ServerList::select('server','slug','download','streaming')->where('episode_id',$episode->id)->orderBy('server','asc')->get();
            if($server!=null){
              $response=[
                'message'=>'Server List',
                'data'=>$server
              ];
              return response()->json($response,201);
            }
            $response=[
              'message'=>'Telah terjadi kesalahan'
            ];
            return response()->json($response,404);
          }
        }
      }
      $response=[
        'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !',
      ];
      return response()->json($response,404);
    }

    /**
     * Display all listing of the resource where get delete by softdelete.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDelete($slug_anime,$slug_season,$ep)
    {
      $anime=Anime::select('id')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            $server=ServerList::onlyTrashed()->select('server','slug','download','streaming')->where('episode_id',$episode->id)->orderBy('server','asc')->get();
            if($server!=null){
              $response=[
                'message'=>'Server Yang di hapus List',
                'data'=>$server
              ];
              return response()->json($response,201);
            }
            $response=[
              'message'=>'Telah terjadi kesalahan'
            ];
            return response()->json($response,404);
          }
        }
      }
      $response=[
        'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !',
      ];
      return response()->json($response,404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($slug_anime,$slug_season,$ep,Request $request)
    {
       $this->validasi($request);
       $anime=Anime::select('id')->where('slug',$slug_anime)->first();
       if($anime!=null){
         $season=Season::select('id')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
         if($season!=null){
           $episode=Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
           if($episode!=null){
             $server=new ServerList([
               'episode_id'=>$episode->id,
               'server'=>$request->nama_server,
               'slug'=>Str::slug($request->nama_server),
               'download'=>$request->has('download') ?$request->download:null,
               'streaming'=>$request->has('streaming') ?$request->streaming:null,
             ]);
             if($server->save()){
               $response=[
                 'message'=>'Server '.$request->nama_server.' Berhasil Di Tambahkan',
                 'data'=>$server
               ];
               return response()->json($response,201);
             }
             $response=[
               'message'=>'Telah terjadi kesalahan'
             ];
             return response()->json($response,404);
           }
         }
       }
       $response=[
         'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !',
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
    public function update($slug_anime,$slug_season,$ep,$server,Request $request)
    {
      $this->validasi($request);
      $anime=Anime::select('id')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            $server=ServerList::select('id','server','download','streaming')->where('episode_id',$episode->id)->where('slug',$server)->first();
            if($request->has('server_id')){
              $server->episode_id=$episode->id;
            }
            if($request->has('nama_server')){
              $server->server=$request->nama_server;
              $server->slug=Str::slug($request->nama_server);
            }
            if($request->has('download')){
              $server->download=$request->has('download') ?$request->download:null;
            }
            if($request->has('streaming')){
              $server->streaming=$request->has('streaming') ?$request->streaming:null;
            }

            if($server->save()){
              $response=[
                'message'=>'Server '.$request->nama_server.' Berhasil Di Ubah',
                'data'=>$server
              ];
              return response()->json($response,201);
            }
            $response=[
              'message'=>'Telah terjadi kesalahan'
            ];
            return response()->json($response,404);
          }
        }
      }
      $response=[
        'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !',
      ];
      return response()->json($response,404);
    }

    /**
     * Restore the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($slug_anime,$slug_season,$ep,$server)
    {
      $anime=Anime::select('id')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            $server=ServerList::onlyTrashed()->select('id','server')->where('episode_id',$episode->id)->where('slug',$server)->first();
            if($server->restore()){
              $response=[
                'message'=>'Server '.$server->server.' Berhasil Di Restore',
                'data'=>$server
              ];
              return response()->json($response,201);
            }
            $response=[
              'message'=>'Telah terjadi kesalahan'
            ];
            return response()->json($response,404);
          }
        }
      }
      $response=[
        'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !',
      ];
      return response()->json($response,404);
    }

    /**
     * Remove the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug_anime,$slug_season,$ep,$server)
    {
      $anime=Anime::select('id')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            $server=ServerList::select('id','server')->where('episode_id',$episode->id)->where('slug',$server)->first();
            if($server->delete()){
              $response=[
                'message'=>'Server '.$server->server.' Berhasil Di Hapus',
                'data'=>$server
              ];
              return response()->json($response,201);
            }
            $response=[
              'message'=>'Telah terjadi kesalahan'
            ];
            return response()->json($response,404);
          }
        }
      }
      $response=[
        'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !',
      ];
      return response()->json($response,404);
    }

    /**
     * Remove the specified resource from storage with hard delete *Permanent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function harddestroy($slug_anime,$slug_season,$ep,$server)
    {
      $anime=Anime::select('id')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            $server=ServerList::onlyTrashed()->select('id','server')->where('episode_id',$episode->id)->where('slug',$server)->first();
            if($server->forcedelete()){
              $response=[
                'message'=>'Server '.$server->server.' Berhasil Di Hapus Permanent',
                'data'=>$server
              ];
              return response()->json($response,201);
            }
            $response=[
              'message'=>'Telah terjadi kesalahan'
            ];
            return response()->json($response,404);
          }
        }
      }
      $response=[
        'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !',
      ];
      return response()->json($response,404);
    }

    private function validasi($request){
      return $this->validate($request, [
        'nama_server'=>'required|max:255',
        'download'=>'max:255',
        'streaming'=>'max:255',
      ]);
    }

    private function validasiUpdate($request){
      return $this->validate($request, [
        'required'=>'required|max:255',
        'angka'=>'integer|min:1',
        'date'=>'date_format:d-m-Y',
        'string'=>'alpha|max:15',
        'array'=>'array|array|min:1',
        'image'=>'image',
      ]);
    }
}
