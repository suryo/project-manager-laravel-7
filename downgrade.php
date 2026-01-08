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
