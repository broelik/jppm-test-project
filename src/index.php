<?php
use php\lib\arr;
use php\lang\System;
use php\format\Processor;
use php\lib\fs;
use php\format\JsonProcessor;
use php\format\YamlProcessor;
use php\io\Stream;

// получаем аргументы командной строки
global $argv;
$args = $argv;
arr::shift($args); // убираем первый аргумент

/**
 * Функция для вывода сообщения об ошибке и выходе из программы
 * @param string $message
 * @param int $exitCode
 */
function error(string $message){
    echo $message."\n";
    System::halt(1);
}

if(count($args) < 2){
    error("Usage: json-yaml-converter <input json or yaml> <output json or yaml file>");
}

$input = $args[0];
$output = $args[1];

/**
 * @return Processor
 * @param string $file
 */
function getProcessor(string $file){
    if(($inputExt = fs::ext($file)) == 'json'){
        return new JsonProcessor(JsonProcessor::DESERIALIZE_AS_ARRAYS | JsonProcessor::SERIALIZE_PRETTY_PRINT);
    }
    else if($inputExt == 'yml' || $inputExt == 'yaml'){
        return new YamlProcessor();
    }
    error("Unable find processor for {$file}");
}

$inputProcessor = getProcessor($input);
$outputProcessor = getProcessor($output);

try{
    $outputProcessor->formatTo($inputProcessor->parse(fs::get($input)), $output);
}
catch(\php\io\IOException $e){
    error($e->getMessage());
}



