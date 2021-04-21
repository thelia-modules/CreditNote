<?php

namespace CreditNote\Form;

use CreditNote\CreditNote;
use Thelia\Form\BaseForm;

/**
 * @author Léa Normandon <lnormandon@openstudio.fr>
 */
class CreditNoteSearchForm extends BaseForm
{
    const CREDIT_NOTE_FORM_NAME = 'credit-note-search-form';

    /**
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'ref',
                'text',
                [
                    'label' => $this->translator->trans('Credit Note reference', [], CreditNote::DOMAIN_MESSAGE),
                    'label_attr' => ['for' => 'ref'],
                    'required' => false
                ]
            )
            ->add(
                'creditNoteDateMin',
                "date",
                [
                    "label" => $this->translator->trans("From", [], CreditNote::DOMAIN_MESSAGE),
                    "label_attr" => ["for" => "creditNoteDateMin"],
                    "required" => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
                    "format" => "dd/MM/yyyy"
                ]
            )
            ->add(
                'creditNoteDateMax',
                'date',
                [
                    'label' => $this->translator->trans('To', [], CreditNote::DOMAIN_MESSAGE),
                    'label_attr' => ['for' => 'creditNoteDateMax'],
                    'required' => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
                    "format" => "dd/MM/yyyy"
                ]
            );
    }

    public function getName()
    {
        return self::CREDIT_NOTE_FORM_NAME;
    }
}
