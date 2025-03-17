<?php

namespace App\Jobs;

use App\Mail\ForgotMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ForgotJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $token;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param $email
     * @param $token
     * @param $user
     */
    public function __construct($email, $token, $user) {
        $this->email = $email;
        $this->token = $token;
        $this->user = $user;

        Log::debug($token);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        Mail::to($this->email)->send(new ForgotMail($this->token, $this->user));
    }
}
