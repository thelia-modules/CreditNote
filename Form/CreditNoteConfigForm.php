<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Form;

use CreditNote\CreditNote;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Thelia\Form\BaseForm;

class CreditNoteConfigForm extends BaseForm
{
    protected function buildForm(): void
    {
        $this->formBuilder
            ->add(CreditNote::CONFIG_KEY_REF_PREFIX, TextType::class, [
                'label' => $this->translator->trans('Credit note reference prefix', [], CreditNote::DOMAIN_MESSAGE),
                'required' => false,
            ])
            ->add(CreditNote::CONFIG_KEY_REF_MIN_LENGTH, IntegerType::class, [
                'label' => $this->translator->trans('Credit note reference minimum length', [], CreditNote::DOMAIN_MESSAGE),
                'constraints' => [new NotBlank()],
            ])
            ->add(CreditNote::CONFIG_KEY_REF_INCREMENT, IntegerType::class, [
                'label' => $this->translator->trans('Next credit note reference number', [], CreditNote::DOMAIN_MESSAGE),
                'constraints' => [new NotBlank()],
            ])
            ->add(CreditNote::CONFIG_KEY_INVOICE_REF_PREFIX, TextType::class, [
                'label' => $this->translator->trans('Invoice reference prefix', [], CreditNote::DOMAIN_MESSAGE),
                'required' => false,
            ])
            ->add(CreditNote::CONFIG_KEY_INVOICE_REF_MIN_LENGTH, IntegerType::class, [
                'label' => $this->translator->trans('Invoice reference minimum length', [], CreditNote::DOMAIN_MESSAGE),
                'constraints' => [new NotBlank()],
            ])
            ->add(CreditNote::CONFIG_KEY_INVOICE_REF_INCREMENT, IntegerType::class, [
                'label' => $this->translator->trans('Next invoice reference number', [], CreditNote::DOMAIN_MESSAGE),
                'constraints' => [new NotBlank()],
            ])
            ->add(CreditNote::CONFIG_KEY_INVOICE_REF_WITH_THELIA_ORDER, CheckboxType::class, [
                'label' => $this->translator->trans('Use the Thelia order reference as invoice reference', [], CreditNote::DOMAIN_MESSAGE),
                'required' => false,
            ])
            ->add(CreditNote::CONFIG_KEY_ORDER_CEILING, CheckboxType::class, [
                'label' => $this->translator->trans('Limit the credit notes of an order to its total', [], CreditNote::DOMAIN_MESSAGE),
                'required' => false,
            ])
            ->add(CreditNote::CONFIG_KEY_DOCUMENT_LOCALES, TextType::class, [
                'label' => $this->translator->trans('Languages of the credit note document (locales separated by commas, en_US,fr_FR when empty)', [], CreditNote::DOMAIN_MESSAGE),
                'required' => false,
                'constraints' => [new Regex(pattern: '/^[a-z]{2}_[A-Z]{2}(,\s*[a-z]{2}_[A-Z]{2})*$/', message: $this->translator->trans('Enter locales such as fr_FR, separated by commas', [], CreditNote::DOMAIN_MESSAGE))],
            ]);
    }

    public static function getName(): string
    {
        return 'credit_note_config';
    }
}
