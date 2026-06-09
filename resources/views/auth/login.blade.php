<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — FinControl</title>
    <meta name="description" content="Acesse o sistema de gestão financeira FinControl">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <div class="login-logo">
                <i class="ti ti-building-bank"></i>
                <h1>FinControl</h1>
                <p>{{ __('Gestão financeira empresarial') }}</p>
            </div>
            <div class="card">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="email">{{ __('E-mail') }}</label>
                        <input type="email" id="email" name="email" placeholder="seu@email.com.br"
                               value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">{{ __('Senha') }}</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                        <label style="font-size:12px;display:flex;align-items:center;gap:6px;cursor:pointer">
                            <input type="checkbox" name="remember" style="width:auto" {{ old('remember') ? 'checked' : '' }}>
                            {{ __('Lembrar-me') }}
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                        <i class="ti ti-login"></i>{{ __('Entrar') }}
                    </button>
                </form>
            </div>
            <div style="text-align:center;font-size:11px;color:var(--color-text-tertiary);margin-top:12px">
                {{ __('RF01 — Autenticação por e-mail e senha') }}
            </div>
        </div>
    </div>
</body>
</html>
