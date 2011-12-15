<?php

namespace Bob;

// Your package's name. Used to identify it on Packagist.org
function getName()
{
    return 'chh/bob';
}

// Should return the package's version.
//
// An idea would be to fetch the latest tag from the repo 
// in your favourite VCS.
function getVersion()
{
    return "master-dev";
}

// Should return an array of authors.
// Each individual author should be an array of `name`, 
// `email` and optionally a `homepage`
//
// By default this parses an `AUTHORS.txt` file in the root
// of the project which is formatted with an author name on
// each line. For example:
//
//     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
//     John Doe <john@example.com>
//
function getAuthors()
{
    $authorsFile = __DIR__.'/AUTHORS.txt';

    if (!file_exists($authorsFile)) {
        return array();
    }

    $authors = array();

    foreach (new \SplFileObject($authorsFile) as $line) {
        if (preg_match('/^(.+) <(.+)>$/', $line, $matches)) {
            $authors[] = array(
                'name' => $matches[1],
                'email' => $matches[2]
            );
        }
    }

    return $authors;
}

function getExecutables()
{
    $binDir = __DIR__.'/bin';

    if (!is_dir($binDir)) {
        return array();
    }

    $executables = array();

    foreach (new \DirectoryIterator($binDir) as $file) {
        if ($file->isFile() and $file->isExecutable()) {
            $logicalPath = substr($file->getRealpath(), strlen(__DIR__) + 1);
            $executables[] = $logicalPath;
        }
    }

    return $executables;
}

desc('Creates a composer.json');
task('composer:manifest', function() {
    $NAME = getName();
    $AUTHORS = getAuthors();
    $EXECUTABLES = getExecutables();
    $VERSION = getVersion();

    $pkg = include(__DIR__.'/composer_spec.php');

    if (!is_array($pkg)) {
        printLn('ERROR: composer_spec.php MUST return an array');
        exit(1);
    }

    $json = json_encode($pkg, JSON_PRETTY_PRINT);

    printLn('Writing composer.json');
    @file_put_contents(__DIR__.'/composer.json', $json);
});

desc('Publishes the composer package on Packagist.org');
task('composer:publish', function() {

});