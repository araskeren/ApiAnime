<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Anime;
use App\Season;
use App\Episode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class EpisodeController extends Controller
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
    public function index($slug_anime,$slug_season)
    {
        $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
        if($anime!=null){
          $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
          if($season!=null){
            $episode=Episode::where('season',$season->id)->select('episode','cover','keterangan')->orderBy('episode','asc')->get();
            foreach($episode as $i){
              $i->anime=$anime->judul;
              $i->season=$season;
              $i->view_episode=[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$i->episode,
                'method'=>'GET',
              ];
              $i->cover=[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$i->episode.'/cover',
                'method'=>'GET',
              ];
            }
            $response=[
              'message'=>'Daftar Seluruh Episode',
              'data'=>$episode,
            ];
            return response()->json($response,200);
          }
          $response=[
            'message'=>'Season '.$slug_season.' Tidak Ditemukan',
          ];
          return response()->json($response,404);
        }
        $response=[
          'message'=>'Anime '.$slug_anime.' Tidak Ditemukan',
        ];
        return response()->json($response,404);

    }

    /**
     * Display all listing of the resource where get delete by softdelete.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDelete($slug_anime,$slug_season)
    {
      $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::onlyTrashed()->select('episode','keterangan')->where('season',$season->id)->get();
          if($episode!=null){
            foreach($episode as $i){
              $i->cover=[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$i->episode.'/cover',
                'method'=>'GET',
              ];
              $i->restore=[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$i->episode.'/restore',
                'method'=>'GET',
              ];
              $i->forcedelete=[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$i->episode.'/destroy',
                'method'=>'DESTROY',
              ];
            }
            $response=[
              'message'=>'Daftar Seluruh Episode Yang Di Hapus',
              'data'=>$episode,
            ];
            return response()->json($response,200);
          }
        }
      }
      $response=[
        'message'=>'Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !'
      ];
      return response()->json($response,404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($slug_anime,$slug_season,Request $request)
    {
      $this->validasi($request);
      $user = Auth::user();
      $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season = Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($this->cekDuplikasi($season->id,$request->episode)){
          $response=[
            'message'=>'Duplikasi Data !'
          ];
          return response()->json($response,404);
        }
        if($season!=null){
          $judul_anime=str_replace(' ', '_',$anime->judul);
          $judul_season=str_replace(' ','_',$season->season);
          $namafile=null;
          if($request->has('cover')){
            $namafile=$judul_anime.'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
            $request->file('cover')->move(storage_path('cover_episode/'.$judul_anime.'/'.$judul_season.'/'),$namafile);
          }
          $episode = new Episode([
            'user'=>$user->id,
            'season'=>$season->id,
            'episode'=>$request->episode,
            'cover'=>$request->has('cover') ?$namafile:null,
            'keterangan'=>$request->has('keterangan') ?$request->keterangan:null,
          ]);

          if($episode->save()){
            $episode->view_episode=[
              'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$episode->episode,
              'method'=>'GET',
            ];
            $response=[
              'message'=>'Episode Ke-'.$episode->episode.' Anime '.$anime->judul.' '.$season->season.' Berhasil Di Tambahkan',
              'data'=>$episode
            ];
            return response()->json($response,201);
          }
          $response=[
            'message'=>'Telah terjadi kesalahan'
          ];
          return response()->json($response,404);
        }
        $response=[
          'message'=>$slug_season.'Anime '.$slug_anime.' Tidak Di Temukan !'
        ];
        return response()->json($response,404);
      }
      $response=[
        'message'=>'Anime Tidak Di Temukan !'
      ];
      return response()->json($response,404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug_anime,$slug_season,$ep)
    {
       $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
       if($anime!=null){
         $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
         if($season!=null){
           $episode=Episode::select('episode','cover','keterangan','created_at')->where('season',$season->id)->where('episode',$ep)->first();
           if($episode!=null){
             $episode->judul_anime=$anime->judul;
             $episode->season=$season->season;
             $episode->cover=[
               'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$episode->episode.'/cover',
               'method'=>'GET',
             ];
             $episode->server_list=null;
             $response=[
               'message'=>'Detail Episode',
               'data'=>$episode,
             ];
             return response()->json($response,200);
           }
           $response=[
             'message'=>'Anime '.$slug_anime.' '.$slug_season.' Episode '.$ep.' Tidak Di Temukan !'
           ];
           return response()->json($response,404);
         }
         $response=[
           'message'=>'Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !'
         ];
         return response()->json($response,404);
       }
       $response=[
         'message'=>'Anime '.$slug_anime.' Tidak Di Temukan !'
       ];
       return response()->json($response,404);
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
    public function update($slug_anime,$slug_season,$ep,Request $request)
    {
        $this->validasi($request);
        $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
        if($anime!=null){
          $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
          if($season!=null){
            $episode=Episode::select('id','season','episode','cover','keterangan')->where('season',$season->id)->where('episode',$ep)->first();
            if($episode!=null){
              if($request->has('episode')){
                $episode->episode=$request->episode;
                $ep=$request->episode;
              }
              if($request->has('cover')){
                $judul_anime=str_replace(' ', '_',$anime->judul);
                $judul_season=str_replace(' ', '_',$season->season);
                if($episode->cover!=null){
                  $image_path = storage_path()."/cover_episode/".$judul_anime."/".$judul_season.'/'.$episode->cover;
                  if(file_exists($image_path)) {
                      unlink($image_path);
                  }
                }
                $namafile=$judul_anime.'_'.$slug_season.'_'.$ep.'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
                $request->file('cover')->move(storage_path('cover_episode/'.$judul_anime.'/'.$judul_season.'/'),$namafile);
                $episode->cover=$namafile;
              }
              if($request->has('season')){
                $episode->season=$request->season;
              }
              if($request->has('episode')){
                $episode->episode=$request->episode;
              }
              if($request->has('keterangan')){
                $episode->keterangan=$request->keterangan;
              }
              if(!$episode->update()){
                return response()->json(['message'=>'Gagal Mengupdate Data !',404]);
              }
              $episode->view_anime=[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$episode->episode,
                'method'=>'GET'
              ];
              $episode->cover=[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$episode->episode.'/cover',
                'method'=>'GET'
              ];
              $response=[
                'message'=>'Anime '.$anime->judul.' '.$season->season.' Episode '.$ep.' Berhasil di update!',
                'data'=>$episode
              ];
              return response()->json($response,201);
            }
            $response=[
              'message'=>'Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !'
            ];
            return response()->json($response,404);
          }
        }
        $response=[
          'message'=>'Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !'
        ];
        return response()->json($response,404);
    }

    /**
     * Restore the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($slug_anime,$slug_season,$ep)
    {
      $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::onlyTrashed()->select('id')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            if($episode->restore()){
              $response=[
                'message'=>'Anime '.$anime->judul.' '.$season->season.' Episode '.$ep.' Berhasil direstore!',
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
        }
      }
      $response=[
        'message'=>'Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !'
      ];
      return response()->json($response,404);
    }

    /**
     * Remove the specified resource from storage with softdelete.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug_anime,$slug_season,$ep)
    {
        $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
        if($anime!=null){
          $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
          if($season!=null){
            $episode = Episode::select('id')->where('season',$season->id)->where('episode',$ep)->first();
            if($episode!=null){
              if(!$episode->delete()){
                return response()->json([
                  'message'=>'Prosess Delete Episode gagal'
                ],404);
              }
              $response=[
                'message'=>'Episode '.$ep.' Anime '.$anime->judul.' '.$season->season .' berhasil di hapus',
                'create'=>[
                  'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/episode',
                  'method'=>'POST',
                  'params'=>[
                    'episode'=>'required|integer|min:0',
                    'cover'=>'image',
                    'keterangan'=>'',
                  ]
                ],
                'forcedelete'=>[
                  'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$ep.'/destroy',
                  'method'=>'DELETE',
                ],
                'restore'=>[
                  'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/'.$ep.'/restore',
                  'method'=>'GET',
                ]
              ];
              return response()->json($response,200);
            }
            return response()->json([
              'message'=>'Episode '.$ep.' Anime '.$anime->judul.' '.$season->season .' Tidak Ditemukan'
            ],404);
          }
          return response()->json([
            'message'=>'Episode '.$ep.' Anime '.$anime->judul.' '.$slug_season .' Tidak Ditemukan'
          ],404);
        }
        return response()->json([
          'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season .' Tidak Ditemukan'
        ],404);
    }

    /**
     * Remove the specified resource from storage with hard delete *Permanent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function harddestroy($slug_anime,$slug_season,$ep)
    {
      $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode = Episode::onlyTrashed()->select('id','cover')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode!=null){
            if(!$episode->forcedelete()){
              return response()->json([
                'message'=>'Prosess Delete Episode gagal'
              ],404);
            }
            $judul_anime=str_replace(' ', '_',$anime->judul);
            $judul_season=str_replace(' ', '_',$season->season);
            if($episode->cover!=null){
              $image_path = storage_path()."/cover_episode/".$judul_anime."/".$judul_season.'/'.$episode->cover;
              if(file_exists($image_path)) {
                  unlink($image_path);
              }
            }
            $response=[
              'message'=>'Episode '.$ep.' Anime '.$anime->judul.' '.$season->season .' berhasil di hapus',
              'create'=>[
                'href'=>'/api/v1/anime/'.$slug_anime.'/'.$slug_season.'/episode',
                'method'=>'POST',
                'params'=>[
                  'episode'=>'required|integer|min:0',
                  'cover'=>'image',
                  'keterangan'=>'',
                ]
              ],
            ];
            return response()->json($response,200);
          }
        }
      }
      return response()->json([
        'message'=>'Episode '.$ep.' Anime '.$slug_anime.' '.$slug_season .' Tidak Ditemukan'
      ],404);
    }

    public function viewImage($slug_anime,$slug_season,$ep){
      $anime=Anime::select('id','judul')->where('slug',$slug_anime)->first();
      if($anime!=null){
        $season=Season::select('id','season')->where('anime_id',$anime->id)->where('slug',$slug_season)->first();
        if($season!=null){
          $episode=Episode::withTrashed()->select('cover')->where('season',$season->id)->where('episode',$ep)->first();
          if($episode->cover!=null&&$episode!=null){
            $judul_anime=str_replace(' ', '_',$anime->judul);
            $judul_season=str_replace(' ', '_',$season->season);
            $cover = storage_path()."/cover_episode/".$judul_anime."/".$judul_season.'/'.$episode->cover;
            if (file_exists($cover)) {
              $file = file_get_contents($cover);
              return response($file, 200)->header('Content-Type', 'image/jpeg');
            }
            $response=[
              'message'=>'Gambar Anime '.$slug_anime.' '.$slug_season.' Episode '.$ep.' Tidak Di Temukan !'
            ];
            return response()->json($response,404);
          }
          $response=[
            'message'=>'Gambar Anime '.$slug_anime.' '.$slug_season.' Episode '.$ep.' Tidak Di Temukan !'
          ];
          return response()->json($response,404);
        }
        $response=[
          'message'=>'Gambar Anime '.$slug_anime.' '.$slug_season.' Tidak Di Temukan !'
        ];
        return response()->json($response,404);
      }
      $response=[
        'message'=>'Gambar Anime '.$slug_anime.' Tidak Di Temukan !'
      ];
      return response()->json($response,404);
    }
    //Validasi Inputan
    private function validasi($request){
      return $this->validate($request, [
        'episode'=>'required|integer|min:0',
        'season'=>'integer|min:0',
        'cover'=>'image',
        'keterangan'=>'',
      ]);
    }
    private function cekDuplikasi($season,$episode){
      $episode=Episode::select('season','episode')->where('season',$season)->where('episode',$episode)->first();
      if(count($episode)>0){
        return true;
      }else{
        return false;
      }
    }
    // private function cekDuplikasiUpdate($season,$episode,$id){
    //   $episode=Episode::select('season','episode')->where('season',$season)->where('episode',$episode)->first();
    //   if(count($episode)>0){
    //     foreach($episod as $i){
    //
    //     }
    //     return true;
    //   }else{
    //     return false;
    //   }
    // }


}
