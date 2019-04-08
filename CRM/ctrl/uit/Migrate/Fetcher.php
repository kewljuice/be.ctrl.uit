<?php

namespace CRM\ctrl\uit\Migrate;

/**
 * Obtain JSON data from UiT.
 */
class Fetcher {

  /**
   * @var array
   * Stores headers.
   */
  private $headers;

  /**
   * Constructor.
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
    $params = str_replace(' ', '%20', $post);
    $url = urldecode($host . '/?' . http_build_query($params));
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
