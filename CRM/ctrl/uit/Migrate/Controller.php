<?php

namespace CRM\ctrl\uit\Migrate;

/**
 * Controller for item actions.
 */
class Controller {

  /**
   * @var string
   * Stores type.
   */
  private $type;

  /**
   * @var array
   * Stores settings.
   */
  private $settings;

  /**
   * @var array
   * Stores config.
   */
  private $config;

  /**
   * @var string
   * Stores host.
   */
  private $host;

  /**
   * @var string
   * Stores key.
   */
  private $key;

  /**
   * @var string
   * Stores params.
   */
  private $params;

  /**
   * @var string
   * Stores limit.
   */
  private $limit;

  /**
   * Constructor.
   */
  function __construct($type) {
    // Set type.
    $this->type = $type;
    // Set config & settings from parameters.
    $settings = \CRM_Core_BAO_Setting::getItem('uit', 'uit-settings');
    $this->settings = json_decode($settings, TRUE);
    $config = \CRM_Core_BAO_Setting::getItem('uit', 'uit-config');
    $this->config = json_decode(utf8_decode($config), TRUE);
    // Set host.
    $this->host = $this->settings['uit_host'] . $this->type;
    // Set key.
    $this->key = $this->settings['uit_key'];
    // Set params.
    $this->params = $this->config[$this->type]['params'];
    // Set limit.
    $this->limit = $this->config[$this->type]['limit'];
  }

  /**
   * Migrate status.
   *
   * @return array
   */
  public function status() {
    // Set post parameters.
    $post['q'] = $this->params;
    $post['embed'] = 'false';
    $post['start'] = 0;
    $post['limit'] = 1;
    // Fetch count from UiT API.
    $fetcher = new Fetcher($this->key);
    $response = $fetcher->getJSON($this->host, $post);
    // Log 'status' response.
    \Civi::log()
      ->info("CRM_ctrl_uit_migrate_controller->status() " . print_r($response, TRUE));
    // Create status array.
    $status = [
      'host' => $this->host,
      'modified' => $this->config[$this->type]['modified'],
    ];
    // Check for totalItems.
    if (isset($response['totalItems'])) {
      $status['count'] = $response['totalItems'];
    }
    else {
      // Return error.
      $status['error'] = $response;
    }
    // Return status.
    return $status;
  }

  /**
   * Migrate Import.
   *
   * @return array
   */
  public function import() {

    // Fetch status.
    $status = $this->status();

    if (isset($status['count'])) {
      // Paged API calls!
      $count = $status['count'];
      $step = 0;
      // Loop.
      do {
        // Log start
        \Civi::log()
          ->info("CRM_ctrl_uit_migrate_controller->import() started " . $step . "  " . $this->limit . " " . memory_get_usage());
        // Set post parameters.
        $post['q'] = $this->params;
        $post['embed'] = 'true';
        $post['start'] = $step;
        $post['limit'] = $this->limit;
        // Initial fetch from UiT API.
        $fetcher = new Fetcher($this->key);
        $response = $fetcher->getJSON($this->host, $post);
        if (isset($response['member'])) {
          foreach ($response['member'] as $value) {
            // Save UiT type to CiviCRM.
            switch ($this->type) {
              case "events":
                // Create Event.
                $event = new Event();
                $save = $event->save($value);
                // Log result.
                \Civi::log()
                  ->info("CRM_ctrl_uit_migrate_controller->import() " . print_r($save, TRUE));
                // Unset variables.
                $event = NULL;
                $save = NULL;
                break;
              default:
                // @todo: Implement other UiT types. (places, ...)
            }
          }
        }
        else {
          // Log 'import' error response.
          \Civi::log()
            ->info("CRM_ctrl_uit_migrate_controller->response_error() " . print_r($response, TRUE));
        }
        // Unset variables.
        $fetcher = NULL;
        $response = NULL;
        // Next step.
        $step += $this->limit;
        // @todo: remove when API can handle more that 10000 items.
        if ($step >= 10000) {
          break;
        }
      } while ($count >= $step);
      // Log stop
      \Civi::log()
        ->info("CRM_ctrl_uit_migrate_controller->import() stopped");
      $return = $status;
    }
    else {
      // Return error.
      $return[] = $status;
    }
    return $return;
  }

  /**
   * Migrate rollback.
   *
   * @return array
   */
  public function rollback() {
    return "rollback";
  }

}
