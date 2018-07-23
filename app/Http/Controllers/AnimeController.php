<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Anime;
use App\Genre;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class AnimeController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $anime=Anime::select('id','judul','slug','sumber')->get();
      foreach($anime as $i){
        $genre=null;
        foreach($i->genre as $j){
          $genre[]=Genre::select('genre','slug')->where('id',$j->pivot->genre_id)->first();
        }
        unset($i->genre);
        $i->genre=$genre;
        $i->view_cover=[
          'href'=>'/api/v1/anime/'.$i->slug.'/cover',
          'method'=>'GET',
        ];
        $i->view_anime=[
          'href'=>'/api/v1/anime/'.$i->slug,
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh Anime',
        'data'=>$anime,
      ];
      return response()->json($response,200);
    }
    public function indexDelete()
    {
      $anime=Anime::onlyTrashed()->select('id','judul','slug')->get();
      foreach($anime as $i){
        $genre=null;
        foreach($i->genre as $j){
          $genre[]=Genre::select('genre','slug')->where('id',$j->pivot->genre_id)->first();
        }
        unset($i->genre);
        $i->genre=$genre;
        $i->view_cover=[
          'href'=>'/api/v1/anime/'.$i->slug.'/cover',
          'method'=>'GET',
        ];
        $i->restore=[
          'href'=>'/api/v1/anime/'.$i->slug.'/restore',
          'method'=>'GET',
        ];
        $i->permanent_delete=[
          'href'=>'/api/v1/anime/'.$i->slug.'/destroy',
          'method'=>'DELETE',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh Anime Yang Di Hapus',
        'data'=>$anime,
      ];
      return response()->json($response,200);
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
      $user = Auth::user();
      $namafile=null;
      if($request->has('cover')){
        $namafile=str_replace(' ', '_', $request->judul).'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
        $request->file('cover')->move(storage_path('cover_anime'),$namafile);
      }
      $anime = new Anime([
        'user_id'=>$user->id,
        'judul'=>$request->judul,
        'judul_alternatif'=>$request->has('judul_alternatif') ?$request->judul_alternatif:null,
        'slug'=>Str::slug($request->judul),
        'sumber'=>$request->has('sumber') ?$request->sumber:null,
        'cover'=>$request->has('cover') ?$namafile:null,
      ]);

      if($anime->save()){
        if($request->has('genre')){
          $anime->Genre()->attach($request->genre);
        }
        $anime->view_anime=[
          'href'=>'/api/v1/anime/'.$anime->slug,
          'method'=>'GET',
        ];
        $response=[
          'message'=>'Anime '.$anime->judul.' Berhasil Di Tambahkan',
          'data'=>$anime
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
      $anime=Anime::where('slug',$slug)->get();
      foreach($anime as $i){
        $genre=null;
        foreach($i->genre as $j){
          $genre[]=Genre::select('genre','slug')->where('id',$j->pivot->genre_id)->first();
        }
        unset($i->genre);
        $i->genre=$genre;
        $i->view_cover=[
          'href'=>'/api/v1/anime/'.$slug.'/cover',
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Detail Anime',
        'data'=>$anime,
      ];
      return response()->json($response,200);
    }
    public function showDelete($id)
    {
      $anime=Anime::onlyTrashed()->where('id',$id)->get();
      foreach($anime as $i){
        if($i->studio_id!=null){
          $i->studio_id=$i->Studio->nama;
        }
        $i->genre=$i->Genre;
        $i->licensor=$i->Licensor;
        $i->produser=$i->produser;
        $i->restore=[
          'href'=>'/api/v1/anime/delete/'.$i->id,
          'method'=>'POST',
        ];
      }
      $response=[
        'message'=>'Detail Anime Yang Di Delete Dengan Softdelete',
        'data'=>$anime,
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
    public function update(Request $request, $slug)
    {
        $this->validasiUpdate($request);
        $anime=Anime::with('genre')->where('slug',$slug)->first();
        if($anime!=null){
          $anime=$this->ubah($anime,$request);

          if(!$anime->update()){
            return response()->json(['message'=>'Gagal Mengupdate Data !',404]);
          }
          if($request->has('genre')){
            $anime->Genre()->sync($request->genre);
          }
          $anime->view_anime=[
            'href'=>'/api/v1/anime/'.$anime->slug,
            'method'=>'GET'
          ];
          $anime->view_cover=[
            'href'=>'/api/v1/anime/'.$slug.'/cover',
            'method'=>'GET',
          ];
          $response=[
            'message'=> 'Anime Telah Berhasil di update',
            'data'=>$anime
          ];
          return response()->json([$response,401]);
        }
        return response()->json([
          'message'=>'Anime Tidak ditemukan'
        ],404);
    }


    //restore Soft Delete
    public function restore($slug){

      $anime=Anime::onlyTrashed()->select('id','judul','judul_alternatif','slug','sumber','cover')->where('slug',$slug)->get();
      if(count($anime)>0){
        foreach($anime as $i){
          foreach ($i->Genre()->withTrashed()->get() as $j){
            DB::table('anime_genre')
                ->where('anime_id',$j->pivot->anime_id)
                ->where('genre_id',$j->pivot->genre_id)
                ->update(['deleted_at' => null]);
          }
          $i->view_anime=[
            'href'=>'/api/v1/anime/'.$i->slug,
            'method'=>'GET',
          ];
          $i->view_cover=[
            'href'=>'/api/v1/anime/'.$i->slug.'/cover',
            'method'=>'GET',
          ];
        }
        Anime::onlyTrashed()->where('slug',$slug)->restore();
        $response=[
          'message'=>'Detail Anime Yang Berhasil di restore',
          'data'=>$anime,
        ];
        return response()->json($response,200);
      }
      return response()->json(['message'=>'Anime Tidak ditemukan'],404);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $anime=Anime::where('slug',$slug)->first();
        if($anime!=null){
          $genre=$anime->Genre;
          if(count($genre)>0){
            foreach ($genre as $i){
              DB::table('anime_genre')
                  ->where('anime_id',$i->pivot->anime_id)
                  ->where('genre_id',$i->pivot->genre_id)
                  ->update(['deleted_at' => Carbon::now()]);
            }
          }
          if(!$anime->delete()){
            if(count($genre)>0){
              foreach ($genre as $i){
                DB::table('anime_genre')
                    ->withTrashed()
                    ->where('anime_id',$i->pivot->anime_id)
                    ->where('genre_id',$i->pivot->genre_id)
                    ->update(['deleted_at' => null]);
              }
            }
            return response()->json([
              'message'=>'Prosess Delete Anime gagal'
            ],404);
          }

          $response=[
            'message'=>'Anime berhasil di hapus',
            'data'=>[
              'create'=>[
                'href'=>'/api/v1/anime',
                'method'=>'POST',
                'params'=>[
                  'judul = required|max:255',
                  'judul_alternatif = max:255',
                  'genre = array|integer|min:1',
                  'cover = image',
                ],
              ],
              'restore'=>[
                'href'=>'/api/v1/anime/'.$anime->slug.'/restore',
                'method'=>'GET',
              ],
              'permanent_delete'=>[
                'href'=>'/api/v1/anime/'.$anime->slug.'/destroy',
                'method'=>'DELETE',
              ],
            ],
          ];
          return response()->json($response,200);
        }
        return response()->json([
          'message'=>'Anime Tidak ditemukan'
        ],404);
    }
    public function harddestroy($slug)
    {
        $anime=Anime::onlyTrashed()->where('slug',$slug)->select('id','judul','slug')->first();

        if($anime!=null){
          $genre=$anime->Genre;

          if(count($genre)>0){
            $anime->Genre()->detach();
          }
          if(!$anime->forceDelete()){
            if(count($genre)>0){
              $anime->Genre()->attach($genre);
            }
            return response()->json([
              'message'=>'Prosess Delete Anime gagal'
            ],404);
          }

          $response=[
            'message'=>'Anime berhasil di hapus',
            'data'=>[
              'create'=>[
                'href'=>'/api/v1/anime',
                'method'=>'POST',
                'params'=>[
                  'judul = required|max:255',
                  'judul_alternatif = max:255',
                  'genre = array|integer|min:1',
                  'cover = image',
                ],
              ],
            ]
          ];
          return response()->json($response,200);
        }
        return response()->json([
          'message'=>'Anime Tidak di temukan'
        ],404);
    }

    public function viewImage($slug){
      $judul=Anime::select('cover')->where('slug',$slug)->first()->cover;
      if($judul!=null){
        $cover = storage_path()."/cover_anime/".$judul;
      }else{
        $cover=null;
      }
      if(file_exists($cover)) {
        $file = file_get_contents($cover);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
      }
      $res['success'] = false;
      $res['message'] = "Gambar Tidak Ditemukan";

      return $res;
    }
    //Validasi Inputan
    private function validasi($request){
      return $this->validate($request, [
        'judul'=>'unique:anime|required|max:255',
        'judul_alternatif'=>'max:255',
        'cover'=>'image',
        'genre'=>'array',
        'sinopsis'=>'',
      ]);
    }
    private function validasiUpdate($request){
      return $this->validate($request, [
        'judul'=>'max:255',
        'judul_alternatif'=>'max:255',
        'cover'=>'image',
        'genre'=>'array',
        'sinopsis'=>'',
      ]);
    }
    private function ubah($anime,$request){
      if($request->has('judul')){
        $anime->judul=$request->judul;
        $anime->slug=Str::slug($request->judul);
      }
      if($request->has('judul_alternatif')){
        $anime->judul_alternatif=$request->judul_alternatif;
      }
      if($request->has('sumber')){
        $anime->sumber=$request->sumber;
      }
      if($request->has('cover')){
        $namafile=str_replace(' ', '_',$anime->judul).'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
        $request->file('cover')->move(storage_path('cover_anime'),$namafile);
        $anime->cover=$namafile;
      }
      return $anime;
    }
}
