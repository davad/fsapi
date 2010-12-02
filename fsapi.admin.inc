<?php
/**
 * @file
 * Administration page for fsapi.  Manages username, password, and api key to 
 * access Family Search server.  Site admin can also select between production
 * and testing api server.
 */

/** 
 * Form builder to configure fsapi
 *
 * @ingroup forms
 * @see system_settings_form()
 */

function fsapi_admin_settings() {
    $form['fsapi_credentials'] = array(
        '#type' => 'fieldset',
        '#title'         => t('Credentials'),
        '#description'   => t('Username, password, and api key to access the Family Search server'),
    );

    $form['fsapi_credentials']['fsapi_username'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Username'),
        '#description'   => t('Family Search username'),
        '#default_value' => variable_get('fsapi_username', ''),
        '#required'      => TRUE,
    );
    $form['fsapi_credentials']['fsapi_password'] = array(
        '#type'          => 'password',
        '#title'         => t('Password'),
        '#description'   => t('Family Search password'),
        '#default_value' => variable_get('fsapi_username', ''),
        '#required'      => TRUE,
    );
    $form['fsapi_credentials']['fsapi_key'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Family Search API Key'),
        '#default_value' => variable_get('fsapi_key', ''),
        '#required'      => TRUE,
    );
    
    $form['fsapi_server'] = array(
        '#type'         => 'radios',
        '#title'        => t('Server Role'),
        '#description'  => t('The Testing server is used for developing extension to the Family History modules.  The Production server should be used for all other sites'),
        '#options'      => array(
            'testing'      => t('Testing'),
            'production'   => t('Production'),
        ),
        '#default_value' => variable_get('fsapi_server','production'),
    );
    return system_settings_form($form);
}
