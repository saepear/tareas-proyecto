<form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="tu@email.com" required>
        @error('email') <span>{{ $message }}</span> @enderror
    </div><br>

    <div>
        <label for="password">Contraseña:</label><br>
        <input type="password" id="password" name="password" placeholder="Tu contraseña" required>
        @error('password') <span>{{ $message }}</span> @enderror
    </div><br>

    <label>
        <input type="checkbox" name="remember"> Recordarme
    </label>

    <button type="submit">Iniciar Sesión</button>
    <a href="{{ route('register') }}">¿No tienes cuenta? Regístrate</a>
</form>