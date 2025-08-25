@component('mail::message')
<div style="text-align:center; margin-bottom:20px;">
    <img src="../../../../public/images/logo.png" alt="SENA" width="120">
</div>

# ¡Hola!

Has solicitado restablecer tu contraseña.
Haz clic en el siguiente botón para continuar:

@component('mail::button', ['url' => $actionUrl, 'color' => 'success'])
Restablecer contraseña
@endcomponent

Si no realizaste esta solicitud, ignora este mensaje.

Gracias,<br>
**Equipo de soporte SENA-SSETP**
@endcomponent
