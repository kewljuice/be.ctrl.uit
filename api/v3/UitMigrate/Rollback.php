<?php

use CRM\ctrl\uit\Migrate\Controller;

/**
 * UitMigrate.Rollback API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_uit_migrate_Rollback_spec(&$spec) {
  $spec['UitType']['api.required'] = 1;
  $spec['UitType']['options'] = [
    'events' => 'UiT Events',
    'places' => 'UiT Places',
  ];
  $spec['UitType']['description'] = "Select the UiT type";
}

/**
 * UitMigrate.Rollback API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_uit_migrate_Rollback($params) {
  if (array_key_exists('UitType', $params)) {
    switch ($params['UitType']) {
      case "events":
        $type = 'events';
        $controller = new Controller($type);
        $returnValues = $controller->rollback();
        return civicrm_api3_create_success($returnValues, $params, 'UitMigrate', 'Rollback');
        break;
      default:
        // @todo: Implement other UiT types. (places, ...)
        return civicrm_api3_create_error('Not yet developed UitType', NULL);
    }
  }
  else {
    throw new API_Exception('Mandatory key(s) missing from params array: UitType', 200);
  }
}
