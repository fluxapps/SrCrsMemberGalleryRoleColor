<?php

namespace srag\DIC\SrCrsMemberGalleryRoleColor\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\SrCrsMemberGalleryRoleColor\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
