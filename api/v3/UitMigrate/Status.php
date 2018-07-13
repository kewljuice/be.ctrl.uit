<?php

/**
 * UitMigrate.Status API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_uit_migrate_Status_spec(&$spec) {
  $spec['UitType']['api.required'] = 1;
  $spec['UitType']['options'] = [
    'events' => 'UiT Events',
    'places' => 'UiT Places',
  ];
  $spec['UitType']['description'] = "Select the UiT type";
}

/**
 * UitMigrate.Status API
 *
 * @param array $params
 *
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_uit_migrate_Status($params) {
  // Check if 'UitType' is given.
  if (array_key_exists('UitType', $params)) {

    // Switch UitType.
    switch ($params['UitType']) {
      case "events":

        // Fetch config & settings.
        $settings = CRM_Core_BAO_Setting::getItem('uit', 'uit-settings');
        $settings = json_decode($settings, TRUE);
        $config = CRM_Core_BAO_Setting::getItem('uit', 'uit-config');
        $config = json_decode(utf8_decode($config), TRUE);

        // Parameters.
        $type = 'events';
        $host = $settings['uit_host'] . $type;
        $post['q'] = $config[$type]['params'];
        $post['embed'] = 'true';
        $post['start'] = 0;
        $post['limit'] = $config[$type]['limit'];
        $key = $settings['uit_key'];

        // Fetch count from UiT API.
        $fetcher = new CRM_ctrl_uit_migrate_fetcher($key);
        $response = $fetcher->getJSON($host, $post);

        // Create result.
        $returnValues[$type] = [
          'host' => $host,
          'post' => $post,
          'modified' => $config[$type]['modified'],
        ];
        if (isset($response['totalItems'])) {
          $returnValues[$type]['count'] = $response['totalItems'];
        }
        else {
          // Return error.
          $returnValues[$type]['error'] = $response;
        }

        // Succes.
        return civicrm_api3_create_success($returnValues, $params, 'UitMigrate', 'Status');

        break;
      default:
        // TODO: Implement other UiT types. (places, ...)
        return civicrm_api3_create_error('Not yet developed UitType', NULL);
    }
  }
  else {
    throw new API_Exception('Mandatory key(s) missing from params array: UitType', 200);
  }
}
