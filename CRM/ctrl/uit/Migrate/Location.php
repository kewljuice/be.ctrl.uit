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
  public function save(&$object) {
    // Check if source_id exists.
    if (!isset($object['@id']) || is_null($object['@id'])) {
      $return = [
        'source_id' => NULL,
        'dest_id' => '',
        'status' => 'error',
      ];
    }
    else {
      $source_id = $object['@id'];
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
          if (isset($address['is_error']) && !$address['is_error']) {
            try {
              // Create LocBlock via CiviCRM API.
              $locblock = civicrm_api3('LocBlock', 'create', ['address_id' => $address['id'],]);
            } catch (\CiviCRM_API3_Exception $e) {
              \Civi::log()
                ->debug("CRM_ctrl_uit_migrate_location->new() LocBlock: " . $e->getMessage());
            }
            if (isset($locblock['is_error']) && !$locblock['is_error']) {
              $dest_id = $locblock['id'];
              $params['loc_block_id'] = $dest_id;
            }
          }
          // Save to UitMigrate.
          if (isset($locblock['id'])) {
            // Save UitMigrate record.
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
    $source_id = NULL;
    $dest_id = NULL;
    $status = NULL;
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
  private function saveAddress($id, &$object) {
    $address = [];
    // Address id.
    if (!is_null($id)) {
      $address_params['id'] = $id;
    }
    // Address parameters.
    $address_params['location_type_id'] = $this->type;
    $address_params['contact_id'] = 2;
    /*$address_params['contact_id'] = 'user_contact_id';*/

    // Name.
    if (isset($object['name']['nl'])) {
      $address_params['name'] = $object['name']['nl'];
    }

    // Without [nl].
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
    // Province.
    if (isset($object['address']['postalCode'])) {
      if (isset($object['address']['addressCountry']) && $object['address']['addressCountry'] == 'BE') {
        $address_params['state_province_id'] = $this->fetchProvinceByPostal($object['address']['postalCode']);
      }
    }

    // With [nl].
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
    // Province with [nl].
    if (isset($object['address']['nl']['postalCode'])) {
      if (isset($object['address']['nl']['addressCountry']) && $object['address']['nl']['addressCountry'] == 'BE') {
        $address_params['state_province_id'] = $this->fetchProvinceByPostal($object['address']['nl']['postalCode']);
      }
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
    if (isset($address['id'])) {
      // Invoke 'civicrm_uit' hook
      \CRM_Utils_Uit::Uit('create', 'address', $address['id'], $object);
    }
    // Unset.
    $address_params = NULL;
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
  private function saveUitMigrate(&$source_id, &$dest_id, &$status, &$hash) {
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
    // Unset.
    $params = NULL;
  }

  /**
   * Fetch province by postal code.
   *
   * @param string $code
   *
   * @return string result
   */
  private function fetchProvinceByPostal($code) {
    if (!empty($code)) {
      switch (TRUE) {
        case  ($code < 1300):
          return "Brussel";
          break;
        case  ($code < 1500):
          return "Waals-Brabant";
          break;
        case  ($code < 2000):
          return "Vlaams-Brabant";
          break;
        case  ($code < 3000):
          return "Antwerpen";
          break;
        case  ($code < 3500):
          return "Vlaams-Brabant";
          break;
        case  ($code < 4000):
          return "Limburg";
          break;
        case  ($code < 5000):
          return "Luik";
          break;
        case  ($code < 6000):
          return "Namen";
          break;
        case  ($code < 6600):
          return "Henegouwen";
          break;
        case  ($code < 7000):
          return "Luxemburg";
          break;
        case  ($code < 8000):
          return "Henegouwen";
          break;
        case  ($code < 9000):
          return "West-Vlaanderen";
          break;
        case  ($code < 10000):
          return "Oost-Vlaanderen";
          break;
      }
    }
    // Return.
    return NULL;
  }

}
