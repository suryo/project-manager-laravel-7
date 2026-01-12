<?php

function downgradeMigrations($dir) {
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        
        // Convert anonymous migration to class
        // Pattern: return new class extends Migration
        if (strpos($content, 'return new class extends Migration') !== false) {
            $basename = basename($file, '.php');
            // Remove date prefix (YYYY_MM_DD_HHMMSS_)
            $parts = explode('_', $basename);
            $classNameParts = array_slice($parts, 4); 
            $className = implode('', array_map('ucfirst', $classNameParts));
            
            // Check if class name starts with number (unlikely but possible if bad naming)
            if (is_numeric(substr($className, 0, 1))) {
                $className = 'Class' . $className;
            }

            echo "Converting $basename to class $className\n";

            $content = str_replace('return new class extends Migration', "class $className extends Migration", $content);
            
            // Remove the semi-colon at the end of the anonymous class definition
            // It usually ends with "};" or "};" with newlines.
            // We need to match the LAST "};"
            $content = preg_replace('/};[\s]*$/', '}', $content);
        }

        // Remove : void return types (optional, but safer for very old PHP or if interface mismatch)
        // Laravel 7 migrations usually don't have types strictly enforced in base class stub, but PHP 7.2 supports void.
        // We will keep void if it works, but some older L7 stubs didn't have it. 
        // L7 Migration::up/down do NOT define return types in the abstract class, so keeping ": void" might cause
        // "Declaration of Up MUST be compatible with Migration::up" if Migration::up doesn't have it?
        // Actually Migration::up is often not defined in base or is abstract.
        // Let's remove ": void" just to be safe and standard L7 style.
        $content = str_replace(': void', '', $content);

        file_put_contents($file, $content);
    }
}

function downgradeModels($dir) {
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);
        
        // Remove HasFactory
        $content = str_replace("use Illuminate\Database\Eloquent\Factories\HasFactory;\n", '', $content);
        $content = str_replace("use Illuminate\Database\Eloquent\Factories\HasFactory;", '', $content);
        $content = str_replace("    use HasFactory;\n", '', $content);
        $content = str_replace("    use HasFactory;", '', $content);

        // Remove Typed Properties: public int $id; -> public $id;
        // Basic regex for public/protected/private typed properties
        // Matches: visibility type $name
        $content = preg_replace('/(public|protected|private)\s+[a-zA-Z0-9_]+\s+(\$[a-zA-Z0-9_]+)/', '$1 $2', $content);

        // Remove Enums (if any simple usage, complex usage needs manual fix)
        // This script can't fix logic, just syntax.

        file_put_contents($file, $content);
    }
}

echo "Downgrading Migrations...\n";
downgradeMigrations(__DIR__ . '/database/migrations');

echo "Downgrading Models...\n";
downgradeModels(__DIR__ . '/app/Models');

// Also downgrade App root models if any
downgradeModels(__DIR__ . '/app');

echo "Done.\n";

function fixKernel($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    // Rename middlewareAliases to routeMiddleware
    $content = str_replace('protected $middlewareAliases = [', 'protected $routeMiddleware = [', $content);
    // Fix Signed Middleware alias
    $content = str_replace("'signed' => \App\Http\Middleware\ValidateSignature::class,", "'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,", $content);
    // Fix Middleware Classes
    $content = str_replace('\Illuminate\Http\Middleware\HandleCors::class', '\Fruitcake\Cors\HandleCors::class', $content);
    $content = str_replace('\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class', '\App\Http\Middleware\CheckForMaintenanceMode::class', $content);
    file_put_contents($path, $content);
    echo "Fixed Kernel.php\n";
}

function fixRouteServiceProvider($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    // Remove ->namespace($this->namespace)
    $content = str_replace("->namespace(\$this->namespace)", "", $content);
    // Remove configureRateLimiting check/usage if checking for specific lines simpler?
    // Doing simple string replacement for now.
    file_put_contents($path, $content);
    echo "Fixed RouteServiceProvider.php\n";
}

function fixTrustProxies($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    $content = str_replace('use Illuminate\Http\Middleware\TrustProxies;', 'use Fideloper\Proxy\TrustProxies;', $content);
    file_put_contents($path, $content);
    echo "Fixed TrustProxies.php\n";
}

function fixAppServiceProvider($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    $content = str_replace('Paginator::useBootstrapFive();', '// Paginator::useBootstrapFive();', $content);
    file_put_contents($path, $content);
    echo "Fixed AppServiceProvider.php\n";
}

function fixBladeFiles($dir) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->getExtension() === 'php') {
            $content = file_get_contents($file);
            $changed = false;
            // Replace @vite
            if (strpos($content, '@vite') !== false) {
                $content = preg_replace('/@vite\(\[.*?\]\)/s', '<link href="{{ mix(\'css/app.css\') }}" rel="stylesheet">' . "\n" . '    <script src="{{ mix(\'js/app.js\') }}" defer></script>', $content);
                $changed = true;
            }
            // Replace match (simple case) - VERY basic
            if (strpos($content, 'match($approval->status)') !== false) {
                 $content = str_replace(
                    '$statusIcon = match($approval->status) {', 
                    '$statusIcon = "bi-clock-fill text-warning"; if($approval->status=="approved") $statusIcon="bi-check-circle-fill text-success"; elseif($approval->status=="rejected") $statusIcon="bi-x-circle-fill text-danger"; // match($approval->status) {', 
                    $content
                );
                 // Comment out the closing brace of match if possible, this is risky.
                 // Let's rely on manual fix for verified complex Blade files.
                 // Or just Replace the known block entirely if it matches exact string.
            }
            
            if ($changed) {
                file_put_contents($file, $content);
                echo "Fixed Blade: " . $file->getFilename() . "\n";
            }
        }
    }
}

echo "Applying additional fixes...\n";
fixKernel(__DIR__ . '/app/Http/Kernel.php');
fixRouteServiceProvider(__DIR__ . '/app/Providers/RouteServiceProvider.php');
fixTrustProxies(__DIR__ . '/app/Http/Middleware/TrustProxies.php');
fixAppServiceProvider(__DIR__ . '/app/Providers/AppServiceProvider.php');

function fixConsoleRoutes($path) {
    if (!file_exists($path)) return;
    $content = file_get_contents($path);
    $content = str_replace('->purpose(', '->describe(', $content);
    file_put_contents($path, $content);
    echo "Fixed routes/console.php\n";
}
fixConsoleRoutes(__DIR__ . '/routes/console.php');

echo "Fixing Blade files...\n";
fixBladeFiles(__DIR__ . '/resources/views');
echo "Additional fixes applied.\n";
