<?php

namespace VooDoo\User;


use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use VooDoo\User\Commands\CreateUser;

class VooDooUserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfigurations();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddlewares();

        $this->registerRoutes();

        $this->registerCommands();

        $this->publishConfiguration();

        $this->registerCreateUserQuestions();
    }

    public function registerCreateUserQuestions()
    {
        VooDooUser::askFor(function (Command $command, Model $user) {
            $user->name = $command->ask(__('Enter name of the user'));
        });

        VooDooUser::askFor(function (Command $command, Model $user) {
            $password = $command->ask(__('Enter password'));

            $user->password = bcrypt($password);
        });

    }

    public function publishConfiguration()
    {
        $this->publishes([
            VooDooUser::path('config/user.php') => config_path('voodoo.user'),
        ]);
    }

    /**
     * Registers Configuration.
     */
    public function registerConfigurations()
    {
        $this->mergeConfigFrom(
            VooDooUser::path('config/user.php'),
            'voodoo.user'
        );
    }

    protected function registerMiddlewares()
    {
    }

    protected function registerRoutes()
    {
    }

    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateUser::class,
            ]);
        }
    }
}
