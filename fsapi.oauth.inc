<?php
/** 
 * @file
 * This file manages the oauth access token.
 */

function fsapi_get_vars() {
  $access_token = variable_get('fsapi_access_token','');
  $expires = variable_get('fsapi_access_token_expires', 0);
  $reauthenticate = variable_get('fsapi_access_token_reauthenticate', 0);
  $last_authentication = variable_get('fsapi_access_token_last_authentication', 0);

  //Check that the token hasn't expired
  if($expires < time()) {
    return array(
      '#token'   =>  '',
      '#expires' =>  0,
      '#reauthenticate' => 0,
      '#last_authentication' => 0,
    );
  }

  return array(
    '#token'   => $access_token,
    '#expires' => $expires,
    '#reauthenticate' => $reauthenticate,
    '#last_authentication' => $last_authentication,
  );
}

/**
 * Set FamilySearch oAuth variables
 *
 * @param The access token obtained after authenticating
 * @param Set to TRUE when calling fsapi_set_vars after authenticting
 */
function fsapi_set_vars($access_token, $just_authenticated=FALSE) {
  $expires = time() + 60*60;
  variable_set('fsapi_access_token',$access_token);
  variable_set('fsapi_access_token_expires', $expires);

  //Set the time that the site admin will need to reauthenticate
  if($just_authenticated) {
    variable_set('fsapi_access_token_last_authentication', time());

    $next_month = time() + (30 * 24 * 60 * 60);
    variable_set('fsapi_access_token_reauthenticate', $next_month);
  }
}


function fsapi_refresh_session() {
  $fsapi_access = fsapi_get_vars();

  // Check that we don't need to reauthenticate
  if($fsapi_access['reauthenticate'] < time()) {
    fsapi_reset_session();
    return;
  }

  // Ping FamilySearch server to keep session alive
  $server_role = variable_get('fsapi_server', 'testing');
  $read_url = $server_role + "/identity/v2/session?sessionId=" + $fsapi_access['#token'];

  $request = drupal_http_request($read_url);
  // TODO: Check $request for errors, if no errors, update expire time
  //       If there is an error, log it to watchdog
  fsapi_set_vars($fsapi_access['#token']);
}

function fsapi_reset_session() {
  variable_set('fsapi_access_token','');
  variable_set('fsapi_access_token_expires', 0);
  variable_set('fsapi_access_token_reauthenticate', 0);
  // Don't reset the last_authentication variable.
  //variable_set('fsapi_access_token_last_authentication', 0;
}

