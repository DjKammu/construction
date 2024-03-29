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
           $setting = \App\Models\Setting::latest()->first();

            if ($setting) {
                   $config = array(
                    'transport' => 'smtp',
                    'host'       => $setting->server_name,
                    'port'       => $setting->port,
                    'encryption' => $setting->mail_encryption,
                    'username'   => $setting->user_name,
                    'password'   => $setting->password
                  );
                  $fromConfig = array('address' => $setting->user_name, 'name' =>  env('MAIL_FROM_NAME', 'QPM CONSTRUCTION') );

                  Config::set('mail.mailers.smtp', $config);
                  Config::set('mail.from', $fromConfig);
            }
    }
}
