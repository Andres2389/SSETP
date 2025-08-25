<?php

namespace App\Notifications;

use App\Models\EtapaProductiva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BitacoraSubidaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $etapaProductiva;
    protected $numeroBitacora;

    /**
     * Create a new notification instance.
     */
    public function __construct(EtapaProductiva $etapaProductiva, int $numeroBitacora)
    {
        $this->etapaProductiva = $etapaProductiva;
        $this->numeroBitacora = $numeroBitacora;
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
        return (new MailMessage)
                    ->subject('Nueva Bitácora para Revisar - SSETP')
                    ->greeting('Hola!')
                    ->line("El aprendiz {$this->etapaProductiva->nombre_completo} ha subido una nueva bitácora para revisión.")
                    ->line("Bitácora #: {$this->numeroBitacora}")
                    ->line("Ficha: {$this->etapaProductiva->ficha->numero}")
                    ->action('Revisar Bitácoras', url('/admin/revisar-bitacoras'))
                    ->line('Gracias por usar SSETP!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nueva Bitácora para Revisar',
            'message' => "El aprendiz {$this->etapaProductiva->nombre_completo} ha subido la bitácora #{$this->numeroBitacora}",
            'aprendiz' => $this->etapaProductiva->nombre_completo,
            'ficha' => $this->etapaProductiva->ficha->numero,
            'numero_bitacora' => $this->numeroBitacora,
            'url' => '/admin/revisar-bitacoras',
        ];
    }
}