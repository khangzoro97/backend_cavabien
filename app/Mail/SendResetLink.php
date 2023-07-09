<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link)
    {
        //
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reset-link')
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('【Carte】パスワードの再設定');
    }
}
