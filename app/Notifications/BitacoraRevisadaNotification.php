<?php

namespace App\Notifications;

use App\Models\BitacoraUpload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BitacoraRevisadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bitacora;
    protected $estado;
    protected $observaciones;

    /**
     * Create a new notification instance.
     */
    public function __construct(BitacoraUpload $bitacora, string $estado, string $observaciones = null)
    {
        $this->bitacora = $bitacora;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
                    ->subject('Bitácora Revisada - SSETP')
                    ->greeting('Hola!');

        if ($this->estado === 'aceptado') {
            $message->line("Tu bitácora #{$this->bitacora->numero_bitacora} ha sido ACEPTADA.")
                   ->line('¡Felicitaciones! Tu trabajo cumple con los requerimientos.')
                   ->action('Ver Mis Bitácoras', url('/admin/mis-bitacoras'));
        } else {
            $message->line("Tu bitácora #{$this->bitacora->numero_bitacora} ha sido DEVUELTA para correcciones.")
                   ->line('Observaciones del instructor:')
                   ->line($this->observaciones ?? 'Sin observaciones específicas')
                   ->line('Por favor, realiza las correcciones necesarias y vuelve a subir tu bitácora.')
                   ->action('Ver Mis Bitácoras', url('/admin/mis-bitacoras'));
        }

        return $message->line('Gracias por usar SSETP!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = $this->estado === 'aceptado' ? 'Bitácora Aceptada' : 'Bitácora Devuelta';
        $message = $this->estado === 'aceptado' 
            ? "Tu bitácora #{$this->bitacora->numero_bitacora} ha sido aceptada"
            : "Tu bitácora #{$this->bitacora->numero_bitacora} necesita correcciones";

        return [
            'title' => $title,
            'message' => $message,
            'numero_bitacora' => $this->bitacora->numero_bitacora,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'url' => '/admin/mis-bitacoras',
        ];
    }
}