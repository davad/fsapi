<?php

/**
 * @file
 * Administration page for fsapi.  Manages username, password, and api key to 
 * access Family Search server.  Site admin can also select between production
 * and testing api server.
 */

require_once (drupal_get_path('module', 'fsapi') . '/fsapi.oauth.inc');

/**
 * Form builder to configure fsapi
 *
 * @ingroup forms
 * @see system_settings_form()
 */
function fsapi_admin_settings() {
  $developer_key = variable_get('fsapi_key','');

  $access = fsapi_get_vars();
  $access_token = $access['#token'];
  $expires = $access['#expires'];

  if(empty($access_token) && !empty($developer_key)) {
    $default_verifier = '';
    if(array_key_exists('oauth_verifier', $_REQUEST)){
      $default_verifier = $_REQUEST['oauth_verifier'];
    }
    //dpm($default_verifier, 'default_verifier');
    fsapi_authenticate($default_verifier);
  }

  $form['fsapi_credentials'] = array(
    '#type' => 'fieldset',
    '#title' => t('Credentials'),
    '#description' => t('API key to access the Family Search server'),
  );

  $form['fsapi_credentials']['fsapi_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Family Search API Key'),
    '#default_value' => variable_get('fsapi_key', ''),
    '#required' => TRUE,
  );

  //TODO: Have this option switch between the testing and
  //      production family search servers
  $form['fsapi_server'] = array(
    '#type' => 'radios',
    '#title' => t('Server Role'),
    '#description' => t('The Testing server is used for developing extension to the Family History modules.  The Production server should be used for all other sites'),
    '#options' => array(
      'testing' => t('Testing'),
      'production' => t('Production'),
    ),
    '#default_value' => variable_get('fsapi_server', 'production'),
  );

  return system_settings_form($form);
}

/*
 * Authenticates with the Family Search server
 *
 * This is based on the sample code provided by the Family Search Developer Network
 * https://devnet.familysearch.org/docs/api/Identity-v2/sample-code/php-sample-code
 */
function fsapi_authenticate($verifier) {
  $consumer_key = variable_get('fsapi_key', ''); # FamilySearch Developer Key
  if(empty($consumer_key)){
    drupal_set_message("The developer key must be set before authenticating.", 'error');
  }

  $oauth_callback = url("admin/config/fsapi", array('absolute'=>TRUE)); # Set this to the URL from which you are running this script
  $server = "http://www.dev.usys.org"; # https://api.familysearch.org if working with production system
  # Production code should pull these from https://api.familysearch.org/identity/v2/properties
  $requestUrl = "/identity/v2/request_token";
  $authorizeUrl = "/identity/v2/authorize";
  $accessUrl = "/identity/v2/access_token";
  //dpm($verifier,'verifier');

    /* -----------------------------------------------------------------------------------
      If this is the first time to this page get a request token and save secret to file
    ------------------------------------------------------------------------------------ */
  if (empty($verifier)) {
    $response = fsapi_get_request_token($server, $requestUrl, $oauth_callback, $consumer_key);
    //dpm($response,'Response from fsapi_get_request_token');
    if (array_key_exists('oauth_problem', $response)) {
      drupal_set_message(t('Could not get request token: @oauth_problem', $response), 'error');
    }

    if(array_key_exists('oauth_token', $response) && array_key_exists('oauth_token_secret', $response)) {
      $_SESSION['fsapi_oauth_token_secret'] = $response['oauth_token_secret'];
      $_SESSION['fsapi_oauth_token'] = $response['oauth_token'];
      drupal_set_message('Click <a href="' . $server . $authorizeUrl . '?oauth_token=' . $response['oauth_token'] . '">here</a> to authenticate with FamilySearch');
    }
  }

    /* -----------------------------------------
      Exchange oauth_verifier for access_token
    ------------------------------------------ */
  if (!empty($verifier)) {
    $response = fsapi_get_access_token($server, $accessUrl, $consumer_key, $verifier, $_GET["oauth_token"]);
    # Save the sessionId for all future FamilySearch API calls
    $sessionId = $response['oauth_token'];
    //dpm($response, '$response');
    if(empty($sessionId)){
      drupal_set_message('Did not authenticate correctly.', 'error');
      if(array_key_exists('oauth_problem', $response)) {
        drupal_set_message('Error: ' . $response['oauth_problem'], 'error');
      }
      return;
    }

    //Store access token and set expire time as drupal vars
    fsapi_set_vars($sessionID);


    drupal_set_message('Authenticated with FamilySearch successfully.');
    //        drupal_set_message("sessionId is: " . $sessionId . "\n");
    //        drupal_set_message(htmlentities(fsapi_http($server . "/familytree/v2/user/?sessionId=" . $sessionId)));
  }
}

/* Get a request_token */

function fsapi_get_request_token($server, $requestUrl, $oauth_callback, $consumer_key) {
  $oauth_token_secret = '%26';//Because the method is PLAINTEXT and the "consumer secret" is an empty string, this is always %26
  $timeStamp = time();
  $url = $server . $requestUrl . "?oauth_callback=" . $oauth_callback . "&oauth_consumer_key=";
  $url .= $consumer_key . "&oauth_signature_method=PLAINTEXT&oauth_nonce=99806503068046&oauth_version=1.0&oauth_timestamp=";
  $url .= $timeStamp . "&oauth_signature=" . $oauth_token_secret;
  $r = fsapi_http($url);
  return fsapi_oauth_parse_response($r);
}

/* Get an access_token */

function fsapi_get_access_token($server, $requestUrl, $consumer_key, $oauth_verifier, $oauth_token) {
  $oauth_token_secret = $_SESSION['fsapi_oauth_token_secret'];
  $timeStamp = time();
  $url = $server . $requestUrl . "?oauth_consumer_key=" . $consumer_key;
  $url .= "&oauth_signature_method=PLAINTEXT&oauth_nonce=99806503068046&oauth_version=1.0&oauth_timestamp=";
  $url .= $timeStamp . "&oauth_verifier=" . $oauth_verifier . "&oauth_token=" . $oauth_token . "&oauth_signature=%26" . $oauth_token_secret;
  $r = fsapi_http($url);
  return fsapi_oauth_parse_response($r);
}

/* Parse a URL-encoded OAuth response */

function fsapi_oauth_parse_response($responseString) {
  $r = array();
  foreach (explode('&', $responseString) as $param) {
    $pair = explode('=', $param, 2);
    if (count($pair) != 2)
      continue;
    $r[urldecode($pair[0])] = urldecode($pair[1]);
  }
  return $r;
}

/* Make an HTTP request */

function fsapi_http($url, $post_data = null) {
  //dpm($url, 'url passed to fsapi_http');
  $obj = drupal_http_request($url);
  //dpm($obj, 'Object returned');
  return $obj->data;
}
