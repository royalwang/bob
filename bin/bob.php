<?php

namespace Bobfile
{
    $GLOBALS['_bob'] = array(
        'tasks' => array(),
        'descriptions' => array()
    );

    function task($name, $callback)
    {
        global $_bob;
        $_bob['tasks'][$name] = $callback;
    }

    function desc($text)
    {
        global $_bob;
        $_bob['descriptions'][count($_bob['tasks'])] = $text;
    }
}

namespace Bob
{
    function printLn($line)
    {
        echo "[bob] $line\n";
    }

    function tasks($definition)
    {
        if (!file_exists($definition)) {
            printLn(sprintf('Error: No Bobfile found in %s', $definition));
            exit(1);
        }

        include $definition;
        return $GLOBALS['_bob']['tasks'];
    }

    function listTasks($tasks, $descs)
    {
        $i = 0;
        foreach ($tasks as $name => $task) {
            $desc = isset($descs[$i]) ? $descs[$i] : '';
            echo "$name";
            if ($desc) echo ": $desc";
            echo "\n";
            ++$i;
        }
    }

    function execute($tasks, $name)
    {
        if (!isset($tasks[$name])) {
            printLn(sprintf('Task "%s" not found', $name));
            exit(1);
        }

        $task = $tasks[$name];

        printLn(sprintf('Running Task "%s"', $name));
        $start = microtime(true);

        call_user_func($task, $tasks);

        printLn(sprintf('Finished in %f seconds', microtime(true) - $start));
    }

    /*
     * Main Script
     */
    $cwd = $_SERVER['PWD'];
    $tasks = tasks("$cwd/Bobfile");
    $ARGV = $_SERVER['argv'];

    array_shift($ARGV);

    // Run first defined task if called without arguments
    if (empty($ARGV)) {
        execute($tasks, key($tasks));
        exit(0);
    }

    switch ($ARGV[0]) {
        case '-t':
            listTasks($tasks, $GLOBALS['_bob']['descriptions']);
            exit(0);
        default:
            execute($tasks, $ARGV[0]);
    }
}

