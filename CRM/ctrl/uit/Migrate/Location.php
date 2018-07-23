<?php

namespace CRM\ctrl\uit\Migrate;

/**
 * Save JSON data to CiviCRM Address from UiT.
 */
class Location {

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

  /**
   * Save location.
   *
   * @param $object
   *
   * @return array result
   */
  public function save($object) {
    // Check if source_id exists.
    $source_id = $object['@id'];
    if (!isset($source_id) || is_null($source_id)) {
      $return = [
        'source_id' => $source_id,
        'dest_id' => '',
        'status' => 'error',
        'type' => 'location',
      ];
    }
    else {
      // Check if Address exists in UitMigrate table.
      $hash = md5(serialize($object));
      $dest_id = NULL;
      try {
        $check = civicrm_api3('UitMigrate', 'get', [
          'sequential' => 1,
          'source_id' => $source_id,
        ]);
        $status = 'update';
        $dest_id = $check['values'][0]['dest_id'];
        if ($check['values'][0]['hash'] == $hash) {
          $status = 'ignore';
        }
      } catch (\CiviCRM_API3_Exception $e) {
        $status = 'new';
      }
      // Actions.
      switch ($status) {
        case 'new':
          // Save address.
          $address = $this->saveAddress(NULL, $object);
          if (!$address['is_error']) {
            try {
              // Create LocBlock via CiviCRM API.
              $locblock = civicrm_api3('LocBlock', 'create', ['address_id' => $address['id'],]);
              \Civi::log()
                ->info("CRM_ctrl_uit_migrate_location->new() LocBlock: " . print_r($locblock['id'], TRUE));
            } catch (\CiviCRM_API3_Exception $e) {
              \Civi::log()
                ->debug("CRM_ctrl_uit_migrate_location->new() LocBlock: " . print_r($e, TRUE));
            }
            if (!$locblock['is_error']) {
              $dest_id = $locblock['id'];
              $params['loc_block_id'] = $dest_id;
            }
          }
          // Save to UitMigrate.
          if (isset($locblock['id'])) {
            $this->saveUitMigrate($source_id, $dest_id, $status, $hash);
          }
          // Result.
          $return = [
            'source_id' => $source_id,
            'dest_id' => $dest_id,
            'status' => $status,
            'type' => 'location',
          ];
          break;
        case 'update':
          // Fetch address_id by location id.
          try {
            // Create LocBlock via CiviCRM API.
            $locblock = civicrm_api3('LocBlock', 'getsingle', [
              'return' => ['address_id'],
              'id' => $dest_id,
            ]);
            \Civi::log()
              ->info("CRM_ctrl_uit_migrate_location->update() LocBlock: " . print_r($locblock['id'], TRUE));
          } catch (\CiviCRM_API3_Exception $e) {
            \Civi::log()
              ->debug("CRM_ctrl_uit_migrate_location->update() LocBlock: " . print_r($e, TRUE));
          }
          if (isset($locblock['address_id'])) {
            // Update address.
            $address = $this->saveAddress($locblock['address_id'], $object);
            if (!$address['is_error']) {
              // Update UitMigrate.
              $this->saveUitMigrate($source_id, $dest_id, $status, $hash);
            }
          }
          // Result.
          $return = [
            'source_id' => $source_id,
            'dest_id' => $dest_id,
            'status' => $status,
            'type' => 'location',
          ];
          break;
        case 'ignore':
          $return = [
            'source_id' => $source_id,
            'dest_id' => $dest_id,
            'status' => $status,
            'type' => 'location',
          ];
          break;
      }
    }
    // Return.
    return $return;
  }

  /**
   * Save address.
   *
   * @param integer $id
   * @param object $object
   *
   * @return array result
   */
  private function saveAddress($id, $object) {
    $address = [];
    // Address id.
    if (!is_null($id)) {
      $address_params['id'] = $id;
    }
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
        ->info("CRM_ctrl_uit_migrate_location->saveAddress(): " . $address['id'] . " - " . $address_params['external_id']);
    } catch (\CiviCRM_API3_Exception $e) {
      \Civi::log()
        ->debug("CRM_ctrl_uit_migrate_location->saveAddress(): " . print_r($e, TRUE));
    }
    // Return.
    return $address;
  }

  /**
   * Save UitMigrate record.
   *
   * @param string $source_id
   * @param string $dest_id
   * @param string $status
   * @param string $hash
   */
  private function saveUitMigrate($source_id, $dest_id, $status, $hash) {
    $params['source_id'] = $source_id;
    $params['dest_id'] = $dest_id;
    $params['type'] = 'location';
    $params['status'] = $status;
    $params['hash'] = $hash;
    try {
      civicrm_api3('UitMigrate', 'create', $params);
    } catch (\CiviCRM_API3_Exception $e) {
      \Civi::log()
        ->debug("CRM_ctrl_uit_migrate_location->saveUitMigrate(): " . print_r($e, TRUE));
    }
  }
}
