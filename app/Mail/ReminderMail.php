<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable {
    use Queueable, SerializesModels;
    private $booking_data;
    /**
     * Create a new message instance.
     *
     * @param $booking_data
     */
    public function __construct($booking_data) {
        $this->booking_data = $booking_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $booking_data = $this->booking_data;
        return $this->text('sunsun.mails.booking.reminder')
            ->subject('【ぬか酸素Sun燦】ご予約日のお知らせ')
            ->with(
                [
                    'booking_data' => $booking_data
                ]);
    }
}
