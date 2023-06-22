<?php

// I am trying to connect google forms to my db using PhP

require_once 'vendor/autoload.php';

use Google\Client as GoogleClient;

$credentialsFile = '583551002188-ns4u64lkepargtfcjln7hb76liqibggs.apps.googleusercontent.com';
$scope = ['https://www.googleapis.com/auth/forms.readonly'];
$formId = '1FJmdNHIOQfVEM9yl8D2SKgO48v0EcrLUNljAhGoXhJQ';

$dbHost = ' ';
$dbName = ' ';
$dbUser = ' ';
$dbPassword = ' ';

function connectToPostgreSQL() {
    $connectionString = "host=$GLOBALS['dbHost'] dbname=$GLOBALS['dbName'] user=$GLOBALS['dbUser'] password=$GLOBALS['dbPassword']";
    $conn = pg_connect($connectionString);
    return $conn;
}

function retrieveFormResponses() {
    $client = new GoogleClient();
    $client->setAuthConfig($GLOBALS['credentialsFile']);
    $client->setScopes($GLOBALS['scope']);
    $service = new Google_Service_Forms($client);
    $response = $service->forms_responses->listResponses($GLOBALS['1FJmdNHIOQfVEM9yl8D2SKgO48v0EcrLUNljAhGoXhJQ'], ['includeResponseIds' => true]);
    return $response->getResponses();
}

function saveResponsesToPostgreSQL($responses) {
    $conn = connectToPostgreSQL();

    foreach ($responses as $response) {
        $responseId = $response->getResponseId();
        $name = $response->getAnswers()[0]->getText();
        $email = $response->getAnswers()[1]->getText();
        $bloodtype = $response->getAnswers()[2]->getText();

        $query = "INSERT INTO your_table_name (response_id, name, email) VALUES ('$responseId', '$name', '$email')";
        pg_query($conn, $query);
    }

    pg_close($conn);
}

$responses = retrieveFormResponses();
saveResponsesToPostgreSQL($responses);

?>
