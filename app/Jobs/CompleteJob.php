<?php

namespace App\Jobs;

use App\Mail\CompleteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CompleteJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user_data;
    protected $email;

    /**
     * Create a new job instance.
     *
     * @param $email
     * @param $user_data
     */
    public function __construct($email, $user_data) {
        $this->email = $email;
        $this->user_data = $user_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        Mail::to($this->email)->send(new CompleteMail($this->user_data));
    }
}
