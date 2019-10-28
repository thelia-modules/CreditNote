<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Form\BaseForm;

/**
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 */
class CreditNoteCreateForm extends BaseForm
{
    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return 'credit-note-create';
    }

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add('action', 'text', array(
                'required' => false
            ))
            ->add('customer_id', 'integer', array(
                'required' => false
            ))
            ->add('order_id', 'integer', array(
                'required' => false
            ))
            ->add('status_id', 'integer', array(
                'required' => false
            ))
            ->add('currency_id', 'integer', array(
                'required' => false
            ))
            ->add('type_id', 'integer', array(
                'required' => false
            ))
            ->add('currency_id', 'integer', array(
                'required' => false
            ))
            ->add('total_price', 'number', array(
                'required' => false
            ))
            ->add('comment', 'text', array(
                'required' => false
            ));

        $this->formBuilder
            ->add('order_product_quantity', 'collection', array(
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ));

        $this->formBuilder
            ->add('free_amount_price', 'collection', array(
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ))
            ->add('free_amount_price_with_tax', 'collection', array(
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ))
            ->add('free_amount_tax_rule_id', 'collection', array(
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ))
            ->add('free_amount_type', 'collection', array(
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ))
            ->add('free_amount_title', 'collection', array(
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ))
            ->add('free_amount_id', 'collection', array(
                'required' => false,
                'allow_add'    => true,
                'allow_delete' => true
            ));

        $this->formBuilder
            ->add('discount_without_tax', 'number', array(
                'required' => false
            ))
            ->add('discount_with_tax', 'number', array(
                'required' => false
            ))
        ;

        $this->formBuilder->add('ui_target', ChoiceType::class, [
            'choices'  => [
                'order' => 'order',
                'customer' => 'customer'
            ]
        ]);

        $this->formBuilder->add('ui_target_id', IntegerType::class);

        $this->formBuilder
            ->add('invoice_address_id', IntegerType::class, array(
                'required' => false
            ))
            ->add('invoice_address_title', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_firstname', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_lastname', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_company', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_address1', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_address2', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_zipcode', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_city', TextType::class, array(
                'required' => false
            ))
            ->add('invoice_address_country_id', IntegerType::class, array(
                'required' => false
            ))
        ;
    }
}
