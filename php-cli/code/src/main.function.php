<?php

function main(string $configFileAddress) : string {
    $config = readConfig($configFileAddress);

    if(!$config){
        return handleError("Невозможно подключить файл настроек");
    }

    $functionName = parseCommand();

    if(function_exists($functionName)) {
        $result = $functionName($config);
    }
    else {
        $result = handleError("Вызываемая функция не существует");
    }

    return $result;
}

function parseCommand() : string {
    $functionName = 'helpFunction';
    
    if(isset($_SERVER['argv'][1])) {
        $functionName = match($_SERVER['argv'][1]) {
            'read-all' => 'readAllFunction',
            'add' => 'addFunction',
            'clear' => 'clearFunction',
            'read-profiles' => 'readProfilesDirectory',
            'read-profile' => 'readProfile',
            'search' => 'searchFunction',
            'delete' => 'deleteFunction',
            'help' => 'helpFunction',
            default => 'helpFunction'
        };
    }

    return $functionName;
}