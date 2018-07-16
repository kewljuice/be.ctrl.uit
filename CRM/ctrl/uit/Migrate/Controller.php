<?php

/**
 * Controller for item actions.
 */
class CRM_ctrl_uit_migrate_controller {

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
    $settings = CRM_Core_BAO_Setting::getItem('uit', 'uit-settings');
    $this->settings = json_decode($settings, TRUE);
    $config = CRM_Core_BAO_Setting::getItem('uit', 'uit-config');
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
    $fetcher = new CRM_ctrl_uit_migrate_fetcher($this->key);
    $response = $fetcher->getJSON($this->host, $post);
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

  public function import() {

    $status = $this->status();

    if (isset($status['count'])) {
      $return = $status;
      // Paged API calls!
      $count = $status['count'];
      $step = 0;
      $items = [];
      // Loop.
      do {
        // Set post parameters.
        $post['q'] = $this->params;
        $post['embed'] = 'true';
        $post['start'] = $step;
        $post['limit'] = $this->limit;
        // Initial fetch from UiT API.
        $fetcher = new CRM_ctrl_uit_migrate_fetcher($this->key);
        $response = $fetcher->getJSON($this->host, $post);
        if (isset($response['member'])) {
          foreach ($response['member'] as $value) {
            $items[$value['@id']]['id'] = $value['@id'];
            $items[$value['@id']]['hash'] = md5(serialize($value));
          }
        }
        // Next.
        $step += $this->limit;
        // @todo: remove when API can handle more that 10000 items.
        if ($step >= 1000) {
          break;
        }
      } while ($count >= $step);
      $return['items'] = $items;
    }
    else {
      // Return error.
      $return['error'] = $status;
    }
    return $return;
  }

  public function rollback() {
    return "rollback";
  }

}