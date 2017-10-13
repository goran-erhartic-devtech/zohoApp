<?php
/**
 * Created by PhpStorm.
 * User: goran
 * Date: 13-Oct-17
 * Time: 12:38
 */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://hooks.slack.com/services/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");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, "opacaaaaaa");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

if (FALSE === $result)
    throw new Exception(curl_error($ch), curl_errno($ch));

curl_close($ch);

echo "ADFDSFSF";