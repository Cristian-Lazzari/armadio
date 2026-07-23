<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GarmentPhotoController extends Controller
{
    /**
     * Riceve la miniatura (già ridimensionata lato client) e la salva
     * su storage/app/public/garments. Ritorna l'URL pubblico.
     * Richiede `php artisan storage:link` eseguito una volta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|max:2048', // max 2 MB
        ]);

        $path = $request->file('photo')->storeAs(
            'garments',
            Str::random(20) . '.jpg',
            'public'
        );

        return response()->json(['url' => asset('storage/' . $path)]);
    }
}
