<?php
// require 'vendor/autoload.php';

$_POST = json_decode(file_get_contents('php://input'), true);

// Include Google Cloud dependencies using Composer
use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

if (isset($_POST) && isset($_POST['g-token'])) {
    // $secretKey = '6LfAjTcrAAAAAOOk1kj90boEgyzXYS7s39tCIs19';
    // $token = $_POST['g-token'];
    // $ip = $_SERVER['REMOTE_ADDR'];

    // $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$token."&remoteip=".$ip;
    // $request = file_get_contents($url);
    // $response = json_decode($request); 

    // if ($response->success && $response->action == 'submit' && $response->score >= 0.5) {
    //     header('HTTP/1.1 200 OK');
    // } else {
    //     header('HTTP/1.1 401 Unauthorized');
    // }
    
    // echo json_encode($response);
    // exit();


    $recaptchaKey = '6LfAjTcrAAAAAJ4CaFltGfPXKrHb9NFk81MpQfZY';
    $token = $_POST['g-token'];
    $project = 'web1-22810215-1747093310606';
    $action = 'SUBMIT'


    // Create the reCAPTCHA client.
  // TODO: Cache the client generation code (recommended) or call client.close() before exiting the method.
  $client = new RecaptchaEnterpriseServiceClient();
  $projectName = $client->projectName($project);

  // Set the properties of the event to be tracked.
  $event = (new Event())
    ->setSiteKey($recaptchaKey)
    ->setToken($token);

  // Build the assessment request.
  $assessment = (new Assessment())
    ->setEvent($event);

  try {
    $response = $client->createAssessment(
      $projectName,
      $assessment
    );

    // Check if the token is valid.
    if ($response->getTokenProperties()->getValid() == false) {
      printf('The CreateAssessment() call failed because the token was invalid for the following reason: ');
      printf(InvalidReason::name($response->getTokenProperties()->getInvalidReason()));
      return;
    }

    // Check if the expected action was executed.
    if ($response->getTokenProperties()->getAction() == $action) {
      // Get the risk score and the reason(s).
      // For more information on interpreting the assessment, see:
      // https://cloud.google.com/recaptcha-enterprise/docs/interpret-assessment
      printf('The score for the protection action is:');
      printf($response->getRiskAnalysis()->getScore());
    } else {
      printf('The action attribute in your reCAPTCHA tag does not match the action you are expecting to score');
    }
  } catch (exception $e) {
    printf('CreateAssessment() call failed with the following error: ');
    printf($e);
  }
} 
?>