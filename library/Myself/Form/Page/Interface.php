<?php
/**
 * Myself Zend Framework Extensions
 * Copyright (c) 2009 David Heidrich
 * Myself Design Internetlösungen
 *
 * @package Myself
 * 
 * @version $Id: Interface.php 3 2009-06-24 18:25:45Z david $
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
 * Interface für mehrseitige Formulare
 * Stellt den Sendeprozess für die Controller zu verfügung
 * 
 * @subpackage Form
 * @subpackage Page
 * @author 		David Heidrich (david@myself-design.com)
 * @copyright   Copyright (c) 2009 David Heidrich
 */
// namespace com\myself\design\form
interface Myself_Form_Page_Interface
{
    /**
     * Gibt den Formularnamen zurück
     * @return string
     */
    public function getPageName ();
    /**
     * Gibt alle Subformen des PageAble Formulares zurück
     * @return array
     */
    public function getAllPages ();
    /**
     * Formular verfügbar?
     * @return boolean
     */
    public function isAvailable ();
    /**
     * Position dieser Seite im Stack zurückgeben
     * @return int|null wenn nichts gefunden
     */
    public function getPosition ();
    /**
     * Gibt eine freundliche URL für das Formhandling zurück
     * 
     * @param string|null page
     * @return string
     */
    public function getRewriteProgressUrl ();
    /**
     * Gibt das Rendering Script für dieses Formular zurück
     * @return string
     */
    public function getRenderScript ();
    /**
     * Kontext der Seite
     * @return Zend_Session_Namespace
     */
    public function getContext ();
}