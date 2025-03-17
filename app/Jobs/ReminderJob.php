<?php

namespace App\Jobs;

use App\Mail\ConfirmMail;
use App\Mail\ReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Yoyaku;

class ReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $booking_data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $booking_data)
    {
        $this->email = $email;
        $this->booking_data = $booking_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug("Dang gui mail");
        $yo = Yoyaku::where('booking_id', $this->booking_data->booking_id)->first();
        Log::debug($this->booking_data->booking_id);
        if(isset($yo->history_id)  === false && isset($yo->del_flg)  === false){
            Log::debug("Chua bi sua");
            Mail::to($this->email)->send(new ReminderMail($this->booking_data));
        } else {
            Log::debug("Da bi sua");
        }
    }
}
