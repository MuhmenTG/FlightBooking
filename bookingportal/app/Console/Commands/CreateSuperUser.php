<?php

namespace App\Console\Commands;

use App\Models\UserAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperUser extends Command
{
    protected $signature = 'create:superuser';

    protected $description = 'Create a superuser';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
        $userAccount = new UserAccount();
        $userAccount->setPassword(Hash::make("Admin123"));
        $userAccount->setFirstName("Admin");
        $userAccount->setLastName("Adminsen");
        $userAccount->setEmail("admin@admin-nmflights.dk");
        $userAccount->setIsAgent(0);
        $userAccount->setIsAdmin(1);
        $userAccount->setStatus(1);
        $userAccount->save();

        $this->info('Superuser created successfully!');

    }
}
