<?php

namespace srag\DIC\CrsMemberGalleryRoleColor;

use ilLogLevel;
use ilPlugin;
use srag\DIC\CrsMemberGalleryRoleColor\DIC\DICInterface;
use srag\DIC\CrsMemberGalleryRoleColor\DIC\Implementation\ILIAS52DIC;
use srag\DIC\CrsMemberGalleryRoleColor\DIC\Implementation\ILIAS53DIC;
use srag\DIC\CrsMemberGalleryRoleColor\DIC\Implementation\ILIAS54DIC;
use srag\DIC\CrsMemberGalleryRoleColor\DIC\Implementation\LegacyDIC;
use srag\DIC\CrsMemberGalleryRoleColor\Exception\DICException;
use srag\DIC\CrsMemberGalleryRoleColor\Output\Output;
use srag\DIC\CrsMemberGalleryRoleColor\Output\OutputInterface;
use srag\DIC\CrsMemberGalleryRoleColor\Plugin\Plugin;
use srag\DIC\CrsMemberGalleryRoleColor\Plugin\PluginInterface;
use srag\DIC\CrsMemberGalleryRoleColor\Version\Version;
use srag\DIC\CrsMemberGalleryRoleColor\Version\VersionInterface;

/**
 * Class DICStatic
 *
 * @package srag\DIC\CrsMemberGalleryRoleColor
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class DICStatic implements DICStaticInterface {

	/**
	 * @var DICInterface|null
	 */
	private static $dic = NULL;
	/**
	 * @var OutputInterface|null
	 */
	private static $output = NULL;
	/**
	 * @var PluginInterface[]
	 */
	private static $plugins = [];
	/**
	 * @var VersionInterface|null
	 */
	private static $version = NULL;


	/**
	 * @inheritdoc
	 */
	public static function dic()/*: DICInterface*/ {
		if (self::$dic === NULL) {
			switch (true) {
				case (self::version()->isLower(VersionInterface::ILIAS_VERSION_5_2)):
					global $GLOBALS;
					self::$dic = new LegacyDIC($GLOBALS);
					break;

				case (self::version()->isLower(VersionInterface::ILIAS_VERSION_5_3)):
					global $DIC;
					self::$dic = new ILIAS52DIC($DIC);
					break;

				case (self::version()->isLower(VersionInterface::ILIAS_VERSION_5_4)):
					global $DIC;
					self::$dic = new ILIAS53DIC($DIC);
					break;

				default:
					global $DIC;
					self::$dic = new ILIAS54DIC($DIC);
					break;
			}
		}

		return self::$dic;
	}


	/**
	 * @inheritdoc
	 */
	public static function output()/*: OutputInterface*/ {
		if (self::$output === NULL) {
			self::$output = new Output();
		}

		return self::$output;
	}


	/**
	 * @inheritdoc
	 */
	public static function plugin(/*string*/
		$plugin_class_name)/*: PluginInterface*/ {
		if (!isset(self::$plugins[$plugin_class_name])) {
			if (!class_exists($plugin_class_name)) {
				throw new DICException("Class $plugin_class_name not exists!");
			}

			if (method_exists($plugin_class_name, "getInstance")) {
				$plugin_object = $plugin_class_name::getInstance();
			} else {
				$plugin_object = new $plugin_class_name();

				self::dic()->log()->write("DICLog: Please implement $plugin_class_name::getInstance()!", ilLogLevel::DEBUG);
			}

			if (!$plugin_object instanceof ilPlugin) {
				throw new DICException("Class $plugin_class_name not extends ilPlugin!");
			}

			self::$plugins[$plugin_class_name] = new Plugin($plugin_object);
		}

		return self::$plugins[$plugin_class_name];
	}


	/**
	 * @inheritdoc
	 */
	public static function version()/*: VersionInterface*/ {
		if (self::$version === NULL) {
			self::$version = new Version();
		}

		return self::$version;
	}


	/**
	 * DICStatic constructor
	 */
	private function __construct() {

	}
}
