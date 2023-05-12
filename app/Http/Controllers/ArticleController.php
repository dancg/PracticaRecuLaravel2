<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articulos = Article::with('user')->orderBy('id', 'desc')->paginate(5);
        return view('articulos.inicio', compact('articulos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $autores = User::select('id', 'name')->orderBy('name')->get();
        return view('articulos.nuevo', compact('autores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo'=>['required', 'string', 'min:3', 'unique:articles,titulo'],
            'contenido'=>['required', 'string', 'min:10'],
            'estado'=>['required', 'in:Publicado,Borrador'],
            'user_id'=>['required', 'exists:users,id'],
            'imagen'=>['required', 'image', 'max:2048'],
        ]);
        $rutaImagen = $request->imagen->store('imagenarticulos');
        Article::create([
            'titulo'=>$request->titulo,
            'contenido'=>$request->contenido,
            'estado'=>$request->estado,
            'user_id'=>$request->user_id,
            'imagen'=>$rutaImagen,
        ]);
        return redirect()->route('articulos.index')->with('info', 'Artículo Creado');

    }

    /**
     * Display the specified resource.
     */
    public function show(Article $articulo)
    {

        return view('articulos.detalle', compact('articulo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $articulo)
    {
        $autores = User::select('id', 'name')->orderBy('name')->get();
        return view('articulos.editar', compact('articulo','autores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $articulo)
    {
        $request->validate([
            'titulo'=>['required', 'string', 'min:3', 'unique:articles,titulo,'. $articulo->id],
            'contenido'=>['required', 'string', 'min:10'],
            'estado'=>['required', 'in:Publicado,Borrador'],
            'user_id'=>['required', 'exists:users,id'],
            'imagen'=>['nullable', 'image', 'max:2048'],
        ]);
        $rutaImagen = $articulo->imagen;
        //Primero comprobamos si se ha subido una imgaen nueva, si es cierto, se guarda y se borra la antigua
        if(isset($request->imagen)){
            $rutaImagen = $request->imagen->store('imagenarticulos');
            Storage::delete($articulo->imagen);
        }
        $articulo->update([
            'titulo'=>$request->titulo,
            'contenido'=>$request->contenido,
            'estado'=>$request->estado,
            'user_id'=>$request->user_id,
            'imagen'=>$rutaImagen,
        ]);
        return redirect()->route('articulos.index')->with('info', 'Artículo Actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $articulo)
    {
        //Primero borro la imagen
        Storage::delete($articulo->imagen);
        //Ahora borro el artículo
        $articulo->delete();
        return redirect()->route('articulos.index')->with('info', 'Artículo Borrado');
    }
}
