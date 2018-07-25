<?php

namespace CRM\ctrl\uit\Migrate;

/**
 * Save JSON data to CiviCRM Event from UiT.
 */
class Event {

  /**
   * @var string
   * Stores event_type_id.
   */
  private $type;

  /**
   * Constructor.
   */
  function __construct() {
    $config = \CRM_Core_BAO_Setting::getItem('uit', 'uit-config');
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
  public function save(&$object) {
    // Check if Event exists in UitMigrate table.
    $hash = md5(serialize($object));
    $dest_id = NULL;
    $location = [];
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

    // @todo: move after 'ignore'? - Save Location.
    $fetcher = new Location();
    $location = $fetcher->save($object['location']);
    if ($location['dest_id']) {
      $event['loc_block_id'] = $location['dest_id'];
    }

    // Skip status 'ignore'.
    if ($status != 'ignore') {

      // Add 'event_id' for update.
      if ($status == 'update') {
        $event['id'] = $dest_id;
      }
      // Event parameters.
      $event['event_type_id'] = $this->type;
      if (isset($object['name']['nl'])) {
        $event['title'] = $this->remove_emoji($object['name']['nl']);
      }
      else {
        $event['title'] = $object['@id'];
      }
      /* $event['summary'] = ''; */
      if (isset($object['description']['nl'])) {
        $event['description'] = $this->remove_emoji($object['description']['nl']);
      }
      $event['is_active'] = 0;
      $event['start_date'] = date('Y-m-d H:i', strtotime($object['startDate']));
      $event['end_date'] = date('Y-m-d H:i', strtotime($object['endDate']));
      try {
        // Create Event via CiviCRM API.
        $result = civicrm_api3('Event', 'create', $event);
      } catch (\CiviCRM_API3_Exception $e) {
        \Civi::log()
          ->debug("CRM_ctrl_uit_migrate_event->save() Event: " . $e->getMessage());
      }
      $event = NULL;
      // Save UitMigrate record.
      if (isset($result['id'])) {
        $dest_id = $result['id'];
        $params['source_id'] = $object['@id'];
        $params['dest_id'] = $dest_id;
        $params['type'] = 'events';
        $params['status'] = $status;
        $params['hash'] = $hash;
        try {
          civicrm_api3('UitMigrate', 'create', $params);
        } catch (\CiviCRM_API3_Exception $e) {
          \Civi::log()
            ->debug("CRM_ctrl_uit_migrate_event->save() UitMigrate: " . $e->getMessage());
        }
        $params = NULL;
      }
    }
    // Return.
    $return = [
      'event' => [
        'source_id' => $object['@id'],
        'dest_id' => $dest_id,
        'status' => $status,
      ],
      'location' => $location,
    ];
    // Unset.
    $hash = NULL;
    $dest_id = NULL;
    $status = NULL;
    $location = NULL;
    // Return.
    return $return;
  }

  /**
   * Remove emoji from string.
   *
   * @param $string
   *
   * @return string clear_string
   */
  protected function remove_emoji(&$string) {
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
