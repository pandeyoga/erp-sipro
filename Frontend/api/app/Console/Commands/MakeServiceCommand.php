<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'make:service {name}';
    
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
        $path = app_path("Services/{$serviceName}.php");
        
        if (File::exists($path)) {
            $this->error("Service {$serviceName} sudah ada!");
            return Command::FAILURE;
        }
        
        // Pastikan folder Services ada
        if (!File::exists(app_path('Services'))) {
            File::makeDirectory(app_path('Services'), 0755, true);
        }
        
        $stub = <<<PHP
                <?php
                        
                namespace App\Services;
                        
                class {$serviceName}
                {
                    //
                }
                PHP;
        File::put($path, $stub);
        $this->info("Service [app/Services/{$serviceName}.php] created successfully!");
        
        return Command::SUCCESS;
    }
}
