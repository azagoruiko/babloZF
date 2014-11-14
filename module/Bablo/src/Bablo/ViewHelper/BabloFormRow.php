<?php

namespace Bablo\ViewHelper;

use Zend\Form\Element;
use Zend\Form\View\Helper\AbstractHelper;

class BabloFormRow extends AbstractHelper {
    function __invoke(Element $element, $cssClass) {
        $view = $this->getView();
        return  "<div class=\"$cssClass\">" . $view->formLabel($element)
                . $view->formElement($element) . '</div>';
    }
}
