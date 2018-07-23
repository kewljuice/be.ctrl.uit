<?php

/**
 * UitMigrate.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_uit_migrate_Create_spec(&$spec) {
  $spec['source_id']['api.required'] = 1;
  $spec['dest_id']['api.required'] = 1;
  $spec['type']['api.required'] = 1;
  $spec['type']['options'] = [
    'events' => 'UiT Events',
    'places' => 'UiT Places',
    'location' => 'UiT Location',
  ];
  $spec['status']['api.required'] = 1;
  $spec['status']['options'] = [
    'new' => 'New',
    'update' => 'Update',
    'ignore' => 'Ignore',
  ];
  $spec['hash']['api.required'] = 1;
}

/**
 * UitMigrate.Create API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_uit_migrate_Create($params) {
  if (array_key_exists('source_id', $params) &&
    array_key_exists('dest_id', $params) &&
    array_key_exists('type', $params) &&
    array_key_exists('status', $params) &&
    array_key_exists('hash', $params)) {
    // Insert/Update UitMigrate record.
    $result = \CRM_ctrl_uit_BAO_UitMigrate::create($params);
    if (!empty($result)) {
      $returnValues[] = $result;
      return civicrm_api3_create_success($returnValues, $params, 'UitMigrate', 'Create');
    }
    else {
      return civicrm_api3_create_error('Error creating UitMigrate', NULL);
    }
  }
  else {
    throw new API_Exception('Mandatory key(s) missing from params array', 200);
  }
}
