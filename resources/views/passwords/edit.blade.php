@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Modifier le mot de passe</div>

                <div class="card-body">
                    <div id="password-form-app">
                        <password-form 
                            @save="updatePassword"
                            :edit-mode="true"
                            :password-data="{{ json_encode($password) }}"
                        ></password-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const app = new Vue({
            el: '#password-form-app',
            methods: {
                updatePassword(form) {
                    // Créer un formulaire pour soumettre les données
                    const submitForm = document.createElement('form');
                    submitForm.action = "{{ route('passwords.update', $password) }}";
                    submitForm.method = 'POST';
                    submitForm.style.display = 'none';

                    // Ajouter le token CSRF et la méthode PUT
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = "{{ csrf_token() }}";
                    submitForm.appendChild(csrfToken);

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'PUT';
                    submitForm.appendChild(methodField);

                    // Ajouter chaque champ du formulaire
                    for (const key in form) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = form[key];
                        submitForm.appendChild(input);
                    }

                    // Ajouter le formulaire au document et le soumettre
                    document.body.appendChild(submitForm);
                    submitForm.submit();
                }
            }
        });
    });
</script>
@endsection
