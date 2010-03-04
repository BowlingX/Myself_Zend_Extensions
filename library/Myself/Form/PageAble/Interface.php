<?php
/**
 * Myself Zend Framework Extensions
 * Copyright (c) 2009 David Heidrich
 * Myself Design Internetlösungen
 *
 * @package Myself
 * @subpackage Form
 * @subpackage PageAble
 * @version $Id: Interface.php 3M 2009-06-24 18:25:45Z (local) $
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
 * Interface für Container für Mehrseitige Formulare
 * 
 * @author 		David Heidrich (david@myself-design.com)
 * @copyright   Copyright (c) 2009 David Heidrich
 */
interface Myself_Form_PageAble_Interface
{
    /**
     * Eine Seite für die Anzeige Vorbereiten
     * 
     * @param  Myself_Form_Page_Interface $subForm
     * @param  array $decorators zusätzliche Decoratoren für dieses Subform
     * @return Myself_Form_Page_Interface
     */
    public function preparePage (Myself_Form_Page_Interface $subForm, 
        array $decorators = array());
}
