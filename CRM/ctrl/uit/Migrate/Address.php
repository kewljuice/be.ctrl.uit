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

  /**
   * Save Address.
   *
   * @param $object
   *
   * @return array result
   */
  public function save($object) {

    $params = [];

    // @todo: check if LocBlock/Address exists?
    $address_params['external_id'] = $object['@id'];

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
        $params['loc_block_id'] = $locblock['id'];
      }
    }

    // Return.
    return $params;
  }

}
