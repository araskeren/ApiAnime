<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Anime;
use Carbon\Carbon;
class AnimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $anime=Anime::select('id','judul','status')->get();
      foreach($anime as $i){
        $i->genre=$i->Genre;
        $i->view_anime=[
          'href'=>'/api/v1/anime/'.$i->id,
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
      $anime=Anime::onlyTrashed()->select('id','judul','status')->get();
      foreach($anime as $i){
        $i->genre=$i->Genre;
        $i->view_anime=[
          'href'=>'/api/v1/anime/delete/'.$i->id,
          'method'=>'GET',
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
      $namafile=null;
      if($request->has('cover')){
        $namafile=str_replace(' ', '_', $request->judul).'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
        $request->file('cover')->move(storage_path('cover_anime'),$namafile);
      }
      $anime = new Anime([
        'user_id'=>$request->user_id,
        'judul'=>$request->judul,
        'judul_alternatif'=>$request->has('judul_alternatif') ?$request->judul_alternatif:null,
        'studio_id'=>$request->has('studio_id') ?$request->studio_id:null,
        'durasi'=>$request->has('durasi') ?$request->durasi:null,
        'episode'=>$request->has('episode') ?$request->episode:null,
        'tanggal_tayang'=>$request->has('episode') ?$request->tanggal_tayang:null,
        'tanggal_end'=>$request->has('tanggal_end') ?$request->tanggal_end:null,
        'type'=>$request->has('type') ?$request->type:null,
        'sumber'=>$request->has('sumber') ?$request->sumber:null,
        'musim'=>$request->has('musim') ?$request->musim:null,
        'status'=>$request->has('status') ?$request->status:null,
        'broadcast'=>$request->has('broadcast') ?$request->broadcast:null,
        'cover'=>$request->has('cover') ?$namafile:null,
        'sinopsis'=>$request->has('sinopsis') ?$request->sinopsis:null,
      ]);

      if($anime->save()){
        if($request->has('genre')){
          $anime->Genre()->attach($request->genre);
        }
        if($request->has('licensor')){
          $anime->Licensor()->attach($request->licensor);
        }
        if($request->has('produser')){
          $anime->Produser()->attach($request->produser);
        }
        $anime->view_anime=[
          'href'=>'/api/v1/anime/'.$anime->id,
          'method'=>'GET',
        ];
        $response=[
          'message'=>'Anime '.$anime->judul.' Berhasil Di Tambahgan',
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
    public function show($id)
    {
      $anime=Anime::where('id',$id)->get();
      foreach($anime as $i){
        if($i->studio_id!=null){
          $i->studio_id=$i->Studio->nama;
        }
        $i->genre=$i->Genre;
        $i->licensor=$i->Licensor;
        $i->produser=$i->produser;
        $i->view_anime=[
          'href'=>'/api/v1/anime',
          'method'=>'GET',
        ];
      }
      $response=[
        'message'=>'Daftar Seluruh Anime',
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
    public function update(Request $request, $id)
    {
        $this->validasiUpdate($request);
        $anime=Anime::with('genre')->findOrFail($id);
        $anime=$this->ubah($anime,$request);

        if(!$anime->update()){
          return response()->json(['message'=>'Gagal Mengupdate Data !',404]);
        }
        if($request->has('genre')){
          $anime->Genre()->sync($request->genre);
        }
        $anime->view_anime=[
          'href'=>'/api/v1/anime'.$anime->id,
          'method'=>'GET'
        ];
        $response=[
          'message'=> 'Anime Telah Berhasil di update',
          'meeting'=>$anime
        ];
        return response()->json([$response,401]);
    }

    //restore Soft Delete
    public function restore($id){

      $anime=Anime::onlyTrashed()->where('id',$id)->get();
      foreach($anime as $i){
        if($i->studio_id!=null){
          $i->studio_id=$i->Studio->nama;
        }
        foreach ($i->Genre()->withTrashed()->get() as $j){
          DB::table('anime_genre')
              ->where('anime_id',$j->pivot->anime_id)
              ->where('genre_id',$j->pivot->genre_id)
              ->update(['deleted_at' => null]);
        }
        foreach ($i->Produser()->withTrashed()->get() as $j){
          DB::table('anime_produser')
              ->where('anime_id',$j->pivot->anime_id)
              ->where('produser_id',$j->pivot->produser_id)
              ->update(['deleted_at' => null]);
        }
        foreach ($i->Licensor()->withTrashed()->get() as $j){
          DB::table('anime_licensor')
              ->where('anime_id',$j->pivot->anime_id)
              ->where('licensor_id',$j->pivot->licensor_id)
              ->update(['deleted_at' => null]);
        }
        $i->view_anime=[
          'href'=>'/api/v1/anime/'.$i->id,
          'method'=>'GET',
        ];
      }
      Anime::onlyTrashed()->where('id',$id)->restore();
      $response=[
        'message'=>'Detail Anime Yang Berhasil di restore',
        'data'=>$anime,
      ];
      return response()->json($response,200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $anime=Anime::findOrFail($id);
        $genre=$anime->Genre;
        $licensor=$anime->Licensor;
        $produser=$anime->produser;

        if(count($genre)>0){
          //$anime->Genre()->detach();
          foreach ($genre as $i){
            DB::table('anime_genre')
                ->where('anime_id',$i->pivot->anime_id)
                ->where('genre_id',$i->pivot->genre_id)
                ->update(['deleted_at' => Carbon::now()]);
          }
        }
        if(count($produser)>0){
          //$anime->Produser()->detach();
          foreach ($produser as $i){
            DB::table('anime_produser')
                ->where('anime_id',$i->pivot->anime_id)
                ->where('produser_id',$i->pivot->produser_id)
                ->update(['deleted_at' => Carbon::now()]);
          }
        }
        if(count($licensor)>0){
          // $anime->Licensor()->detach();
          foreach ($licensor as $i){
            DB::table('anime_licensor')
                ->where('anime_id',$i->pivot->anime_id)
                ->where('licensor_id',$i->pivot->licensor_id)
                ->update(['deleted_at' => Carbon::now()]);
          }
        }
        if(!$anime->delete()){
          if(count($genre)>0){
            // $anime->Genre()->attach($genre);
            foreach ($genre as $i){
              DB::table('anime_genre')
                  ->withTrashed()
                  ->where('anime_id',$i->pivot->anime_id)
                  ->where('genre_id',$i->pivot->genre_id)
                  ->update(['deleted_at' => null]);
            }
          }
          if(count($produser)>0){
            // $anime->Produser()->attach($produser);
            foreach ($produser as $i){
              DB::table('anime_produser')
                  ->withTrashed()
                  ->where('anime_id',$i->pivot->anime_id)
                  ->where('produser_id',$i->pivot->produser_id)
                  ->update(['deleted_at' => null]);
            }
          }
          if(count($licensor)>0){
            // $anime->Licensor()->attach($licensor);
            foreach ($licensor as $i){
              DB::table('anime_licensor')
                  ->withTrashed()
                  ->where('anime_id',$i->pivot->anime_id)
                  ->where('licensor_id',$i->pivot->licensor_id)
                  ->update(['deleted_at' => null]);
            }
          }
          return response()->json([
            'message'=>'Prosess Delete Anime gagal'
          ],404);
        }

        $response=[
          'message'=>'Anime berhasil di hapus',
          'create'=>[
            'href'=>'/api/v1/anime',
            'method'=>'POST',
            'params'=>[
              'judul = required|max:255',
              'judul_alternatif = max:255',
              'studio_id = integer|min:1',
              'durasi = integer|min:1',
              'episode = integer|min:0',
              'tanggal_tayang = date_format:(dd-mm-yyyy)',
              'tanggal_end = date_format:(dd-mm-yyyy)',
              'type = alpha|max:15',
              'sumber = alpha|max:15',
              'musim = max:11',
              'status = alpha|max:8',
              'genre = array|array|min:1',
              'produser = integer|array|min:1',
              'licensor = integer|array|min:1',
              'broadcast = max:255',
              'cover = image',
              'sinopsis = ',
            ]
          ]
        ];
        return response()->json($response,200);
    }
    public function harddestroy($id)
    {
        $anime=Anime::findOrFail($id);
        $genre=$anime->Genre;
        $licensor=$anime->Licensor;
        $produser=$anime->produser;

        if(count($genre)>0){
          $anime->Genre()->detach();
        }
        if(count($produser)>0){
          $anime->Produser()->detach();
        }
        if(count($licensor)>0){
          $anime->Licensor()->detach();
        }
        if(!$anime->forceDelete()){
          if(count($genre)>0){
            $anime->Genre()->attach($genre);
          }
          if(count($produser)>0){
            $anime->Produser()->attach($produser);
          }
          if(count($licensor)>0){
            $anime->Licensor()->attach($licensor);
          }
          return response()->json([
            'message'=>'Prosess Delete Anime gagal'
          ],404);
        }

        $response=[
          'message'=>'Anime berhasil di hapus',
          'create'=>[
            'href'=>'/api/v1/anime',
            'method'=>'POST',
            'params'=>[
              'judul = required|max:255',
              'judul_alternatif = max:255',
              'studio_id = integer|min:1',
              'durasi = integer|min:1',
              'episode = integer|min:0',
              'tanggal_tayang = date_format:(dd-mm-yyyy)',
              'tanggal_end = date_format:(dd-mm-yyyy)',
              'type = alpha|max:15',
              'sumber = alpha|max:15',
              'musim = max:11',
              'status = alpha|max:8',
              'genre = array|array|min:1',
              'produser = integer|array|min:1',
              'licensor = integer|array|min:1',
              'broadcast = max:255',
              'cover = image',
              'sinopsis = ',
            ]
          ]
        ];
        return response()->json($response,200);
    }

    //Validasi Inputan
    private function validasi($request){
      return $this->validate($request, [
        'judul'=>'required|max:255',
        'judul_alternatif'=>'max:255',
        'studio_id'=>'integer|min:1',
        'durasi'=>'integer|min:1',
        'episode'=>'integer|min:0',
        'tanggal_tayang'=>'date_format:d-m-Y',
        'tanggal_end'=>'date_format:d-m-Y',
        'type'=>'alpha|max:15',
        'sumber'=>'alpha|max:15',
        'musim'=>'max:11',
        'status'=>'alpha|max:8',
        'produser'=>'array|array|min:1',
        'licensor'=>'array|array|min:1',
        'genre'=>'array|array|min:1',
        'broadcast'=>'max:255',
        'cover'=>'image',
        'sinopsis'=>'',
      ]);
    }
    private function validasiUpdate($request){
      return $this->validate($request, [
        'judul'=>'max:255',
        'judul_alternatif'=>'max:255',
        'studio_id'=>'integer',
        'durasi'=>'integer|min:1',
        'episode'=>'integer|min:0',
        'tanggal_tayang'=>'date_format:d-m-Y',
        'tanggal_end'=>'date_format:d-m-Y',
        'type'=>'alpha|max:15',
        'sumber'=>'max:15',
        'musim'=>'max:11',
        'status'=>'alpha|max:8',
        'produser'=>'array|array|min:1',
        'licensor'=>'array|array|min:1',
        'genre'=>'array|array|min:1',
        'broadcast'=>'max:255',
        'cover'=>'image',
        'sinopsis'=>'',
      ]);
    }
    private function ubah($anime,$request){
      if($request->has('judul')){
        $anime->judul=$request->judul;
      }
      if($request->has('judul_alternatif')){
        $anime->judul_alternatif=$request->judul_alternatif;
      }
      if($request->has('studio_id')){
        $anime->studio_id=$request->studio_id;
      }
      if($request->has('durasi')){
        $anime->durasi=$request->durasi;
      }
      if($request->has('episode')){
        $anime->episode=$request->episode;
      }
      if($request->has('tanggal_tayang')){
        $anime->tanggal_tayang=Carbon::parse($request->tanggal_tayang);
      }
      if($request->has('tanggal_end')){
        $anime->tanggal_end=Carbon::parse($request->tanggal_end);
      }
      if($request->has('type')){
        $anime->type=$request->type;
      }
      if($request->has('sumber')){
        $anime->sumber=$request->sumber;
      }
      if($request->has('musim')){
        $anime->musim=$request->musim;
      }
      if($request->has('status')){
        $anime->status=$request->status;
      }
      if($request->has('produser')){
        $anime->produser=$request->produser;
      }
      if($request->has('licensor')){
        $anime->licensor=$request->licensor;
      }
      if($request->has('broadcast')){
        $anime->broadcast=$request->broadcast;
      }
      if($request->has('cover')){
        $namafile=str_replace(' ', '_',$anime->judul).'_'.time().'.'.$request->file('cover')->getClientOriginalExtension();
        $request->file('cover')->move(storage_path('cover_anime'),$namafile);
        $anime->cover=$namafile;
      }
      if($request->has('sinopsis')){
        $anime->sinopsis=$request->sinopsis;
      }

      return $anime;
    }
}
