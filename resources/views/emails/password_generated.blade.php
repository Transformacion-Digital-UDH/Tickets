<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta Creada</title>
</head>

<body>
    <h1>Hola {{ $user->name }},</h1>
    <p>Tu cuenta ha sido creada exitosamente. Aquí están tus credenciales de acceso:</p>
    <ul>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Contraseña:</strong> {{ $password }}</li>
    </ul>
    <p>Te recomendamos cambiar la contraseña una vez que inicies sesión.</p>
    <p>Saludos,<br>El equipo</p>
</body>

</html>
