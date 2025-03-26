@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Mes mots de passe</span>
                    <a href="{{ route('passwords.create') }}" class="btn btn-primary btn-sm">
                        Ajouter un mot de passe
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($passwords->isEmpty())
                        <div class="text-center p-5">
                            <p>Vous n'avez pas encore de mots de passe enregistrés.</p>
                            <a href="{{ route('passwords.create') }}" class="btn btn-primary">
                                Ajouter votre premier mot de passe
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Site web</th>
                                        <th>Dernière utilisation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($passwords as $password)
                                        <tr>
                                            <td>{{ $password->titre }}</td>
                                            <td>
                                                @if ($password->site_web)
                                                    <a href="{{ $password->site_web }}" target="_blank">
                                                        {{ $password->site_web }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $password->last_used ? $password->last_used->diffForHumans() : '-' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('passwords.show', $password->id) }}" class="btn btn-sm btn-info">
                                                        Voir
                                                    </a>
                                                    <a href="{{ route('passwords.edit', $password) }}" class="btn btn-sm btn-warning">
                                                        Modifier
                                                    </a>
                                                    <form action="{{ route('passwords.destroy', $password) }}" method="POST" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce mot de passe?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
