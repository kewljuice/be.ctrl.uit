<?php

require_once 'uit.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function uit_civicrm_config(&$config) {
  _uit_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function uit_civicrm_xmlMenu(&$files) {
  _uit_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function uit_civicrm_install() {
  _uit_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function uit_civicrm_postInstall() {
  _uit_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function uit_civicrm_uninstall() {
  _uit_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function uit_civicrm_enable() {
  // Set default settings variable.
  $settings['uit_host'] = 'https://search.uitdatabank.be/';
  CRM_Core_BAO_Setting::setItem(json_encode($settings), 'uit', 'uit-settings');
  // Set default config variable.
  $config['events'] = [
    'status' => 1,
    'event_type_id' => 1,
    //'modified' => strtotime('now'),
    'modified' => NULL,
    'params' => '',
    'limit' => 250,
  ];
  CRM_Core_BAO_Setting::setItem(json_encode($config), 'uit', 'uit-config');
  // Continue.
  _uit_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function uit_civicrm_disable() {
  // Remove variable(s).
  CRM_Core_BAO_Setting::setItem('', 'uit', 'uit-settings');
  CRM_Core_BAO_Setting::setItem('', 'uit', 'uit-config');
  // Continue.
  _uit_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function uit_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _uit_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function uit_civicrm_managed(&$entities) {
  _uit_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function uit_civicrm_caseTypes(&$caseTypes) {
  _uit_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function uit_civicrm_angularModules(&$angularModules) {
  _uit_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function uit_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _uit_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
