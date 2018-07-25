<?php

class CRM_Utils_Uit {

  static function Uit($op, $objectName, $id, &$params) {
    $arg5 = NULL;
    $arg6 = NULL;
    return CRM_Utils_Hook::singleton()
      ->invoke(4, $op, $objectName, $id, $params, $arg5, $arg6, 'civicrm_uit');
  }

}