<?php

namespace App\Http\Controllers;

use App\Models\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
 
    public function index()
    {
        $passwords = Password::where('user_id', Auth::id())->get();
        return view('passwords.index', compact('passwords'));
    }

    
    public function create()
    {
        return view('passwords.create');
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'titre' => 'required|string|max:255',
                'site_web' => 'nullable|string|max:255',
                'mot_de_passe_crypte' => 'required|string',
            ]);
            
            $userId = Auth::id();
            $lastUsed = now();
            
            $password = Password::create([
                'titre' => $validated['titre'],
                'site_web' => $validated['site_web'] ?? null,
                'mot_de_passe_crypte' => $validated['mot_de_passe_crypte'],
                'user_id' => $userId,
                'last_used' => $lastUsed,
            ]);
         //   dd("try");
            return redirect()->route('passwords.index')
                ->with('success', 'Mot de passe créé avec succès.');
        } catch (\Exception $e) {
            //('An error occurred: ' . $e->getMessage());
        }
    }

 
    public function show(Password $password)
    {
        $this->authorize('view', $password);
        
        return view('passwords.show', compact('password'));
    }

    
    public function edit(Password $password)
    {
        $this->authorize('update', $password);
        
        return view('passwords.edit', compact('password'));
    }

    
    public function update(Request $request, Password $password)
    {
        $this->authorize('update', $password);

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'site_web' => 'nullable|string|max:255',
            'mot_de_passe_crypte' => 'required|string',
        ]);

        $validated['last_used'] = now();
        
        $password->update($validated);

        return redirect()->route('passwords.index')
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

   
    public function updateLastUsed(Password $password)
    {
        $this->authorize('view', $password);
        
        $password->update(['last_used' => now()]);
        
        return response()->json(['success' => true]);
    }


    public function destroy(Password $password)
    {
        $this->authorize('delete', $password);
        
        $password->delete();

        return redirect()->route('passwords.index')
            ->with('success', 'Mot de passe supprimé avec succès.');
    }
}
