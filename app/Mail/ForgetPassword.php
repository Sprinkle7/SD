<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;
    private $token;
    private $name;
    private $language;

    /**
    * Create a new message instance.
    *
    * @return void
    */
    public function __construct($token, $language, $name)
    {
        $this->token = $token;
        $this->language = $language;
        $this->name = $name;
    }

    /**
    * Build the message.
    *
    * @return $this
    */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'),'Shop | sevendisplays.com')->subject('Passwort zurücksetzen | sevendisplays.com')->view('mails.auth.forgetPassword')->with(['token' => $this->token, 'language' => $this->language, 'name' => $this->name]);
    }
}