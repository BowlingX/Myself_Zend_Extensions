<?php
/**
 * Myself Zend Framework Extensions
 * Copyright (c) 2009 David Heidrich
 * Myself Design Internetlösungen
 *
 * @package Myself
 * @subpackage Form
 * @subpackage Page
 * @version $Id: Controller.php 3M 2009-06-24 18:25:45Z (local) $
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
 * Action Controller für mehrseitige Formulare
 * Handelt mehrseitige Formulare
 * 
 * @code
 * <?php
 * // Beispielanwendung
 * class MyController extends Myself_Form_Page_Abstract
 * {
 * 
 *   public function getForm ()
 *   {
 *       if (null === $this->_form) {
 *           $this->_form = new Myself_Form_Beispiel();
 *       }
 *       return $this->_form;
 *   }
 *
 *    public function handleData(Myself_Form_PageAble $form){
 *      var_dump($form->getValues());       
 *       return $this->_forward('complete');
 *   }
 *   
 *  public function completeAction(){
 *       
 *   }
 * }
 * 
 * ?>
 * @endcode
 * 
 * @author 		David Heidrich (david@myself-design.com)
 * @copyright   Copyright (c) 2009 David Heidrich
 * 
 * @see Myself_Form_Page_Interface
 * @see Myself_Form_PageAble
 */
