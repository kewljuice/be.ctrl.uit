<?php

/**
 * Obtain JSON data from UiT.
 */
class CRM_ctrl_uit_migrate_fetcher {

  /**
   * @var array
   * Stores headers.
   */
  private $headers;

  /**
   * Contructor.
   */
  function __construct($key) {
    // Define headers.
    $this->headers = [
      'Content-Type:application/json',
      'X-API-Key: ' . $key,
    ];
  }

  /**
   * Get results from UiT.
   *
   * @param $host
   * @param $post
   *
   * @return array
   */
  public function getJSON($host, $post) {
    // Get cURL resource.
    $curl = curl_init();
    // Create url.
    $url = urldecode($host . '/?' . http_build_query($post));
    // Set some cURL options.
    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_HTTPHEADER => $this->headers,
      CURLOPT_URL => $url,
    ]);
    // Send the request & save response to $data.
    $data = curl_exec($curl);
    // Close request to clear up resources.
    curl_close($curl);
    // Pass results.
    return json_decode($data, TRUE);
  }
}