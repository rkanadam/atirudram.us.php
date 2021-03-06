<?php
date_default_timezone_set("America/Los_Angeles");

$fileName = $_SERVER["SCRIPT_FILENAME"];
$matches = array ();
if (preg_match("/^(.*)\\/api\\/.*$/", $fileName, $matches)) {
    $base = realpath($matches[1]);
} else {
    $base = realpath(dirname($fileName) . "/..");
}


require "$base/../vendor/autoload.php";

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$scopes = implode(' ', array("https://spreadsheets.google.com/feeds", Google_Service_Drive::DRIVE_READONLY, Google_Service_Calendar::CALENDAR));

$auth = getenv("GOOGLE_AUTH");
if (empty($auth)) {
    $auth = file_get_contents("$base/api/auth.p12");
} else {
    $auth = base64_decode($auth);
}

$clientEmail = "684263653197-clcarg5o7cg5u2rq9h5arkf0fcbr3k57@developer.gserviceaccount.com";

$credentials = new Google_Auth_AssertionCredentials(
    $clientEmail,
    $scopes,
    $auth
);

$client = new Google_Client();
$client->setAssertionCredentials($credentials);
if ($client->getAuth()->isAccessTokenExpired()) {
    $client->getAuth()->refreshTokenWithAssertion();
}
$accessToken = json_decode($client->getAccessToken());
$accessToken = $accessToken->{"access_token"};

$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);

function startsWith($haystack, $needle)
{
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle)
{
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}
