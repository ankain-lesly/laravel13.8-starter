<?php

namespace App\Providers;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mime\Address;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->environment(['local', 'development'])) {
            return;
        }

        if (config('mail.mailers.'.config('mail.default').'.transport') === 'log') {
            config(['mail.mailers.log.channel' => 'null']);
        }

        Event::listen(MessageSent::class, function (MessageSent $event): void {
            static $loggedMessages;

            $loggedMessages ??= new \WeakMap;
            $message = $event->message;

            if (isset($loggedMessages[$message])) {
                return;
            }

            $loggedMessages[$message] = true;

            $addresses = static fn (?array $list): array => array_map(
                fn (Address $recipient) => $recipient->getAddress(),
                $list ?? [],
            );

            $payload = [
                'subject' => $message->getSubject(),
                'from' => $addresses($message->getFrom()),
                'to' => $addresses($message->getTo()),
                'cc' => $addresses($message->getCc()),
                'bcc' => $addresses($message->getBcc()),
                'text' => (string) ($message->getTextBody() ?? ''),
                'html' => (string) ($message->getHtmlBody() ?? ''),
            ];

            Log::channel('stderr')->info('Email sent', $payload);
        });
    }
}
