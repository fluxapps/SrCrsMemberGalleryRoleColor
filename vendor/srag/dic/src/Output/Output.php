<?php

namespace srag\DIC\CrsMemberGalleryRoleColor\Output;

use ilAdvancedSelectionListGUI;
use ilConfirmationGUI;
use ILIAS\UI\Component\Component;
use ilModalGUI;
use ilPropertyFormGUI;
use ilTable2GUI;
use ilTemplate;
use JsonSerializable;
use srag\DIC\CrsMemberGalleryRoleColor\DICTrait;
use srag\DIC\CrsMemberGalleryRoleColor\Exception\DICException;
use stdClass;

/**
 * Class Output
 *
 * @package srag\DIC\CrsMemberGalleryRoleColor\Output
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Output implements OutputInterface {

	use DICTrait;


	/**
	 * @inheritdoc
	 */
	public function getHTML($value)/*: string*/ {
		switch (true) {
			// HTML
			case (is_string($value)):
				$html = strval($value);
				break;

			// GUI instance
			case ($value instanceof ilTemplate):
				$html = $value->get();
				break;
			case ($value instanceof ilConfirmationGUI):
			case ($value instanceof ilPropertyFormGUI):
			case ($value instanceof ilTable2GUI):
			case ($value instanceof ilModalGUI):
			case ($value instanceof ilAdvancedSelectionListGUI):
				$html = $value->getHTML();
				break;
			case ($value instanceof Component):
				$html = self::dic()->ui()->renderer()->render($value);
				break;

			// Not supported!
			default:
				throw new DICException("Class " . get_class($value) . " is not supported for output!");
				break;
		}

		return $html;
	}


	/**
	 * @inheritdoc
	 */
	public function output($value, /*bool*/
		$main = true)/*: void*/ {
		$html = $this->getHTML($value);

		if (self::dic()->ctrl()->isAsynch()) {
			echo $html;
		} else {
			if ($main) {
				self::dic()->mainTemplate()->getStandardTemplate();
			}
			self::dic()->mainTemplate()->setContent($html);
			self::dic()->mainTemplate()->show();
		}

		exit;
	}


	/**
	 * @inheritdoc
	 */
	public function outputJSON($value)/*: void*/ {
		switch (true) {
			case (is_string($value)):
			case (is_int($value)):
			case (is_double($value)):
			case (is_bool($value)):
			case (is_array($value)):
			case ($value instanceof stdClass):
			case ($value === NULL):
			case ($value instanceof JsonSerializable):
				$value = json_encode($value);

				header("Content-Type: application/json; charset=utf-8");

				echo $value;

				break;

			default:
				throw new DICException(get_class($value) . " is not a valid JSON value!");
				break;
		}

		exit;
	}
}
