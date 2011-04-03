<?php
/** 
 * @file
 * This file manages the oauth access token.
 */

function fsapi_get_vars() {
  $access_token = variable_get('fsapi_access_token','');
  $expires = variable_get('fsapi_access_token_expires', 0);

  //Check that the token hasn't expired
  if($expires < time()) {
    return array(
      '#token'   =>  '',
      '#expires' =>  0,
    );
  }

  return array(
    '#token'   => $access_token,
    '#expires' => $expires,
  );
}

function fsapi_set_vars($access_token) {
  $expires = time() + 60*60;
  variable_set('fsapi_access_token',$access_token);
  variable_set('fsapi_access_token_expires', $expires);
}


function fsapi_refresh_session() {
  $fsapi_access = fsapi_get_vars();

  $server_role = variable_get('fsapi_server', 'testing');
  $read_url = $server_role + "/identity/v2/session?sessionId=" + $fsapi_access['#token'];

  $request = drupal_http_request($read_url);
  // TODO: Check $request for errors, if no errors, update expire time
  //       If there is an error, log it to watchdog
  fsapi_set_vars($fsapi_access['#token']);
}

function fsapi_reset_session() {
  variable_get('fsapi_access_token','');
  variable_set('fsapi_access_token_expires', 0);
}

