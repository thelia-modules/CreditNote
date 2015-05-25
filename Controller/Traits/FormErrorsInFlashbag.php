<?php

namespace CreditNote\Controller\Traits;

use Symfony\Component\Form\FormInterface;

/**
 * Provide methods to manage form errors in the session flashbag.
 * Apply to a BaseController.
 */
trait FormErrorsInFlashbag
{
    /**
     * Set the errors messages of a form in the session flashbag.
     * Keys are of the form 'form_name.field_name.subfield_name.errors'.
     * Values are an array of the error messages.
     *
     * @param FormInterface $form Form to use.
     * @param string $baseKey Base flashbag key (used for recursion).
     */
    protected function setFormErrorsInFlashbag(FormInterface $form, $baseKey = "")
    {
        if ($form->isValid()) {
            return;
        }

        if ($form->isRoot()) {
            $baseKey = $form->getName();
        } else {
            $baseKey .= ".";
            $baseKey .= $form->getName();
        }

        $formErrorMessages = [];
        foreach ($form->getErrors() as $formError) {
            $formErrorMessages[] = $formError->getMessage();
        }

        if (!empty($formErrorMessages)) {
            $this->getSession()->getFlashBag()->set($baseKey . ".errors", $formErrorMessages);
        }

        /** @var FormInterface $children */
        foreach ($form as $children) {
            $this->setFormErrorsInFlashbag($children, $baseKey);
        }
    }
}
