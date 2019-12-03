<?php
$origName = readline("Original Video name eingeben: ");
$jsonName = readline("Json Name eingeben: ");

$inputPath = "Videos/original/{$origName}.mp4";
$json = "json/{$jsonName}.json";

$folderName = date("d-m-Y_H-i");
$filename = basename($inputPath);
$outputPath = "Videos/changed/{$folderName}/";

if(!file_exists($outputPath)){
  mkdir($outputPath,0777,true);
}

$startTime = 0;
$endTime = 10;

$output = $outputPath.$filename;

$events = json_decode(file_get_contents($json),true);
asort($events);

$i = 0;
foreach ($events as $time => $event) {
  $i++;
  $startTimeSplit = explode(":",$time);
  $startTimeMinute = intval($startTimeSplit[0]);
  $startTimeSecond = intval($startTimeSplit[1]);
  if($startTimeMinute<1){
    $startTimeSecond = 0;
  }else{
    $startTimeMinute--;
  }

  $startTime = sprintf("00:%02d:%02d",$startTimeMinute,$startTimeSecond);

  echo "Cut part nr {$i}: Start time: {$startTime}\n";
  cutVideo($startTime,"00:02:00",$inputPath,$outputPath.$i.".mp4");
}


function cutVideo($startTime,$duration,$inputPath,$outputPath){
  $cmd = "ffmpeg -ss {$startTime} -i \"{$inputPath}\" -c copy -t {$duration} \"{$outputPath}\" 2>&1";
  exec($cmd);
}
