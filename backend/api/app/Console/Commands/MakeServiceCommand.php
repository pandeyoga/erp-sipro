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
        $inputPath = str_replace('\\', '/', $this->argument('name')); // Normalize slash
        $parts = explode('/', $inputPath);
        $className = array_pop($parts); // Ambil nama class
        $subPath = implode('/', $parts); // Path subfolder
        $namespacePart = implode('\\', $parts); // Namespace bagian

        $basePath = app_path('Services');
        $fullDirPath = $subPath ? $basePath . '/' . $subPath : $basePath;
        $filePath = "{$fullDirPath}/{$className}.php";

        if (File::exists($filePath)) {
            $this->error("Service {$className} sudah ada!");
            return Command::FAILURE;
        }

        // Buat direktori jika belum ada
        if (!File::exists($fullDirPath)) {
            File::makeDirectory($fullDirPath, 0755, true);
        }

        $namespace = 'App\\Services' . ($namespacePart ? "\\{$namespacePart}" : '');

        $stub = <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    //
}
PHP;

        File::put($filePath, $stub);
        $this->info("Service [{$filePath}] created successfully!");

        return Command::SUCCESS;
    }
}
