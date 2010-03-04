<?php
/**
 * Myself Zend Framework Extensions
 * Copyright (c) 2009 David Heidrich
 * Myself Design Internetlösungen
 *
 * @package Myself
 * @version $Id: Page.php 3M 2009-06-24 18:25:45Z (local) $
 * 
 * This file is part of Myself_Lib.
 * 
 * Myself_Lib is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *   
 * Myself_Lib is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Myself_Lib. If not, see <http://www.gnu.org/licenses/>.
 * 
 */
/**
 * Seite eines Mehrseitigen Formulares
 * 
 * @subpackage Form
 * 
 * @author 		David Heidrich (david@myself-design.com)
 * @copyright   Copyright (c) 2009 David Heidrich
 */
class Myself_Form_Page extends Zend_Form 
    implements Myself_Form_Page_Interface
{
    /**
     * Array Notation
     * 
     * @see Zend_Form_SubForm
     * @var boolean
     */
    protected $_isArray = true;
    /**
     * Namensraum dieser Formularseite
     *
     * @var Zend_Session_Namespace
     */
    protected $_namespace;
    /**
     * @var array
     */
    protected $_display_criterias = array();
    /**
     * Routenname für Formularhandling
     * 
     * @var string
     */
    private static $_progress_route;
    /**
     * View Script welches zum Anzeigen der Formularseite benutzt wird
     * 
     * @var string
     */
    private $_render_script;
    /**
     * Legt den Namensraum fest
     */
    public function init ()
    {
        $this->_namespace = Myself_Form_PageAble::getNamespace();
    }
    /**
     * Gibt den Formularnamen zurück
     * @return string
     */
    public function getPageName ()
    {
        return $this->getName();
    }
    /**
     * Legt das Rendering Script fest welches für dieses Formular benutzt wird:
     * @param string $script
     */
    public function setRenderScript ($script)
    {
        $this->_render_script = $script;
    }
    /**
     * Gibt das Rendering Script für dieses Formular zurück
     * @return string
     */
    public function getRenderScript ()
    {
        return $this->_render_script;
    }
    /**
     * @param string $name
     * @return Myself_Form_Page
     */
    public static function setRouteName ($name)
    {
        self::$_progress_route = $name;
    }
    /**
     * Ermittelt den Routennamen
     * 
     * @return string
     */
    private static function _getRouteName ()
    {
        return self::$_progress_route;
    }
    /**
     * Gibt alle Subformen des PageAble Formulares zurück
     * 
     * @return array
     */
    public function getAllPages ()
    {
        $parent = Myself_Form_PageAble::getMainForm();
        return $parent->getSubForms();
    }
    /**
     * Gibt alle Schlüssel der ParentSubForms zurück
     * 
     * @return array
     */
    public function getKeysOfOtherPages ()
    {
        return array_keys($this->getAllPages());
    }
    /**
     * Position dieser Seite im Stack zurückgeben
     * 
     * @return int|null wenn nichts gefunden
     */
    public function getPosition ()
    {
        $pos = 0;
        foreach ($this->getKeysOfOtherPages() as $key) {
            $pos ++;
            if ($this->getName() === $key) {
                return $pos;
            }
        }
        return null;
    }
    /**
     * Gibt das Hauptformular zurück
     * @return Myself_Form_PageAble
     */
    public function getMainForm ()
    {
        return Myself_Form_PageAble::getMainForm();
    }
    /**
     * Gibt den Session Context für das gesamte Formular zurück
     * @return Zend_Session_Namespace
     */
    public function getContext ()
    {
        return $this->getMainForm()->getNamespace();
    }
    /**
     * Ist die Seite verfügbar?
     * 
     * Von Kindklassen zu überschreiben
     * @return boolean
     */
    public function isAvailable ()
    {
        foreach ($this->getDisplayCriterias() as $crit) {
            if (! $crit->isAvailable($this->getMainForm())) {
                return false;
            }
        }
        return true;
    }
    /**
     * Fügt ein Anzeigekriterium hinzu
     * 
     * @param Myself_Form_Page_Display_Criteria_Interface $displayCriteria
     * @return Myself_Form_Page
     */
    public function addDisplayCriteria (Myself_Form_Page_Display_Criteria_Interface $displayCriteria)
    {
        $this->_display_criterias[] = $displayCriteria;
        return $this;
    }
    /**
     * Ermittelt die Anzeigekriterien
     * @return array
     */
    public function getDisplayCriterias ()
    {
        return $this->_display_criterias;
    }
    /**
     * Gibt eine freundliche URL für das Formhandling zurück
     * 
     * @param string|null page
     * @return string
     */
    public function getRewriteProgressUrl ($page = null)
    {
        $page = (null === $page) ? $this->getPosition() + 1 : $page;
        $controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        if (null !== self::_getRouteName()) {
            $helper = new Zend_View_Helper_Url();
            $url = $helper->url(array('controller' => $controller , 'page' => $page), self::_getRouteName());
        } else {
            $url = '/' . $controller . '/process/page/' . $page;
        }
        return $url;
    }
}