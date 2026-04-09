<form method="POST" action="{{ route('register') }}">
    @csrf

    <div>
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Tu nombre" required>
        @error('nombre') <span>{{ $message }}</span> @enderror
    </div><br>

    <div>
        <label for="apellido">Apellido:</label><br>
        <input type="text" id="apellido" name="apellido" value="{{ old('apellido') }}" placeholder="Tu apellido"
            required>
        @error('apellido') <span>{{ $message }}</span> @enderror
    </div><br>

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
    <div>
        <label for="password_confirmation">Confirmar Contraseña:</label><br>
        <input type="password" id="password_confirmation" name="password_confirmation"
            placeholder="Confirma tu contraseña" required>
    </div><br>

    <button type="submit">Registrarse</button>
    <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
</form>