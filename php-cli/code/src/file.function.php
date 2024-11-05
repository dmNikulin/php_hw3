<?php

// function readAllFunction(string $address) : string {
function readAllFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "rb");
        
        $contents = ''; 
    
        while (!feof($file)) {
            $contents .= fread($file, 100);
        }
        
        fclose($file);
        return $contents;
    }
    else {
        return handleError("Файл не существует");
    }
}

// function addFunction(string $address) : string {
function addFunction(array $config) : string {
    $address = $config['storage']['address'];

    $name = readline("Введите имя: ");
    $date = readline("Введите дату рождения в формате ДД-ММ-ГГГГ: ");
    if (!validate($date)) {
        return handleError("Неправильный формат даты!");
    }
    $data = $name . ", " . $date . "\r\n";

    $fileHandler = fopen($address, 'a');

    if(fwrite($fileHandler, $data)){
        return "Запись $data добавлена в файл $address"; 
    }
    else {
        return handleError("Произошла ошибка записи. Данные не сохранены");
    }

    fclose($fileHandler);
}

// function clearFunction(string $address) : string {
function clearFunction(array $config) : string {
    $address = $config['storage']['address'];

    if (file_exists($address) && is_readable($address)) {
        $file = fopen($address, "w");
        
        fwrite($file, '');
        
        fclose($file);
        return "Файл очищен";
    }
    else {
        return handleError("Файл не существует");
    }
}

function helpFunction() {
    return handleHelp();
}

function readConfig(string $configAddress): array|false{
    return parse_ini_file($configAddress, true);
}

function readProfilesDirectory(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!is_dir($profilesDirectoryAddress)){
        mkdir($profilesDirectoryAddress);
    }

    $files = scandir($profilesDirectoryAddress);

    $result = "";

    if(count($files) > 2){
        foreach($files as $file){
            if(in_array($file, ['.', '..']))
                continue;
            
            $result .= $file . "\r\n";
        }
    }
    else {
        $result .= "Директория пуста \r\n";
    }

    return $result;
}

function readProfile(array $config): string {
    $profilesDirectoryAddress = $config['profiles']['address'];

    if(!isset($_SERVER['argv'][2])){
        return handleError("Не указан файл профиля");
    }

    $profileFileName = $profilesDirectoryAddress . $_SERVER['argv'][2] . ".json";

    if(!file_exists($profileFileName)){
        return handleError("Файл $profileFileName не существует");
    }

    $contentJson = file_get_contents($profileFileName);
    $contentArray = json_decode($contentJson, true);

    $info = "Имя: " . $contentArray['name'] . "\r\n";
    $info .= "Фамилия: " . $contentArray['lastname'] . "\r\n";

    return $info;
}

function searchFunction(array $config): string {
    $address = $config['storage']['address'];
    echo "Ищем именниников на текущую дату " . date("d.m") . "\r\n ";

    $users = [];

    if (file_exists($address)) {
        $file = fopen($address, "rb");

        while(!feof($file)){
            $user = fgets($file);
            $userData = explode(',', $user);

            if (isset($userData[1])) {
                $dateData = explode('-', trim($userData[1]));

                if ($dateData[0] == date('d') && $dateData[1] == date('m')) {
                    $users[] = $userData[0];
                }
            }
        }
    }
    $message = '';

    if(empty($users)) {
        $message = 'На текущий день именниников нет!';
    }
    else {
        $message .= "Сегодня День Рождения празднуют: \r\n";

        foreach($users as $user) {
            $message .= $user . "\r\n";
        }
    }
    fclose($file);
    return $message;
}

function deleteFunction(array $config): string {
    $address = $config['storage']['address'];
    $name = readline("Введите имя пользователя для удаления: ");

    $users = [];

    if(file_exists($address) && is_readable($address) && is_writable($address)){
        $users = file($address);
        $message ='';

        foreach ($users as $key => $user) {
           $userData = explode(',', $user);

           if($userData[0] == $name){
                unset($users[$key]);
                $message .= "Пользователь " . $name . " удален \r\n";
           }
        }

        $file = fopen($address, 'w');
        fputs($file,implode("", $users));
        fclose($file);
        return $message;
    }
    else {
        return $message = "Файла не существует";
    }
}
