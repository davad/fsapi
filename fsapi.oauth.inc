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
  //read with oAuth format and 

  $expires = time() + 60*60;
}

function fsapi_reset_session() {
  variable_get('fsapi_access_token','');
  variable_set('fsapi_access_token_expires', 0);
}

