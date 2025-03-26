@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Ajouter un mot de passe') }}</div>

                <div class="card-body">
                    <form id="password-form" method="POST" action="{{ route('passwords.store') }}">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="titre" class="col-md-4 col-form-label text-md-right">{{ __('Titre') }}</label>
                            <div class="col-md-6">
                                <input id="titre" type="text" class="form-control @error('titre') is-invalid @enderror" name="titre" value="{{ old('titre') }}" required autofocus>
                                @error('titre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="site_web" class="col-md-4 col-form-label text-md-right">{{ __('Site web') }}</label>
                            <div class="col-md-6">
                                <input id="site_web" type="text" class="form-control @error('site_web') is-invalid @enderror" name="site_web" value="{{ old('site_web') }}">
                                @error('site_web')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Nom d\'utilisateur') }}</label>
                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control sensitive-data">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Mot de passe') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control sensitive-data">
                                <input id="mot_de_passe_crypte" type="hidden" name="mot_de_passe_crypte">
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="master_password" class="col-md-4 col-form-label text-md-right">{{ __('Mot de passe maître') }}</label>
                            <div class="col-md-6">
                                <input id="master_password" type="password" class="form-control" required>
                                <small class="form-text text-muted">
                                    Ce mot de passe est utilisé pour chiffrer vos données mais n'est jamais envoyé au serveur.
                                </small>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Enregistrer') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const form = document.getElementById('password-form');
                            
                            form.addEventListener('submit', (e) => {
                                e.preventDefault();
                                
                                const masterPassword = document.getElementById('master_password').value;
                                const username = document.getElementById('username').value;
                                const password = document.getElementById('password').value;
                          
                                
                                try {
                                    const sensitiveData = {
                                        username: username,
                                        password: password
                                    };
                                    
                                    const jsonData = JSON.stringify(sensitiveData);
                                    
                                    const salt = CryptoJS.lib.WordArray.random(128/8);
                                    
                                    const key = CryptoJS.PBKDF2(masterPassword, salt, {
                                        keySize: 256/32,
                                        iterations: 1000
                                    });
                                    
                                    const iv = CryptoJS.lib.WordArray.random(128/8);
                                    
                                    const encrypted = CryptoJS.AES.encrypt(jsonData, key, {
                                        iv: iv,
                                        padding: CryptoJS.pad.Pkcs7,
                                        mode: CryptoJS.mode.CBC
                                    });
                                    
                                   
                                    const encryptedData = salt.toString() + 
                                                         ':' + 
                                                         iv.toString() + 
                                                         ':' + 
                                                         encrypted.toString();
                                    
                                    console.log('Encrypted data:', encryptedData);
                                    
                                    document.getElementById('mot_de_passe_crypte').value = encryptedData;
                                    
                                    document.getElementById('username').value = '';
                                    document.getElementById('password').value = '';
                                    
                                    form.submit();
                                    
                                } catch (error) {
                                    console.error('Encryption error:', error);
                                    alert('Erreur lors du chiffrement: ' + error.message);
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
