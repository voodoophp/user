<?php

namespace VooDoo\User\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use VooDoo\User\VooDooUser;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'voodoo:users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Creates a new user.';

    /**
     * @var Collection
     */
    protected $questionsToAsk;

    /** @var Model */
    protected $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $user_email = $this->askForUserEmail();

        if ($user = $this->ifUserWithEmailExists($user_email)) {
            if ($this->confirm(__("A User with this email name already exists. Do you want to update the user details?"))) {
                $this->user = $user;
            } else {
                return 1;
            }
        } else {
            /** @var Model $userModel */
            $userModel = config('voodoo.user.model');
            $this->user = new $userModel();
        }

        $this->user->email = $user_email;

        $this->fillUserDetails();

        $this->saveUser();

        return 0;
    }

    /**
     * @param Model $user
     */
    protected function fillUserDetails()
    {
        $this->questionsToAsk = collect(VooDooUser::$questionsCallBacks);

        $this->questionsToAsk->each(function ($question) {
            $question($this, $this->user);
        });
    }

    /**
     * Saves user data.
     */
    protected function saveUser()
    {
        if ($this->confirm(__('Do you want to save the user details?'))) {
            $this->info(__('Saving user details.'));

            $this->user->email_verified_at = Carbon::now();

            $this->user->save();

            $this->info(__('User details saved successfully.'));
        }
    }

    /**
     * @return string
     */
    protected function askForUserEmail()
    {
        do {
            $user_email = $this->ask(__('Enter user email'));

            $validator = Validator::make([
                'user_email' => $user_email
            ], [
                'user_email' => 'email'
            ]);

            if ($validator->fails()) {
                $this->error(__('The email you entered is not valid. Please enter a valid email.'));
            }
        } while ($validator->fails());

        return $user_email;
    }

    /**
     * @param string $email
     */
    protected function ifUserWithEmailExists(string $email)
    {
        /** @var Model $userModel */
        $userModel = config('voodoo.user.model');

        return $userModel::where('email', $email)->first();
    }
}
