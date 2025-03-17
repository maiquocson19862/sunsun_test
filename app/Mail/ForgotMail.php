<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotMail extends Mailable {
    use Queueable, SerializesModels;
    protected $token;
    protected $user;

    /**
     * Create a new message instance.
     *
     * @param $token
     * @param $user
     */
    public function __construct($token, $user) {
        $this->token = $token;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $app_url = config('app.url');
        return $this->text('sunsun.mails.user.forgot')
            ->subject('【ぬか酸素Sun燦】パスワード変更受付')
            ->with(
                [
                    'token' => $this->token,
                    'user' => $this->user,
                    'app_url' => $app_url
                ]);
    }
}
