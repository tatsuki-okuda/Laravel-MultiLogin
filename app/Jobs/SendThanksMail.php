<?php

namespace App\Jobs;

use App\Mail\TestMail;
use App\Mail\ThanksMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendThanksMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user)
        ->send(new ThanksMail($this->products, $this->user));
    }
}
