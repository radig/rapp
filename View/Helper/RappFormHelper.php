<?php
App::uses('Localize', 'Locale.Lib');
App::uses('BootstrapFormHelper', 'TwitterBootstrap.View/Helper');

/**
 * Um helper que implementa de forma transparente a localização
 * de dados em um formulário, através da Lib Lozalize.
 *
 * @package       rapp
 * @subpackage    rapp.helper
 */
class RappFormHelper extends BootstrapFormHelper {

	/**
	 * Sobrecarga do método input incluindo funcionalidades
	 * extras, tais como:
	 *
	 * 1 - Localização automática dos tipos date/datetime e numérico/float
	 * 2 - Formatação baseada no TwitterBootstrap
	 *
	 */
	public function input($fieldName, $options = array()) {
		$this->setEntity($fieldName);
		$modelKey = $this->model();
		$fieldKey = $this->field();

		$fieldDef = $this->_introspectModel($modelKey, 'fields', $fieldKey);

		$value = null;
		if(isset($options['value'])) {
			$value = $options['value'];
		} else if(isset($this->request->data[$modelKey][$fieldKey])) {
			$value = $this->request->data[$modelKey][$fieldKey];
		}

		if((!isset($options['localize']) || $options['localize'] === true) && !empty($value)) {
			switch ($fieldDef['type']) {
				case 'date':
					$value = Localize::date($value);
					break;
				case 'datetime':
				case 'timestamp':
					$value = Localize::datetime($value);
					break;
				case 'float':
					$value = Localize::number($value);
					break;
			}

			$options['value'] = $value;
		}

		if((!isset($options['keepSeconds']) || $options['keepSeconds'] === true) && $fieldDef['type'] === 'time') {
			$options['value'] = (empty($value) || !preg_match('/[0-9]{2}\:[0-9]{2}(\:[0-9]{2})?/')) ? '00:00' : substr($value, 0, 5);
		}

		if(!isset($options['useBootstrap']) || $options['useBootstrap'] === true) {
			/**
			 * @todo Helper não deve estender o BootstrapForm, mas
			 * sim usa-lo quando definido.
			 */
		}

		return parent::input($fieldName, $options);
	}
}