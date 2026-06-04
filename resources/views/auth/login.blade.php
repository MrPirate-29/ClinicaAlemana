@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="login-page">

    {{-- ══════════════ PANEL IZQUIERDO ══════════════ --}}
    <div class="login-left">
        <div class="login-left-content">

            <img src="{{ asset('images/logo-alemana.png') }}"
                 class="ll-logo"
                 alt="Logo Alemana Red de Salud"
                 onerror="this.style.display='none';">

            <div class="ll-badge">— Sistema Interno —</div>

            <h1 class="ll-title">Centro Médico</h1>
            <p class="ll-subtitle">Alemana Red de Salud</p>

            <div class="ll-divider"></div>

            <p class="ll-desc">
                Plataforma de gestión de consultas y seguimiento de pacientes
            </p>

            <div class="ll-location">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">location_on</span>
                Edif. Q Business Center, Piso 2 — Achumani, La Paz
            </div>

        </div>
    </div>

    {{-- ══════════════ PANEL DERECHO ══════════════ --}}
    <div class="login-right">
        <div class="login-card">

            <div class="lc-header">
                <h2 class="lc-title">Iniciar Sesión</h2>
                <p class="lc-sub">Acceso restringido al personal autorizado</p>
            </div>

            {{-- Errores inline (fallback si SweetAlert no carga) --}}
            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="login-form">
                @csrf

                {{-- Correo institucional --}}
                <div class="form-group">
                    <label for="coe_usu">Correo Institucional</label>
                    <input type="email"
                           id="coe_usu"
                           name="coe_usu"
                           value="{{ old('coe_usu') }}"
                           placeholder="correo@centromedicoalemana.com"
                           required
                           autofocus/>
                </div>

                {{-- Contraseña con toggle --}}
                <div class="form-group">
                    <label for="con_usu">Contraseña</label>
                    <div class="input-icon-wrap">
                        <input type="password"
                               id="con_usu"
                               name="con_usu"
                               placeholder="Ingrese su contraseña"
                               required/>
                        <button type="button" class="toggle-pass" id="togglePassword" title="Mostrar/ocultar contraseña">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Recordarme --}}
                <div class="remember-wrap">
                    <input type="checkbox" id="remember" name="remember" value="1"
                           {{ old('remember') ? 'checked' : '' }}/>
                    <label for="remember">Recordarme en este dispositivo</label>
                </div>

                {{-- Botón submit --}}
                <button type="submit" class="btn-submit">
                    <span class="material-symbols-outlined" style="font-size:1.1rem;">lock_open</span>
                    Iniciar Sesión
                </button>

            </form>

            <div class="link-back-wrap">
                <a href="{{ url('/') }}" class="link-back">← Volver al inicio</a>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Toggle visibilidad contraseña
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('con_usu');
    const eyeIcon   = toggleBtn.querySelector('i');

    toggleBtn.addEventListener('click', function () {
        const isPassword = passInput.getAttribute('type') === 'password';
        passInput.setAttribute('type', isPassword ? 'text' : 'password');
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });

    // SweetAlert para errores de validación
    @if ($errors->any())
        Swal.fire({
            icon:               'error',
            title:              '¡Acceso denegado!',
            text:               '{{ addslashes($errors->first()) }}',
            confirmButtonColor: '#014A8F',
            confirmButtonText:  'Intentar de nuevo',
        });
    @endif

    // SweetAlert para mensajes de sesión
    @if (session('success_alert'))
        Swal.fire({
            icon:               'success',
            title:              '¡Listo!',
            text:               '{{ addslashes(session("success_alert")) }}',
            confirmButtonColor: '#835500',
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon:               'error',
            title:              'Error',
            text:               '{{ addslashes(session("error")) }}',
            confirmButtonColor: '#014A8F',
        });
    @endif
</script>
@endsection
