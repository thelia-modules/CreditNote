<?php

namespace CreditNote\Form;

use CreditNote\CreditNote;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Form\BaseForm;

/**
 * @author Léa Normandon <lnormandon@openstudio.fr>
 */
class CreditNoteSearchForm extends BaseForm
{
    const CREDIT_NOTE_FORM_NAME = 'credit_note_search_form';

    /**
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'ref',
                TextType::class,
                [
                    'label' => $this->translator->trans('Credit Note reference', [], CreditNote::DOMAIN_MESSAGE),
                    'label_attr' => ['for' => 'ref'],
                    'required' => false
                ]
            )
            ->add(
                'creditNoteDateMin',
                DateType::class,
                [
                    "label" => $this->translator->trans("From", [], CreditNote::DOMAIN_MESSAGE),
                    "label_attr" => ["for" => "creditNoteDateMin"],
                    "required" => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
                    "format" => "dd/MM/yyyy",
                    'html5' => false
                ]
            )
            ->add(
                'creditNoteDateMax',
                DateType::class,
                [
                    'label' => $this->translator->trans('To', [], CreditNote::DOMAIN_MESSAGE),
                    'label_attr' => ['for' => 'creditNoteDateMax'],
                    'required' => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
                    "format" => "dd/MM/yyyy",
                    'html5' => false
                ]
            );
    }

    public static function getName()
    {
        return self::CREDIT_NOTE_FORM_NAME;
    }
}
