<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ThanksMail extends Mailable
{
    use Queueable, SerializesModels;

    // 引数を受け取る為に変数を定義する。
    public $products;
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($products, $user)
    {
        //受け取った引数を定義
        $this->products = $products;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.thanks')
        ->subject('ご購入ありがとうございます。');
    }
}
