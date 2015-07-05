<?php

require_once 'src/Google_Client.php';
require 'vendor/autoload.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$client_id = '432968448510-5tlpl27dfn6ha4e72v0pmsfvd5gfm4l2.apps.googleusercontent.com';
$client_email = '432968448510-5tlpl27dfn6ha4e72v0pmsfvd5gfm4l2@developer.gserviceaccount.com';
$path_to_p12 = dirname(__FILE__).'\test2-7d30c56652f3.p12';


if($_POST)
{

    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        
        $output = json_encode(array( //create JSON data
            'type'=>'error', 
            'text' => 'Sorry Request must be Ajax POST'
        ));
        die($output); //exit script outputting json data
    } 
    
    //Sanitize input data using PHP filter_var().
    $user_name      = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
    $user_age     = filter_var($_POST["user_age"], FILTER_SANITIZE_STRING);
    
    //additional php validation
	/*
    if(strlen($user_name)<4){ // If length is less than 4 it will output JSON error.
        $output = json_encode(array('type'=>'error', 'text' => 'Name is too short or empty!'));
        die($output);
    }
	*/
	
	$serviceRequest = new DefaultServiceRequest(getGoogleTokenFromKeyFile($client_id, $client_email, $path_to_p12));
	ServiceRequestFactory::setInstance($serviceRequest);

	$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
	$spreadsheetFeed = $spreadsheetService->getSpreadsheets();
	$spreadsheet = $spreadsheetFeed->getByTitle('table123');
	$worksheetFeed = $spreadsheet->getWorksheets();
	$worksheet = $worksheetFeed->getByTitle('Sheet 1');
	$listFeed = $worksheet->getListFeed();

	$row = array('name'=>$user_name , 'age'=>$user_age);
	$listFeed->insert($row);

    if(!$listFeed)
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Could not send data! Please check your configuration.'));
        die($output);
    }else{
        $output = json_encode(array('type'=>'message', 'text' => 'Thank you!'));
        die($output);
    }
}

	
function getGoogleTokenFromKeyFile($clientId, $clientEmail, $pathToP12File) {
    $client = new Google_Client();
    $client->setClientId($clientId);

    $cred = new Google_AssertionCredentials(
        $clientEmail,
        array('https://spreadsheets.google.com/feeds'),
        file_get_contents($pathToP12File)
    );

	
    $client->setAssertionCredentials($cred);

    if ($client->getAuth()->isAccessTokenExpired()) {
        $client->getAuth()->refreshTokenWithAssertion($cred);
    }
	
    $service_token = json_decode($client->getAccessToken());

    return $service_token->access_token;
}