<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'make:repository {name}';
    
    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Create a new service class';
    
    /**
    * Execute the console command.
    */
    public function handle()
    {
        $name = $this->argument('name');
        $serviceName = ucfirst($name);
        $path = app_path("Repositories/{$serviceName}.php");
        
        if (File::exists($path)) {
            $this->error("Service {$serviceName} sudah ada!");
            return Command::FAILURE;
        }
        
        // Pastikan folder Repositories ada
        if (!File::exists(app_path('Repositories'))) {
            File::makeDirectory(app_path('Repositories'), 0755, true);
        }
        
        $stub = <<<PHP
                <?php
                        
                namespace App\Repositories;
                        
                class {$serviceName}
                {
                    //
                }
                PHP;
        File::put($path, $stub);
        $this->info("Service [app/Repositories/{$serviceName}.php] created successfully!");
        
        return Command::SUCCESS;
    }
}
