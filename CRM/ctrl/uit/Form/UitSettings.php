<?php

use CRM_ctrl_uit_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_ctrl_uit_Form_UitSettings extends CRM_Core_Form {

  /**
   * {@inheritdoc}
   */
  public function buildQuickForm() {
    // Get default values.
    $defaults = CRM_Core_BAO_Setting::getItem('uit', 'uit-settings');
    $decode = json_decode(utf8_decode($defaults), TRUE);
    // Fields.
    $this->add(
      'text',
      'uit_host',
      'Host',
      ['value' => $decode['uit_host']]
    );
    /*
    $this->add(
      'text',
      'uit_user',
      'User',
      ['value' => $decode['uit_user']]
    );
    $this->add(
      'text',
      'uit_pass',
      'Pass',
      ['value' => $decode['uit_pass']]
    );
    */
    $this->add(
      'text',
      'uit_key',
      'Key',
      ['value' => $decode['uit_key']]
    );
    // Buttons.
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
    ]);
    // Export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * {@inheritdoc}
   */
  public function addRules() {
    $this->addFormRule(['CRM_ctrl_uit_Form_UitSettings', 'validation']);
  }

  /**
   * {@inheritdoc}
   */
  public function postProcess() {
    // Get the submitted values as an array
    $values = $this->controller->exportValues($this->_name);
    $credentials['uit_host'] = $values['uit_host'];
    /*
    $credentials['uit_user'] = $values['uit_user'];
    $credentials['uit_pass'] = $values['uit_pass'];
    */
    $credentials['uit_key'] = $values['uit_key'];
    $encode = json_encode($credentials);
    CRM_Core_BAO_Setting::setItem($encode, 'uit', 'uit-settings');
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

  /**
   * Validation callback.
   */
  public static function validation($values) {
    $errors = [];
    if (empty($values['uit_host'])) {
      $errors['uit_host'] = ts('The host is required!');
    }
    if (empty($values['uit_key'])) {
      $errors['uit_key'] = ts('The key is required!');
    }
    return empty($errors) ? TRUE : $errors;
  }

}
