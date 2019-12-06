<?php
$origName = readline("Original Video name eingeben: ");
$jsonName = readline("Json Name eingeben: ");
$startTimeOffset = "00:45";
$duration = "00:00:50";

$inputPath = "Videos/original/{$origName}.mp4";
$json = "json/{$jsonName}.json";

$folderName = "{$origName}_".date("YmdHi");
$filename = basename($inputPath);
$outputPath = "Videos/changed/{$folderName}/";

if(!file_exists($outputPath)){
  mkdir($outputPath,0777,true);
}

$output = $outputPath.$filename;

$events = json_decode(file_get_contents($json),true);
asort($events);

$i = 0;
$lastEvent = "";
foreach ($events as $time => $event) {
  if($lastEvent != $event){
    $lastEvent = $event;
    $i = 0;
  }
  $i++;
  $startTimeSplit = explode(":",$time);
  $offset = explode(":",$startTimeOffset);
  $startTimeMinute = intval($startTimeSplit[0]);
  $startTimeSecond = intval($startTimeSplit[1]);
  $offsetMinute = intval($offset[0]);
  $offsetSecond = intval($offset[1]);
  $startTimeSplit = $startTimeMinute*60+$startTimeSecond;
  $offset = $offsetMinute*60+$offsetSecond;

  $timeDiff = $startTimeSplit-$offset;
  if($timeDiff<0){
    $timeDiff = 0;
  }

  $minutes = intdiv($timeDiff,60);
  $seconds = $timeDiff%60;
  $startTime = sprintf("00:%02d:%02d",$minutes,$seconds);

  echo "Cut part nr {$i}: Start time: {$startTime}\n";
  cutVideo($startTime,$duration,$inputPath,$outputPath.correctFilename($event)."_".$i.".mp4");
}


function cutVideo($startTime,$duration,$inputPath,$outputPath){
  $cmd = "ffmpeg -ss {$startTime} -i \"{$inputPath}\" -c copy -t {$duration} \"{$outputPath}\" 2>&1";
  exec($cmd);
}

function correctFilename($dateiname){
  $dateiname = strtolower ( $dateiname );
  $dateiname = str_replace ('"', "-", $dateiname );
  $dateiname = str_replace ("'", "-", $dateiname );
  $dateiname = str_replace ("*", "-", $dateiname );
  $dateiname = str_replace ("ß", "ss", $dateiname );
  $dateiname = str_replace ("&szlig;", "ss", $dateiname );
  $dateiname = str_replace ("ä", "ae", $dateiname );
  $dateiname = str_replace ("&auml;", "ae", $dateiname );
  $dateiname = str_replace ("ö", "oe", $dateiname );
  $dateiname = str_replace ("&ouml;", "oe", $dateiname );
  $dateiname = str_replace ("ü", "ue", $dateiname );
  $dateiname = str_replace ("&uuml;", "ue", $dateiname );
  $dateiname = str_replace ("&Auml;", "ae", $dateiname );
  $dateiname = str_replace ("&Ouml;", "oe", $dateiname );
  $dateiname = str_replace ("&Uuml;", "ue", $dateiname );
  $dateiname = htmlentities ( $dateiname );
  $dateiname = str_replace ("&", "und", $dateiname );
  $dateiname = str_replace ("+", "und", $dateiname );
  $dateiname = str_replace ("(", "-", $dateiname );
  $dateiname = str_replace (")", "-", $dateiname );
  $dateiname = str_replace (" ", "-", $dateiname );
  $dateiname = str_replace ("\'", "-", $dateiname );
  $dateiname = str_replace ("/", "-", $dateiname );
  $dateiname = str_replace ("?", "-", $dateiname );
  $dateiname = str_replace ("!", "-", $dateiname );
  $dateiname = str_replace (":", "-", $dateiname );
  $dateiname = str_replace (";", "-", $dateiname );
  $dateiname = str_replace (",", "-", $dateiname );
  $dateiname = str_replace ("--", "-", $dateiname );

  $dateiname = filter_var($dateiname, FILTER_SANITIZE_URL);
  return ($dateiname);
}
