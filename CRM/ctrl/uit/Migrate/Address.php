<?php

namespace CRM\ctrl\uit\Migrate;

/**
 * Save JSON data to CiviCRM Address from UiT.
 */
class Address {

  /**
   * @var string
   * Stores location_type_id.
   */
  private $type;

  /**
   * Constructor.
   */
  function __construct() {
    $this->type = 'Main';
  }

  /**save
   * Save Address.
   *
   * @param $object
   *
   * @return array result
   */
  public function save($object) {

    // Check if Address exists in UitMigrate table.
    $hash = md5(serialize($object));
    $dest_id = NULL;
    try {
      $check = civicrm_api3('UitMigrate', 'get', [
        'sequential' => 1,
        'source_id' => $object['@id'],
      ]);
      $status = 'update';
      $dest_id = $check['values'][0]['dest_id'];
      if ($check['values'][0]['hash'] == $hash) {
        $status = 'ignore';
      }
    } catch (\CiviCRM_API3_Exception $e) {
      $status = 'new';
    }

    switch ($status) {
      case 'new':

        // Address parameters.
        $address_params['location_type_id'] = $this->type;
        $address_params['contact_id'] = 'user_contact_id';
        if (isset($object['name']['nl'])) {
          $address_params['name'] = $object['name']['nl'];
        }
        if (isset($object['streetAddress'])) {
          $address_params['street_address'] = $object['streetAddress'];
        }
        if (isset($object['postalCode'])) {
          $address_params['postal_code'] = $object['postalCode'];
        }
        if (isset($object['addressLocality'])) {
          $address_params['city'] = $object['addressLocality'];
        }
        if (isset($object['addressCountry'])) {
          $address_params['country'] = $object['addressCountry'];
        }
        try {
          // Create Address via CiviCRM API.
          $address = civicrm_api3('Address', 'create', $address_params);
          \Civi::log()
            ->info("CRM_ctrl_uit_migrate_address->save() Address: " . $address['id'] . " - " . $address_params['external_id']);
        } catch (\CiviCRM_API3_Exception $e) {
          \Civi::log()
            ->debug("CRM_ctrl_uit_migrate_address->save() Address: " . print_r($e, TRUE));
        }
        if (!$address['is_error']) {
          try {
            // Create LocBlock via CiviCRM API.
            $locblock = civicrm_api3('LocBlock', 'create', ['address_id' => $address['id'],]);
            \Civi::log()
              ->info("CRM_ctrl_uit_migrate_address->save() LocBlock: " . print_r($locblock['id'], TRUE));
          } catch (\CiviCRM_API3_Exception $e) {
            \Civi::log()
              ->debug("CRM_ctrl_uit_migrate_address->save() LocBlock: " . print_r($e, TRUE));
          }
          if (!$locblock['is_error']) {
            $dest_id = $locblock['id'];
            $params['loc_block_id'] = $dest_id;
          }
        }
        // Return.
        $return = [
          'source_id' => $object['@id'],
          'dest_id' => $dest_id,
          'status' => $status,
          'type' => 'address',
        ];
        return $return;
        break;
      case 'update':

        // @todo

        // Return.
        $return = [
          'source_id' => $object['@id'],
          'dest_id' => $dest_id,
          'status' => $status,
          'type' => 'address',
        ];
        return $return;
        break;
      case 'ignore':
        // Return.
        $return = [
          'source_id' => $object['@id'],
          'dest_id' => $dest_id,
          'status' => $status,
          'type' => 'address',
        ];
        return $return;
        break;
    }

  }
}
