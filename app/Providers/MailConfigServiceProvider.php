<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Config;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
           $setting = \App\Models\Setting::first();

            if (@$setting != null) {
                   $config = array(
                    'transport' => 'smtp',
                    'host'       => $setting->server_name,
                    'port'       => $setting->port,
                    'encryption' => $setting->mail_encryption,
                    'username'   => $setting->user_name,
                    'password'   => $setting->password,
                    'timeout' => null,
                    'auth_mode' => null,
                  );
                  $fromConfig = array('address' => $setting->from_email, 'name' =>  env('MAIL_FROM_NAME', 'QPM CONSTRUCTION') );

                  Config::set('mail.mailers.smtp', $config);
                  Config::set('mail.from', $fromConfig);
            }
            else{
                
                $config =  [
                        'transport' => 'smtp',
                        'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
                        'port' => env('MAIL_PORT', 587),
                        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                        'username' => env('MAIL_USERNAME'),
                        'password' => env('MAIL_PASSWORD'),
                        'timeout' => null,
                        'auth_mode' => null,
                    ];

                  $fromConfig =  [
                            'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                            'name' => env('MAIL_FROM_NAME', 'Example'),
                        ];

                  Config::set('mail.mailers.smtp', $config);
                  Config::set('mail.from', $fromConfig);

            }
    }
}
