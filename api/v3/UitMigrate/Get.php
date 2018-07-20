<?php

/**
 * UitMigrate.Get API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_uit_migrate_Get_spec(&$spec) {
  $spec['source_id']['api.required'] = 1;
}

/**
 * UitMigrate.Get API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_uit_migrate_Get($params) {
  if (array_key_exists('source_id', $params)) {
    // Fetch by source_id.
    $result = \CRM_ctrl_uit_BAO_UitMigrate::retrieve($params['source_id']);
    if (!empty($result)) {
      // Return values.
      $returnValues[] = $result;
      // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
      return civicrm_api3_create_success($returnValues, $params, 'UitMigrate', 'Get');
    }
    else {
      return civicrm_api3_create_error('No UitMigrate found for given source_id', NULL);
    }
  }
  else {
    throw new API_Exception('Mandatory key(s) missing from params array', 200);
  }
}
