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
            } catch (\CiviCRM_API3_Exception $e) {
              \Civi::log()
                ->debug("CRM_ctrl_uit_migrate_location->new() LocBlock: " . $e->getMessage());
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
          } catch (\CiviCRM_API3_Exception $e) {
            \Civi::log()
              ->debug("CRM_ctrl_uit_migrate_location->update() LocBlock: " . $e->getMessage());
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
          ];
          break;
        case 'ignore':
          $return = [
            'source_id' => $source_id,
            'dest_id' => $dest_id,
            'status' => $status,
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
    $address_params['contact_id'] = 2;
    /*$address_params['contact_id'] = 'user_contact_id';*/

    // name.
    if (isset($object['name']['nl'])) {
      $address_params['name'] = $object['name']['nl'];
    }

    // without [nl].
    if (isset($object['address']['streetAddress'])) {
      $address_params['street_address'] = $object['address']['streetAddress'];
    }
    if (isset($object['address']['postalCode'])) {
      $address_params['postal_code'] = $object['address']['postalCode'];
    }
    if (isset($object['address']['addressLocality'])) {
      $address_params['city'] = $object['address']['addressLocality'];
    }
    if (isset($object['address']['addressCountry'])) {
      $address_params['country'] = $object['address']['addressCountry'];
    }

    // with [nl].
    if (isset($object['address']['nl']['streetAddress'])) {
      $address_params['street_address'] = $object['address']['nl']['streetAddress'];
    }
    if (isset($object['address']['nl']['postalCode'])) {
      $address_params['postal_code'] = $object['address']['nl']['postalCode'];
    }
    if (isset($object['address']['nl']['addressLocality'])) {
      $address_params['city'] = $object['address']['nl']['addressLocality'];
    }
    if (isset($object['address']['nl']['addressCountry'])) {
      $address_params['country'] = $object['address']['nl']['addressCountry'];
    }

    // Latitude
    if (isset($object['geo']['latitude'])) {
      $address_params['geo_code_1'] = $object['geo']['latitude'];
    }
    // Longitude
    if (isset($object['geo']['longitude'])) {
      $address_params['geo_code_2'] = $object['geo']['longitude'];
    }

    try {
      // Create Address via CiviCRM API.
      $address = civicrm_api3('Address', 'create', $address_params);
    } catch (\CiviCRM_API3_Exception $e) {
      \Civi::log()
        ->debug("CRM_ctrl_uit_migrate_location->saveAddress(): " . $e->getMessage());
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
        ->debug("CRM_ctrl_uit_migrate_location->saveUitMigrate(): " . $e->getMessage());
    }
  }
}
