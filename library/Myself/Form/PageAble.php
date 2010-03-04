<?php
/**
 * Myself Zend Framework Extensions
 * Copyright (c) 2009 David Heidrich
 * Myself Design Internetlösungen
 *
 * @package Myself
 * @version $Id: PageAble.php 3M 2009-06-24 18:25:45Z (local) $
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
 * Ein Formular welches beliebig viele Seiten eines Formulares aufnehmen kann
 * 
 * Beispiel:
 * @code
 * <?php
 * 
 * class Zend_Form_Demo extends Myself_Form_PageAble
 * {
 * 		
 * 		public function createForm(){
 * 		
 * 			$form = new Myself_Form_Page();
 * 			//...Elemente hinzufügen etc.
 * 			$this->addPages(array('erste' => $form));
 * 		
 * 		}
 * 	
 * }
 * 
 * ?>
 * @endcode
 * @subpackage Form
 * @see Myself_Form_PageAble_Interface
 * @abstract 
 * @author 		David Heidrich (david@myself-design.com)
 * @copyright   Copyright (c) 2009 David Heidrich
 */
abstract class Myself_Form_PageAble extends Zend_Form implements Myself_Form_PageAble_Interface
{
    /**
     * Namensraum für alle PageAble Instanzen:
     * @var J3k_Session_Namespace
     */
    protected static $_namespace = null;
    /**
     * Enthält das Eltern Formular aller Seiten
     * @var Myself_Form
     */
    protected static $_main_form = null;
    /**
     * Zurück Button
     * @var Zend_Form_Element
     */
    private $_back_btn;
    /**
     * Vorwärts Button
     * @var Zend_Form_Element
     */
    private $_submit_btn;
    /**
     * Initialisiert Buttons
     * 
     */
    public function init ()
    {
        // Weiter
        $next = new Zend_Form_Element_Button('next_step', 
            array('label' => 'next' , 'class' => 'form-next-btn' , 'type' => 'submit'));
        $this->setSubmitButton($next);
        // Zurück: 
        $prev = new Zend_Form_Element_Button('prev_step', 
            array('label' => 'back' , 'class' => 'form-back-btn' , 'type' => 'submit'));
        $this->setBackButton($prev);
        // Formular erstellen:
        $this->createForm();
    }
    /**
     * Zu implementierende Methode um ein mehrseitiges Formular zu erstellen
     */
    public abstract function createForm ();
    /**
     * Definiert den Namensraum für diese Instanz
     * Pro (PageAble) Formular ist nur eine Instanz zulässig
     * 
     * @param Zend_Session_Namespace $namespace
     * @return void
     */
    public static function setNamespace (Zend_Session_Namespace $namespace)
    {
        if (self::$_namespace === null) {
            self::$_namespace = $namespace;
        }
    }
    /**
     * Basis Formular setzen:
     * @param Myself_Form_PageAble $main
     */
    public static function setMainForm (Myself_Form_PageAble_Interface $main)
    {
        if (self::$_main_form === null) {
            self::$_main_form = $main;
        }
    }
    /**
     * Ermittelt das Eltern Formular für alle Seiten
     * @return Myself_Form_PageAble
     */
    public static function getMainForm ()
    {
        return self::$_main_form;
    }
    /**
     * Namensraum ermitteln
     * @return Zend_Session_Namespace
     */
    public static function getNamespace ()
    {
        return self::$_namespace;
    }
    /**
     * Eine Subform für die Anzeige Vorbereiten
     * 
     * @param  Myself_Form_Page_Interface $spec 
     * @param  array $decorators zusätzliche Decoratoren für dieses Subform
     * @return Myself_Form_Page_Interface
     */
    public function preparePage (Myself_Form_Page_Interface $subForm, array $decorators = array())
    {
        // Zusätzliche Dekoratoren und Optionen setzen:     
        $this->addDecorators($decorators)->addButtons($subForm)->addSubFormActions($subForm);
        // Render Scripte Setzen, wenn nicht gesetzt:
        if (null === $subForm->getRenderScript()) {
            $subForm->setRenderScript($subForm->getName());
        }
        return $subForm;
    }
    /**
     * Legt den Button für den nächsten Schritt fest
     *
     * @param Zend_Form_Element $btn
     */
    public function setSubmitButton (Zend_Form_Element $btn)
    {
        $this->_submit_btn = $btn;
        return $this;
    }
    /**
     * Legt den Button für einen Schritt zurück fest
     *
     * @param Zend_Form_Element $btn
     */
    public function setBackButton (Zend_Form_Element $btn)
    {
        $this->_back_btn = $btn;
        return $this;
    }
    /**
     * Gibt den Submit Button zurück
     *
     * @return Zend_Form_Element
     */
    public function getSubmitButton ()
    {
        return $this->_submit_btn;
    }
    /**
     * Gibt den "Zurück" Button zurück
     * 
     * @return Zend_Form_Element
     */
    public function getBackButton ()
    {
        return $this->_back_btn;
    }
    /**
     * Fügt einen vor und einen Zurück Button in die Formular ein
     * Hinweis: Bei der ersten Seite wird standardmäßig nur einer gesetzt
     * 
     * @param Myself_Form_Page $subForm
     * @param array $labels
     * @return Myself_Form_PageAble_Interface
     */
    public function addButtons (Myself_Form_Page $subForm)
    {
        $subForm->addElement($this->getSubmitButton());
        if ($subForm->getPosition() > 1)
            $subForm->addElement($this->getBackButton());
        return $this;
    }
    /**
     * Ermittelt ein spezifisches Subform (Subpage)
     * @param string $name
     * @return Myself_Form_Page_Interface
     */
    public function getSubForm ($name)
    {
        $form = parent::getSubForm($name);
        if (null !== $form) {
            if ($form->isAvailable()) {
                return $form;
            } else {
                // Remove form data from namespace
                unset($this->getNamespace()->{$form->getName()});
            }
        }
        return null;
    }
    /**
     * Ermittelt alle Subformen (Subpages)
     * @return Myself_Form_Page_Interface
     */
    public function getSubForms ()
    {
        $subForms = parent::getSubForms();
        $newForms = array();
        foreach ($subForms as $form) {
            if (! $form->isAvailable()) {
                // Remove form data from namespace
                unset($this->getNamespace()->{$form->getName()});
                continue;
            }
            $newForms[$form->getName()] = $form;
        }
        return $newForms;
    }
    /**
     * Wrapper um neue Subforms hinzuzufügen
     * Terminologie geändert da wir Seiten und in dem Sinne keine Subforms haben
     * 
     * @param array $pages
     * @return Myself_Form_PageAble
     * @see Zend_Form#addSubForm()
     */
    public function addPages (array $pages)
    {
        $this->addSubForms($pages);
        return $this;
    }
    /**
     * Setting defaults
     * Values are written to session namespace & form is beeing populatet
     * 
     * @param array $values
     * @return Myself_Form_PageAble
     */
    public function setSessionDefaults (array $values)
    {
        parent::setDefaults($values);
        self::setMainForm($this);
        // Vorher alles rausschmeißen:
        $this->getNamespace()->unsetAll();
        foreach ($this->getSubForms() as $form) {
            $this->getNamespace()->{$form->getName()} = $form->getValues(true);
        }
        return $this;
    }
    /**
     * Fügt eine einzelne Seite hinzu
     * Proxy für addSubForm
     * 
     * @param Myself_Form_Page $page
     * @param string $name Formularname
     * @param int $order 
     * @return Myself_Form_PageAble
     * @see Zend_Form#addSubForm()
     */
    public function addPage (Myself_Form_Page $page, $name, $order = null)
    {
        $this->addSubForm($page, $name, $order);
        return $this;
    }
    /**
     * Aktion und Methode der Subform hinzufügen
     * 
     * @param  Myself_Form_Page $subForm 
     * @return Myself_Form_PageAble
     */
    public function addSubFormActions (Myself_Form_Page $subForm)
    {
        // URL für den nächsten Step eintragen:
        $subForm->setAction($subForm->getRewriteProgressUrl())->setMethod('post');
        return $this;
    }
}
