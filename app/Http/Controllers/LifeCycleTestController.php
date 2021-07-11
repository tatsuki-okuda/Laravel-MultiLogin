<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LifeCycleTestController extends Controller
{

    public function serviceprovidertest()
    {
        // プロバイダーで登録しておくとコントローラーなどいろんなところで使える様になる。
        // 登録してあるencryptプロバイダの使用例
        $encrypt = app()->make('encrypter');
        $password = $encrypt->encrypt('password');
        // 登録したサービスプロバイダーの呼び出し。
        $sample = app()->make('ServiceProviderTest');
        
        // サービスコンテナで登録したものだと存在しないとエラーになってしまう。
        // $test = app()->make('lifeCycleTest');

        dd($sample, $password, $encrypt->decrypt($password));
    }

    public function showServiceContainerTest()
    {
        // サービスコンテナへの登録。
        app()->bind('lifeCycleTest', function(){
            return 'lifeCycleTest';
        });
        $test = app()->make('lifeCycleTest');

        // サービスコンテナなし
        // $message = new Message;
        // $sample = new Sample($message);
        // $sample->run();
        
        // サービスコンテンアを使うパターン
        app()->bind('Sample', Sample::class);
        $sample = app()->make('Sample');
        $sample->run();

        // サービスプロバイダなのでここでも使える。
        $sample = app()->make('ServiceProviderTest');

        dd($sample, $test, app());
    }
}

class Sample
{
    public $message;
    // diで引数にクラスを指定することで自動でインスタンス化してくれる。
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function run()
    {
        $this->message->send();
    }
}

class Message
{
    public function send()
    {
        echo 'メッセージ表示';
    }
}