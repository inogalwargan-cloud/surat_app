<?php

$xml = file_get_contents('document.xml');
echo 'Contains shapetype: ' . (strpos($xml, 'shapetype') !== false ? 'YES' : 'NO') . "\n";
echo 'Contains _x0000_t75: ' . (strpos($xml, '_x0000_t75') !== false ? 'YES' : 'NO') . "\n";
preg_match_all('/<v:[^>]+>/', $xml, $matches);
print_r($matches[0]);
