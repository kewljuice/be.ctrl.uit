<?php

class CRM_ctrl_uit_BAO_UitMigrate extends CRM_ctrl_uit_DAO_UitMigrate {

  /**
   * Class constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Create a new UitMigrate based on array-data
   *
   * @param array $params key-value pairs
   *
   * @return mixed|null
   */
  public static function create($params) {
    if (isset($params['source_id'])) {
      // Check if UitMigrate item exists.
      $status = self::retrieve($params['source_id']);
      if (!empty($status)) {
        // Update.
        $query = 'UPDATE civicrm_uit_migrate SET status="%1", hash="%2", modified=CURRENT_TIMESTAMP WHERE source_id="%3"';
        $result = CRM_Core_DAO::executeQuery($query, [
          1 => [
            $params['status'],
            'String',
            CRM_Core_DAO::QUERY_FORMAT_NO_QUOTES,
          ],
          2 => [
            $params['hash'],
            'String',
            CRM_Core_DAO::QUERY_FORMAT_NO_QUOTES,
          ],
          3 => [
            $params['source_id'],
            'String',
            CRM_Core_DAO::QUERY_FORMAT_NO_QUOTES,
          ],
        ]);
        $action[] = 'update';
      }
      else {
        // Insert.
        $UitMigrate = new CRM_ctrl_uit_DAO_UitMigrate();
        $UitMigrate->copyValues($params);
        $result = $UitMigrate->save();
        $action[] = 'insert';
      }
    }
    return $action;
  }

  /**
   * Fetch object based on array of properties.
   *
   * @param string $source_id
   *   (reference ) $source_id for migrate record.
   *
   * @return array|null
   */
  public static function retrieve(&$source_id) {
    // Check is source_id exists in table.
    $query = 'SELECT * FROM civicrm_uit_migrate WHERE source_id = "%1"';
    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [$source_id, 'String', CRM_Core_DAO::QUERY_FORMAT_NO_QUOTES],
    ]);
    $items = [];
    while ($dao->fetch()) {
      $item['source_id'] = $dao->source_id;
      $item['dest_id'] = $dao->dest_id;
      $item['type'] = $dao->type;
      $item['status'] = $dao->status;
      $item['hash'] = $dao->hash;
      $item['modified'] = $dao->modified;
      $items[$source_id] = $item;
    }
    if (!empty($items[$source_id])) {
      // create.
      return $items[$source_id];
    }
    return NULL;
  }

  /**
   * Remove UitMigrate based on type.
   *
   * @param string $type
   *   (reference ) $type.
   *
   * @return array|null
   */
  public static function clear($type) {
    // Check type.
    if ($type == "all") {
      $query = "DELETE FROM civicrm_uit_migrate";
      CRM_Core_DAO::executeQuery($query);
    }
    else {
      $query = 'DELETE FROM civicrm_uit_migrate WHERE type = "%1"';
      CRM_Core_DAO::executeQuery($query, [
        1 => [$type, 'String', CRM_Core_DAO::QUERY_FORMAT_NO_QUOTES],
      ]);
    }
    return NULL;
  }

}
