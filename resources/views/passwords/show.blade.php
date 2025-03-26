@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Détails du mot de passe</span>
                    <a href="{{ route('passwords.index') }}" class="btn btn-secondary btn-sm">
                        Retour à la liste
                    </a>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="password-info">
                        <h3>{{ $password->titre }}</h3>
                        
                        @if($password->site_web)
                        <p>
                            <strong>Site Web:</strong> 
                            <a href="{{ $password->site_web }}" target="_blank">{{ $password->site_web }}</a>
                        </p>
                        @endif
                        
                        
                        <div class="sensitive-data">
                            <p>
                                <strong>Mot de passe:</strong>
                                <span id="password-container">
                                    <span id="password-text" style="font-family: monospace; font-weight: bold; font-size: 1.1em;"></span>
                                    <input type="password" id="master_password" placeholder="Entrez votre mot de passe maître" class="form-control mt-2">
                                    <button id="decrypt-btn" class="btn btn-primary btn-sm mt-2">Déchiffrer</button>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('passwords.edit', $password) }}" class="btn btn-warning">Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const encryptedData = "{{ $password->mot_de_passe_crypte }}";
    const passwordText = document.getElementById('password-text');
    const masterPasswordInput = document.getElementById('master_password');
    const decryptBtn = document.getElementById('decrypt-btn');
    
    decryptBtn.addEventListener('click', function() {
        const masterPassword = masterPasswordInput.value;
        
            const parts = encryptedData.split(':');
            const salt = CryptoJS.enc.Hex.parse(parts[0]);
            const iv = CryptoJS.enc.Hex.parse(parts[1]);
            const ciphertext = parts[2];
            
            const key = CryptoJS.PBKDF2(masterPassword, salt, {
                keySize: 256/32,
                iterations: 1000
            });
            
            
            const decrypted = CryptoJS.AES.decrypt(ciphertext, key, {
                iv: iv,
                padding: CryptoJS.pad.Pkcs7,
                mode: CryptoJS.mode.CBC
            });
            
            const decryptedText = decrypted.toString(CryptoJS.enc.Utf8);
            
            if (!decryptedText) {
                throw new Error("Mot de passe maître incorrect");
            }
            
            const data = JSON.parse(decryptedText);
            passwordText.textContent = data.password;
            
            const copyBtn = document.createElement('button');
            copyBtn.className = 'btn btn-sm btn-secondary ml-2';
            copyBtn.textContent = 'Copier';
            copyBtn.onclick = function() {
                copyToClipboard(data.password);
            };
            passwordText.parentNode.appendChild(copyBtn);
            
            masterPasswordInput.style.display = 'none';
            decryptBtn.style.display = 'none';
            
      
    });
});

    
</script>
@endsection
