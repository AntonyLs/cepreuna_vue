@extends('layouts.web')
@section('titulo', 'Bienvenido | CepreUNA')
@section('css')

<style type="text/css">
    .comunicado {
        padding: 20px 30px;
        border: 1px solid #989898;
        border-radius: 15px;
        margin-bottom: 20px;
        font-size: 15px;
    }

    .comunicado p {
        margin-bottom: 6px;
        text-align: justify;
    }

    .imagen {
        margin-left: auto;
        margin-right: auto;
        width: 60%;
    }

    .contenido {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
    }

    .card {
        width: 22rem;
        padding: 20px;
        display: flex;
        justify-content: center;
        text-align: center;
    }

    h8 {
        margin: 0 10px;
        color: gray;
    }

    .formulario {
        justify-content: flex-start;
    }

    .password-container {
        position: relative;
    }

    .password-container .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }
</style>

@endsection
@section('content')
<div class="contenido mt-5">
    <div class="card">
        <h1>Inicio de Sesión</h1>
        <div class="d-flex flex-row justify-content-center">
            <!-- <img src="https://cepreuna.edu.pe/assets/images/logo/logo.png" width="30%" alt="" /> -->
        </div>
        <div class="card-body text-left">
            <form method="POST" action="{{ route('loginSimulacro') }}">
                @csrf
                <!-- Agrega tus campos de formulario, por ejemplo: -->
                <label for="email">Correo Institucional:</label>
                <input type="email" class="form-control" name="email" placeholder="Ingrese e-mail del CEPREUNA" required>

                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese contraseña" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility()">
                        <i class="fa fa-eye" id="eye"></i>
                    </span>
                </div>
                <button type="submit" class="btn btn-primary form-control my-3 px-5" style="background: orangered; color: white">
                    Iniciar sesión
                </button>
            </form>
        </div>
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')

<script>
    function togglePasswordVisibility() {
        var passwordInput = document.getElementById('password');
        var eyeIcon = document.getElementById('eye');

        passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        eyeIcon.className = passwordInput.type === 'password' ? 'fa fa-eye' : 'fa fa-eye-slash';
    }
</script>

@endsection