<?php
use CRM_ctrl_uit_ExtensionUtil as E;

class CRM_ctrl_uit_BAO_UitMigrate extends CRM_ctrl_uit_DAO_UitMigrate {

  /**
   * Create a new UitMigrate based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_ctrl_uit_DAO_UitMigrate|NULL
   *
  public static function create($params) {
    $className = 'CRM_ctrl_uit_DAO_UitMigrate';
    $entityName = 'UitMigrate';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
