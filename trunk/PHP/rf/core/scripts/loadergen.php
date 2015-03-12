<?php
chdir(__DIR__);
chdir("../"); // go to the core/ directory

$source = '.';
$target = 'scripts/requires.php';

global $allPhpFiles, $classArray;
$allPhpFiles = array();
$classArray = array();

/**
 * Get all the PHP files inside a directory. No matter how deep they are
 * And store them in $allPhpFiles
 *
 * @param $directory string the path
 * @return void
 */
function _fr_findAllPhpFiles ($directory)
{
    $items = scandir($directory);
    global $allPhpFiles;
    foreach($items as $item)
    {
        if(in_array($item, array('.', '..')))
            continue;
        $path = $directory . DIRECTORY_SEPARATOR .$item;
        if(is_file($path))
        {
            if(substr($item, -4) === '.php')
            {
                $allPhpFiles []= $path;
            }
        }
        if(is_dir($path))
        {
            _fr_findAllPhpFiles($path);
        }
    }
}

/**
 * Get all the classes present in the filename and use them to populate
 * $classArray
 * @param $fileName
 * @return void
 */
function _fr_getAllClasses ($fileName)
{
    global $classArray;
    $tokens = token_get_all(file_get_contents($fileName));
    $state = 0; // looking for class keyword

    foreach($tokens as $token)
    {
        switch($state)
        {
            case 0:
                if($token[0] === T_CLASS || $token[0] === T_INTERFACE)
                {
                    $state = 1;
                }
                break;
            case 1:
                if($token[0] === T_STRING)
                {
                    $state = 0;
                    $name = $token[1];

                    $classArray [$name] = $fileName;
                }
                break;
        }
    }

}

// Find each PHP files in the given source directory
_fr_findAllPhpFiles($source);
foreach($allPhpFiles as $file)
{
    // get all the classes inside each file
    _fr_getAllClasses($file);
}

//
$requiresFile = '<?php global $frClasses; $frClasses = array('."\n";
$i = 0;
foreach($classArray as $className => $fileName)
{
    $i ++;
    $comma = ',';

    // don't add a comma for the last item
    if($i === count($classArray))
    {
        $comma = '';
    }
    // set the key-value format for the array
    $requiresFile = $requiresFile."'$className' => '$fileName'$comma\n";
}
$requiresFile = $requiresFile.");\n";

// delete the file if it exists
if(file_exists($target))
{
    unlink($target);
}

// write the generated PHP code into the target file
file_put_contents($target, $requiresFile);