abstract class Myself_Form_Page_Controller extends Zend_Controller_Action
{
    /**
     * @desc Session Namespace Ablauf
     */
    const SESSION_EXPIRE = 300;
    /**
     * Das Vollständige Formular dieses Controllers
     * @var Myself_Form
     */
    protected $_form = null;
    /**
     * Der Jeweilige Session Namespace für dieses Mehrseitige Formular
     *
     * @var Myself_Session_Namespace
     */
    protected $_form_session_namespace = null;
    /**
     * Der Namespace für die Datenspeicherung
     * Pro Formular sollte ein anderer verwendet werden!
     * @var string
     */
    protected $_form_namespace_name;
    /**
     * Initialisiert diesen Action Controller
     */
    public function init ()
    {
        // Namensraum für die Formulare dieses Controllers angeben:
        Myself_Form_PageAble::setNamespace($this->getSessionNamespace());
        // Parentformular setzen:
        Myself_Form_PageAble::setMainForm($this->getForm());
    }
    /**
     * Setzt das mehrseitige Formular
     * 
     * @param Myself_Form_PageAble_Interface $form
     * @return Myself_Form_Page_Controller
     */
    public function setForm (Myself_Form_PageAble_Interface $form)
    {
        $this->_form = $form;
        return $this;
    }
    /**
     * Ermittelt das mehrseitige Formular
     * @return Myself_Form_PageAble_Interface
     */
    public function getForm ()
    {
        return $this->_form;
    }
    /**
     * Gibt den Namensraum für dieses Formular zurück
     *
     * @return Myself_Session_Namespace
     */
    protected function getSessionNamespace ()
    {
        if (null === $this->_form_session_namespace) {
            // Für den Namensraum entweder die Klasse oder einen bestimmten Namen benutzen:
            $namespace = ($this->_form_namespace_name !== null) ? 
                $this->_form_namespace_name : get_class($this);
            $this->_form_session_namespace = new Zend_Session_Namespace($namespace);
            // Ablaufzeit für die mehrseitigen Formulare
            $this->getSessionNamespace()->setExpirationSeconds(self::SESSION_EXPIRE);
            return $this->_form_session_namespace;
        } else
            return $this->_form_session_namespace;
    }
    /**
     * Setzt den Namensraum Namen für dieses Multipage Formular
     *
     * @param string $name
     */
    protected function setNamespaceName ($name)
    {
        $this->_form_namespace_name = $name;
    }
    /**
     * Ermittelt alle Namen der gespeicherten Formulare dieses Controllers
     * @return array
     */
    protected function getStoredPages ()
    {
        $pages = array();
        // Alle Seiten(namen) dieses Formulares ermitteln:
        $forms = $this->getSessionNamespace();
        foreach ($forms as $key => $value) {
            if (null !== $value) {
                $pages[] = $key;
            }
        }
        return $pages;
    }
    /**
     * Gibt eine Liste aller verfügbaren SubForms (Seiten) zurück
     * @return array
     */
    protected function getAvailableForms ()
    {
        $form = $this->getForm();
        // Alle Namen an unterseiten ermitteln:
        return array_keys($form->getSubForms());
    }
    /**
     * Ermittelt die gespeicherten Formulardaten für eine Seite
     *
     * @param Myself_Form_Page $page
     * @return array
     */
    protected function getSavedFormData (Myself_Form_Page $page)
    {
        $name = $page->getName();
        $form_data = $this->getSessionNamespace()->$name;
        return $form_data;
    }
    /**
     * Diese Methode prüft ob die anderen Formulare bereits ausgefüllt wurden
     * und diese Seite erlaubt ist!
     * @param int $page
     * @return boolean 
     */
    protected function pageIsAvailable ($page)
    {
        $saved_forms = $this->getStoredPages();
        // Außerdem muss das Formular ausgefüllt sein!
        $form = $this->getNextPageFormById($page);
        if ($form !== null) {
            if ((count($saved_forms) >= $page - 1)) {
                return true;
            } else
                return false;
        } else
            return false;
    }
    /**
     * Aktuelles Formular ermitteln:
     * @return false|Myself_Form_Page
     */
    public function getCurrentPageForm ()
    {
        // Request ermitteln:
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return false;
        }
        // Alle in Frage kommenden Formulare ermitteln:
        foreach ($this->getAvailableForms() as $name) {
            $data = $request->getPost($name, false);
            if ($data) {
                if (is_array($data)) {
                    $form = $this->getForm()->getSubForm($name);
                    // BUG: Achtung...Hier ausdrücklich die Post Daten verwenden!
                    if ($this->pageIsAvailable($form->getPosition())) {
                        return $this->getForm()->getSubForm($name);
                    }
                    break;
                }
            }
        }
        return false;
    }
    /**
     * Ermittelt das nächste Formular nach Name:
     * @param string $name
     * @return Myself_Form_Page|null
     */
    public function getNextPageFormById ($id)
    {
        $availForms = $this->getForm()->getSubForms();
        $count = 1;
        foreach ($availForms as $form) {
            if ($count == $id) {
                return $form;
            }
            $count ++;
        }
        return null;
    }
    /**
     * Das nächste Formular (nächste Seite) für die Anzeige ermitteln:
     * @return Myself_Form_Page
     */
    public function getNextPageForm ()
    {
        // Bereits gespeicherten Formulare ermitteln:
        $storedForms = $this->getStoredPages();
        // Dann alle verfügbaren Formulare ermitteln:
        $potentialForms = $this->getAvailableForms();
        // Abgleich, prüfen welches noch nicht im Speicher ist, und dann zurückgeben
        foreach ($potentialForms as $name) {
            if (! in_array($name, $storedForms)) {
                $form = $this->getForm()->getSubForm($name);
                if (! empty($storedForms)) {
                    $data = $this->getSavedFormData($form);
                } else
                    $data = null;
                if (is_array($data)) {
                    $form->populate($data);
                }
                return $form;
            }
        }
        $form = $this->getNextPageFormById(1);
        if (! empty($storedForms)) {
            $data = $this->getSavedFormData($form);
        } else
            $data = null;
        if (is_array($data)) {
            $form->populate($data);
        }
        return $form;
    }
    /**
     * Prüft ob eine bestimmte Seite des Formulares gültig ist
     * Speichert die Daten dann in die Session
     * @param  Myself_Form_Page $page 
     * @param  array $data 
     * @return bool
     */
    public function subFormIsValid (Myself_Form_Page $page, array $data)
    { // Name ermitteln
        $name = $page->getName();
        // Wenn diese Formularseite Gültig ist, wird es in den Session Namensraum geschrieben
        if ($page->isValid($data)) {
            if ($this->pageIsAvailable($page->getPosition())) {
                $this->getSessionNamespace()->$name = $page->getValues(true);
                return true;
            }
        }
        return false;
    }
    /**
     * Ist die komplette Form (mit allen enthaltenen Seiten) gültig
     * @return boolean
     */
    public function formIsValid ()
    {
        $data = array();
        foreach ($this->getSessionNamespace() as $key => $info) {
            $data[$key] = $info;
        }
        $stored = count($this->getStoredPages());
        $avail = count($this->getAvailableForms());
        if ($stored < $avail) {
            return false;
        } else {
            $form = $this->getForm();
            return $form->isValid($data);
        }
    }
    /**
     * Default Index Action für die Formulare:
     */
    public function indexAction ()
    {
        // Aktuelle Seite ermitteln:
        $page = $this->getRequest()->getParam('page');
        // Formular dazu ermitteln:
        // Entweder die aktuelle Seite nochmals anzeigen, oder
        // die nächste "next" (erste) Subform holen
        if (! $form = $this->getCurrentPageForm()) {
            $form = $this->getNextPageForm();
        }
        if (isset($page)) {
            // Hier jetzt noch prüfen ob alle anderen Formulare bereits abgearbeitet wurden:
            if ($this->pageIsAvailable($page)) {
                $form = $this->getNextPageFormById($page);
                // Wenn eine Seite angegeben ist:
                // Und es dazu auch ein Formular gibt:
                if ($form !== null) {
                    // Daten aus der Session in das Formular laden:
                    $name = $form->getName();
                    $data = $this->getSessionNamespace()->$name;
                    if (is_array($data)) {
                        $form->populate($data);
                    }
                } else
                    $form = $this->getNextPageForm();
            } else
                $form = $this->getNextPageForm();
        }
        $this->view->form = $this->getForm()->preparePage($form);
        $this->render($form->getRenderScript());
    }
    /**
     * Managed Alle mehrseitigen Formulare
     * @return void
     */
    public function processAction ()
    {
        // Formular ermitteln: 
        $form = $this->getCurrentPageForm();
        if ($this->getRequest()->isPost()) {
            $back_form = $this->getNextPageFormById($this->getRequest()->getParam('page') - 1);
            // Hinweis: Hier wird auch die Value geprüft, wenn normale buttons benutzt werden muss die also 1 sein!
            $button_pref = $this->getRequest()->getParam($back_form->getName());
            $backButtonName = $this->getForm()->getBackButton()->getName();
            if (isset($button_pref[$backButtonName])) {
                // Ein Schritt zurück gehen: (Nächste -1 (aktuelle) -1 (vorgänger))
                $this->_redirect($back_form->getRewriteProgressUrl($this->getRequest()->getParam('page') - 2));
            }
        }
        // Keine Daten übermittelt?
        if (! $form) {
            // Zurück zum Startformular leiten:
            return $this->indexAction();
        } // Ansonsten Prüfen wir ob das abgeschickte Formular Valide ist:
        if (! $this->subFormIsValid($form, $this->getRequest()->getPost())) {
            $this->view->form = $this->getForm()->preparePage($form);
            return $this->render($form->getRenderScript());
        } // Wenn ja, ist das ganze Formular schon Valide?
        if (! $this->formIsValid()) {
            return $this->indexAction();
        } else {
            // Letztendlich ist das Formular gültig und die Daten müssen gehandelt werden:
            $this->handleData($this->getForm());
            $this->getSessionNamespace()->unsetAll();
            return null;
        }
    }
}