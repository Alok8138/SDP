<?php
$ch = curl_init('http://solr:8983/solr/csvcore/update?commit=true');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['delete' => ['query'=> '*:*']]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch);
