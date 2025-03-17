<?php

namespace App\Jobs;

use App\Mail\ConfirmMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ConfirmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $booking_data;

    /**
     * Create a new job instance.
     *
     * @param $email
     * @param $booking_data
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
        Mail::to($this->email)->send(new ConfirmMail($this->booking_data));
    }
}
