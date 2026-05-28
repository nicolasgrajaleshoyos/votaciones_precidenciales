<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:fetch-polls')]
#[Description('Command description')]
class FetchPolls extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
