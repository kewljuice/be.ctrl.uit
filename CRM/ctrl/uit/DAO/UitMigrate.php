<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2018
 *
 * Generated from
 *   /var/www/html/sites/all/civicrm/extensions/be.ctrl.uit/xml/schema/CRM/ctrl/uit/UitMigrate.xml
 *   DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:cfe2b7735f53495885d32a60450c4f00)
 */

/**
 * Database access object for the UitMigrate entity.
 */
class CRM_ctrl_uit_DAO_UitMigrate extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  static $_tableName = 'civicrm_uit_migrate';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log
   * table.
   *
   * @var bool
   */
  static $_log = TRUE;

  /**
   * Unique Source ID
   *
   * @var string
   */
  public $source_id;

  /**
   * Unique Destination ID
   *
   * @var int unsigned
   */
  public $dest_id;

  /**
   * Entity type
   *
   * @var string
   */
  public $type;

  /**
   * Status
   *
   * @var string
   */
  public $status;

  /**
   * Hash
   *
   * @var string
   */
  public $hash;

  /**
   * Modified.
   *
   * @var timestamp
   */
  public $modified;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_uit_migrate';
    parent::__construct();
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'source_id' => [
          'name' => 'source_id',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Source id'),
          'description' => 'Unique Source ID',
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'table_name' => 'civicrm_uit_migrate',
          'entity' => 'UitMigrate',
          'bao' => 'CRM_ctrl_uit_DAO_UitMigrate',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'dest_id' => [
          'name' => 'dest_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => ts('Destination id'),
          'description' => 'Unique Destination ID',
          'required' => TRUE,
          'table_name' => 'civicrm_uit_migrate',
          'entity' => 'UitMigrate',
          'bao' => 'CRM_ctrl_uit_DAO_UitMigrate',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'type' => [
          'name' => 'type',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Type'),
          'description' => 'Entity type',
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'table_name' => 'civicrm_uit_migrate',
          'entity' => 'UitMigrate',
          'bao' => 'CRM_ctrl_uit_DAO_UitMigrate',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'status' => [
          'name' => 'status',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Status'),
          'description' => 'Status',
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'table_name' => 'civicrm_uit_migrate',
          'entity' => 'UitMigrate',
          'bao' => 'CRM_ctrl_uit_DAO_UitMigrate',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'hash' => [
          'name' => 'hash',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => ts('Hash'),
          'description' => 'Hash',
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'table_name' => 'civicrm_uit_migrate',
          'entity' => 'UitMigrate',
          'bao' => 'CRM_ctrl_uit_DAO_UitMigrate',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'modified' => [
          'name' => 'modified',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => ts('Modified'),
          'description' => 'Modified.',
          'required' => TRUE,
          'table_name' => 'civicrm_uit_migrate',
          'entity' => 'UitMigrate',
          'bao' => 'CRM_ctrl_uit_DAO_UitMigrate',
          'localizable' => 0,
          'html' => [
            'type' => 'Select Date',
          ],
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in
   * fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'uit_migrate', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'uit_migrate', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }
}
