<?php

/**
 * UitMigrate.Clear API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_uit_migrate_Clear_spec(&$spec) {
  $spec['type']['api.required'] = 1;
  $spec['type']['options'] = [
    'all' => '*',
    'events' => 'UiT Events',
    'address' => 'UiT Address',
    'places' => 'UiT Places',
  ];
}

/**
 * UitMigrate.Clear API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_uit_migrate_Clear($params) {
  if (array_key_exists('type', $params)) {
    // Switch type.
    switch ($params['type']) {
      case "all":
        $result = \CRM_ctrl_uit_BAO_UitMigrate::clear('all');
        // Return values.
        $returnValues = [
          1 => ['type' => "all"],
        ];
        return civicrm_api3_create_success($returnValues, $params, 'UitMigrate', 'Clear');
        break;
      case "events";
        $result = \CRM_ctrl_uit_BAO_UitMigrate::clear('events');
        // Return values.
        $returnValues = [
          1 => ['type' => "events"],
        ];
        return civicrm_api3_create_success($returnValues, $params, 'UitMigrate', 'Clear');
        break;
      default:
        // @todo: Implement other UiT types. (places, ...)
        return civicrm_api3_create_error('Not yet developed Type', NULL);
    }
  }
  else {
    throw new API_Exception('Mandatory key(s) missing from params array', 200);
  }
}
