<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeEmail;


class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $verificationCode;

    public function __construct($email, $verificationCode)
    {
        $this->email = $email;
        $this->verificationCode = $verificationCode;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new VerificationCodeEmail($this->verificationCode));
    }
}
