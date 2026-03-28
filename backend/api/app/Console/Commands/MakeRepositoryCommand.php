<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inputPath = str_replace('\\', '/', $this->argument('name')); // Normalize slashes
        $parts = explode('/', $inputPath);
        $className = array_pop($parts); // Get the actual class name
        $subPath = implode('/', $parts);
        $namespacePart = implode('\\', $parts); // For use in namespace

        $basePath = app_path('Repositories');
        $fullDirPath = $subPath ? $basePath . '/' . $subPath : $basePath;
        $filePath = "{$fullDirPath}/{$className}.php";

        if (File::exists($filePath)) {
            $this->error("Repository {$className} sudah ada!");
            return Command::FAILURE;
        }

        // Buat folder jika belum ada
        if (!File::exists($fullDirPath)) {
            File::makeDirectory($fullDirPath, 0755, true);
        }

        $namespace = 'App\\Repositories' . ($namespacePart ? "\\{$namespacePart}" : '');

        $stub = <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    //
}
PHP;

        File::put($filePath, $stub);
        $this->info("Repository [{$filePath}] created successfully!");

        return Command::SUCCESS;
    }
}
