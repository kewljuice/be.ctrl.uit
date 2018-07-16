<?php

/**
 * Save JSON data to CiviCRM Event from UiT.
 */
class CRM_ctrl_uit_migrate_event {

  /**
   * @var string
   * Stores event_type_id.
   */
  private $type;

  /**
   * Constructor.
   */
  function __construct() {
    $config = CRM_Core_BAO_Setting::getItem('uit', 'uit-config');
    $this->config = json_decode(utf8_decode($config), TRUE);
    $this->type = $this->config['events']['event_type_id'];
  }

  /**
   * Save Event.
   *
   * @param $object
   *
   * @return array result
   */
  public function save($object) {

    // @todo: check if Event exists?
    $event['external_id'] = $object['@id'];

    // Save Address.
    $fetcher = new CRM_ctrl_uit_migrate_address();
    $address = $fetcher->save($object['location']);
    if ($address['loc_block_id']) {
      $event['loc_block_id'] = $address['loc_block_id'];
    }

    // Event parameters.
    $event['event_type_id'] = $this->type;
    $event['title'] = $this->remove_emoji($object['name']['nl']);
    $event['summary'] = '';
    $event['description'] = $this->remove_emoji($object['description']['nl']);
    $event['is_active'] = 0;
    $event['start_date'] = date('Y-m-d H:i', strtotime($object['startDate']));
    $event['end_date'] = date('Y-m-d H:i', strtotime($object['endDate']));

    // Insert Event.
    $result = [
      'id' => '',
      'status' => 1,
      'event' => $event,
      'address' => $address,
    ];

    // Create Event via CiviCRM API.
    try {
      $result = civicrm_api3('Event', 'create', $event);
      Civi::log()
        ->info("CRM_ctrl_uit_migrate_event->save() Event: " . $result['id'] . " - " . $event['external_id']);

    } catch (Exception $e) {
      Civi::log()
        ->debug("CRM_ctrl_uit_migrate_event->save() Event: " . print_r($e, TRUE));
    }

    // @todo: return status (1 Created, 2 Modified, 3 Not modified).
    $event["status"] = 1;

    // Return.
    return $event;
  }

  /**
   * Remove emoji from string.
   *
   * @param $string
   *
   * @return string clear_string
   */
  protected function remove_emoji($string) {

    // Match Emoticons
    $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clear_string = preg_replace($regex_emoticons, '', $string);

    // Match Miscellaneous Symbols and Pictographs
    $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clear_string = preg_replace($regex_symbols, '', $clear_string);

    // Match Transport And Map Symbols
    $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clear_string = preg_replace($regex_transport, '', $clear_string);

    // Match Miscellaneous Symbols
    $regex_misc = '/[\x{2600}-\x{26FF}]/u';
    $clear_string = preg_replace($regex_misc, '', $clear_string);

    // Match Dingbats
    $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
    $clear_string = preg_replace($regex_dingbats, '', $clear_string);

    return $clear_string;
  }

}
