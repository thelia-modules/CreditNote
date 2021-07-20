<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Controller;

use CreditNote\Form\CreditNoteCreateForm;
use CreditNote\Form\CreditNoteSearchForm;
use CreditNote\Helper\CreditNoteHelper;
use CreditNote\Helper\CriteriaSearchHelper;
use CreditNote\Model\Base\CreditNoteStatusQuery;
use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteAddress;
use CreditNote\Model\CreditNoteComment;
use CreditNote\Model\CreditNoteDetail;
use CreditNote\Model\CreditNoteDetailQuery;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use CreditNote\Model\Map\CreditNoteTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Propel;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\PdfEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Template\TemplateHelperInterface;
use Thelia\Core\Thelia;
use Thelia\Core\Translation\Translator;
use Thelia\Exception\TheliaProcessException;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Log\Tlog;
use Thelia\Model\AddressQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\CurrencyQuery;
use Thelia\Model\Customer;
use Thelia\Model\CustomerQuery;
use Thelia\Model\Map\AddressTableMap;
use Thelia\Model\Map\OrderAddressTableMap;
use Thelia\Model\Order;
use Thelia\Model\OrderProductTax;
use Thelia\Model\OrderQuery;
use Thelia\Model\ProductSaleElementsQuery;
use Thelia\Model\TaxRuleQuery;
use CreditNote\CreditNote as CreditNoteModule;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="credit_note")
 * @author Gilles Bourgeat <gilles.bourgeat@gmail.com>
 */
class CreditNoteController extends BaseAdminController
{
    use CriteriaSearchHelper;

    /**
     * @param Request $request
     * @return \Thelia\Core\HttpFoundation\Response
     * @Route("/credit-note", name="_list", methods="GET")
     */
    public function listAction(Request $request)
    {
        return $this->render(
            "credit-note-list",
            [

            ]
        );
    }

    /**
     * @Route("/credit-note/create", name="_create", methods="POST")
     */
    public function createAction(Request $request, EventDispatcherInterface $eventDispatcher, ParserContext $parserContext, SecurityContext $securityContext)
    {
        $creditNote = $this->performCreditNote($eventDispatcher, $parserContext, $securityContext);

        $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        $con->beginTransaction();

        try {
            $creditNote->save();
            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            throw $e;
        }

        if (null !== $request->get('success-url')) {
            return new RedirectResponse($request->get('success-url'));
        }

        if (null !== $creditNote->getOrder()) {
            return $this->generateRedirectFromRoute(
                'admin.order.update.view',
                [
                    'tab' => 'credit-note'
                ],
                [
                    'order_id' => $creditNote->getOrder()->getId()
                ]
            );
        }

        if (null !== $creditNote->getCustomer()) {
            return $this->generateRedirectFromRoute('admin.customer.update.view', [], [
                'customer_id' => $creditNote->getCustomer()->getId()
            ]);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Thelia\Core\HttpFoundation\Response
     * @Route("/credit-note/{id}", name="_view", methods="POST")
     */
    public function viewAction(Request $request, $id, EventDispatcherInterface $eventDispatcher, ParserContext $parserContext, SecurityContext $securityContext)
    {
        $creditNote = CreditNoteQuery::create()
            ->filterById($id, Criteria::EQUAL)
            ->findOne();

        $creditNote = $this->performCreditNote($eventDispatcher, $parserContext, $securityContext, $creditNote);

        return $this->render("ajax/credit-note-modal", [
            'creditNote' => $creditNote
        ]);
    }

    /**
     * @Route("/credit-note/{id}/_update", name="_update", methods="POST")
     */
    public function updateAction(Request $request, $id, EventDispatcherInterface $eventDispatcher, ParserContext $parserContext, SecurityContext $securityContext)
    {
        $creditNote = CreditNoteQuery::create()
            ->filterById($id, Criteria::EQUAL)
            ->findOne();

        $creditNote = $this->performCreditNote($eventDispatcher, $parserContext, $securityContext, $creditNote);

        $con = Propel::getServiceContainer()->getWriteConnection(CreditNoteTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        $con->beginTransaction();

        try {
            $creditNote->save();
            $con->commit();
        } catch (\Exception $e) {
            $con->rollBack();
            throw $e;
        }

        if (null !== $request->get('success-url')) {
            return new RedirectResponse($request->get('success-url'));
        }

        if (null !== $creditNote->getOrder()) {
            return $this->generateRedirectFromRoute(
                'admin.order.update.view',
                [
                    'tab' => 'credit-note'
                ],
                [
                    'order_id' => $creditNote->getOrder()->getId()
                ]
            );
        }

        if (null !== $creditNote->getCustomer()) {
            return $this->generateRedirectFromRoute('admin.customer.update.view', [], [
                'customer_id' => $creditNote->getCustomer()->getId()
            ]);
        }
    }

    /**
     * @Route("/credit-note/{id}/_delete", name="_delete", methods="POST")
     */
    public function deleteAction(Request $request, $id, Translator $translator)
    {
        $creditNote = CreditNoteQuery::create()->findOneById($id);

        if (!empty($creditNote->getInvoiceRef())) {
            $request->getSession()->getFlashBag()->set(
                'error',
                $translator->trans(
                    "You can not delete this credit note"
                )
            );
        } else {
            CreditNoteQuery::create()->filterById($id)->delete();
        }

        if (null !== $request->get('success-url')) {
            return new RedirectResponse($request->get('success-url'));
        }

        if (null !== $creditNote->getOrder()) {
            return $this->generateRedirectFromRoute(
                'admin.order.update.view',
                [
                    'tab' => 'credit-note'
                ],
                [
                    'order_id' => $creditNote->getOrder()->getId()
                ]
            );
        }

        if (null !== $creditNote->getCustomer()) {
            return $this->generateRedirectFromRoute('admin.customer.update.view', [], [
                'customer_id' => $creditNote->getCustomer()->getId()
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Thelia\Core\HttpFoundation\Response
     * @Route("/credit-note/ajax/modal/create", name="_ajax_create", methods="POST")
     */
    public function ajaxModalCreateAction(Request $request, EventDispatcherInterface $eventDispatcher, ParserContext $parserContext, SecurityContext $securityContext)
    {
        $creditNote = $this->performCreditNote($eventDispatcher, $parserContext, $securityContext);

        return $this->render("ajax/credit-note-modal", [
            'creditNote' => $creditNote
        ]);
    }

    /**
     * @Route("/credit-note/pdf/invoice/{creditNoteId}/{browser}", name="_invoice_pdf", methods="GET")
     */
    public function generateInvoicePdfAction(
        $creditNoteId,
        $browser,
        SecurityContext $securityContext,
        TemplateHelperInterface $templateHelper,
        EventDispatcherInterface $eventDispatcher,
        Translator $translator
    )
    {
        return $this->generateCreditNotePdf(
            $creditNoteId,
            'credit-note',
            true,
            true,
            $browser,
            $securityContext,
            $templateHelper,
            $eventDispatcher,
            $translator
        );
    }

    /**
     * @param int $creditNoteId
     * @param string $fileName
     * @param bool $checkCreditNoteStatus
     * @param bool $checkAdminUser
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function generateCreditNotePdf(
        $creditNoteId,
        $fileName,
        $checkCreditNoteStatus = true,
        $checkAdminUser = true,
        $browser = false,
        $securityContext = null,
        $templateHelper = null,
        $eventDispatcher = null,
        $translator = null
    )
    {
        $creditNote = CreditNoteQuery::create()->findPk($creditNoteId);

        // check if the order has the paid status
        if ($checkAdminUser && !$securityContext->hasAdminUser()) {
            throw new NotFoundHttpException();
        }

        if ($checkCreditNoteStatus && !$creditNote->getCreditNoteStatus()->getInvoiced()) {
            throw new NotFoundHttpException();
        }

        $html = $this->renderRaw(
            $fileName,
            [
                'credit_note_id' => $creditNote->getId()
            ],
            $templateHelper->getActivePdfTemplate()
        );

        if ((int) $browser === 2) {
            return new Response($html);
        }

        try {
            $pdfEvent = new PdfEvent($html);

            $eventDispatcher->dispatch($pdfEvent, TheliaEvents::GENERATE_PDF);

            if ($pdfEvent->hasPdf()) {
                if ((int) $browser === 1) {
                    $browser = true;
                } else {
                    $browser = false;
                }
                return $this->pdfResponse($pdfEvent->getPdf(), $creditNote->getInvoiceRef(), 200, $browser);
            }
        } catch (\Exception $e) {
            Tlog::getInstance()->error(
                sprintf(
                    'error during generating invoice pdf for credit note id : %d with message "%s"',
                    $creditNote->getId(),
                    $e->getMessage()
                )
            );
        }

        throw new TheliaProcessException(
           $translator->trans(
                "We're sorry, this PDF invoice is not available at the moment."
            )
        );
    }

    /**
     * @return CreditNote
     */
    protected function performCreditNote(EventDispatcherInterface $eventDispatcher, ParserContext $parserContext, SecurityContext $securityContext, CreditNote $creditNote = null)
    {
        if (null === $creditNote) {
            $creditNote = new CreditNote();
        }

        $form = $this->createForm(CreditNoteCreateForm::getName(), FormType::class, [], ['csrf_protection' => false]);

        $formValidate = $this->validateForm($form, 'post');

        if (null === $creditNote->getInvoiceRef()) {
            $this
                ->performType($formValidate, $creditNote)
                ->performOrder($formValidate, $creditNote)
                ->performCurrency($formValidate, $creditNote)
                ->performCustomer($formValidate, $creditNote)
                ->performInvoiceAddress($formValidate, $creditNote)
                ->performStatus($formValidate, $creditNote)
                ->performFreeAmounts($formValidate, $creditNote)
                ->performOrderProducts($formValidate, $creditNote)
                ->performDiscount($formValidate, $creditNote)
                ->performAmount($formValidate, $creditNote)
            ;
        }

        $this->performComment($formValidate, $creditNote, $securityContext);

        $parserContext->addForm($form);

        return $creditNote;
    }

    protected function performDiscount(Form $formValidate, CreditNote $creditNote)
    {
        $discountWithoutTax = $formValidate->get('discount_without_tax')->getData();
        $discountWithTax = $formValidate->get('discount_with_tax')->getData();

        if (null !== $creditNote->getOrder() && $creditNote->getCreditNoteType()->getCode() === CreditNoteHelper::TYPE_ORDER_FULL_REFUND) {
            $creditNote->setDiscountWithoutTax($creditNote->getOrder()->getDiscount());
            $creditNote->setDiscountWithTax($creditNote->getOrder()->getDiscount());
        } elseif (null !== $discountWithoutTax && null !== $discountWithTax) {
            $creditNote->setDiscountWithoutTax($discountWithoutTax);
            $creditNote->setDiscountWithTax($discountWithTax);
        } elseif (null === $creditNote->getOrder()) {
            $creditNote->setDiscountWithoutTax(0);
            $creditNote->setDiscountWithTax(0);
        }

        return $this;
    }

    protected function performInvoiceAddress(Form $formValidate, CreditNote $creditNote)
    {
        if (!empty($creditNote->getInvoiceRef())) {
            return $this;
        }

        $action = $formValidate->get('action')->getData();

        $invoiceAddressId = $formValidate->get('invoice_address_id')->getData();

        if ($action !== 'view') {
            $creditNoteAddress = $creditNote->getCreditNoteAddress();

            if (null === $creditNoteAddress) {
                $creditNoteAddress = new CreditNoteAddress();
            }

            if (null === $creditNote->getCustomer()) {
                $creditNoteAddress = new CreditNoteAddress();
            } elseif ($invoiceAddressId) {
                $address = AddressQuery::create()->findOneById((int)$invoiceAddressId);

                $customerTitle = $address->getCustomerTitle();

                $creditNoteAddress
                    ->setCustomerTitleId($customerTitle ? $customerTitle->getId() : null)
                    ->setAddress1($address->getAddress1())
                    ->setAddress2($address->getAddress2())
                    ->setAddress3($address->getAddress3())
                    ->setFirstname($address->getFirstname())
                    ->setLastname($address->getLastname())
                    ->setCity($address->getCity())
                    ->setZipcode($address->getZipcode())
                    ->setCompany($address->getCompany())
                    ->setCountryId($address->getCountry()->getId());
            } else {
                $invoiceAddressTitle = $formValidate->get('invoice_address_title')->getData();
                $invoiceAddressFirstname = $formValidate->get('invoice_address_firstname')->getData();
                $invoiceAddressLastname = $formValidate->get('invoice_address_lastname')->getData();
                $invoiceAddressCompany = $formValidate->get('invoice_address_company')->getData();
                $invoiceAddressAddress1 = $formValidate->get('invoice_address_address1')->getData();
                $invoiceAddressAddress2 = $formValidate->get('invoice_address_address2')->getData();
                $invoiceAddressZipcode = $formValidate->get('invoice_address_zipcode')->getData();
                $invoiceAddressCity = $formValidate->get('invoice_address_city')->getData();
                $invoiceAddressCountryId = $formValidate->get('invoice_address_country_id')->getData();

                $country = CountryQuery::create()->findOneById($invoiceAddressCountryId);

                $creditNoteAddress
                    ->setCustomerTitleId($invoiceAddressTitle)
                    ->setAddress1($invoiceAddressAddress1)
                    ->setAddress2($invoiceAddressAddress2)
                    ->setFirstname($invoiceAddressFirstname)
                    ->setLastname($invoiceAddressLastname)
                    ->setCity($invoiceAddressCity)
                    ->setZipcode($invoiceAddressZipcode)
                    ->setCompany($invoiceAddressCompany)
                    ->setCountryId(
                        $country ? $country->getId() : null
                    );
            }

            if (empty($creditNoteAddress->getLastname()) && ('create' === $action || 'update' === $action)) {
                $formValidate->addError(
                    new FormError('Please select a invoice address')
                );

                $creditNoteAddress->save();
            }

            $creditNote->setCreditNoteAddress($creditNoteAddress);
        } elseif (null === $creditNote->getId()) {
            if (null !== $creditNote->getOrder() && null === $creditNote->getCreditNoteAddress()) {
                $address = $creditNote->getOrder()->getOrderAddressRelatedByInvoiceOrderAddressId();

                $customerTitle = $address->getCustomerTitle();

                $creditNoteAddress = new CreditNoteAddress();

                $creditNoteAddress
                    ->setCustomerTitleId($customerTitle ? $customerTitle->getId() : null)
                    ->setAddress1($address->getAddress1())
                    ->setAddress2($address->getAddress2())
                    ->setAddress3($address->getAddress3())
                    ->setFirstname($address->getFirstname())
                    ->setLastname($address->getLastname())
                    ->setCity($address->getCity())
                    ->setZipcode($address->getZipcode())
                    ->setCompany($address->getCompany())
                    ->setCountryId($address->getCountry()->getId());

                $creditNote->setCreditNoteAddress($creditNoteAddress);
            } else {
                $creditNote->setCreditNoteAddress(new CreditNoteAddress());
            }
        }

        return $this;
    }

    protected function performAmount(Form $formValidate, CreditNote $creditNote)
    {
        $totalPrice = 0;
        $totalPriceWithTax = 0;

        foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
            $totalPrice += $creditNoteDetail->getPrice() * $creditNoteDetail->getQuantity();
            $totalPriceWithTax += $creditNoteDetail->getPriceWithTax() * $creditNoteDetail->getQuantity();
        }

        $totalPrice -= $creditNote->getDiscountWithoutTax();
        $totalPriceWithTax -= $creditNote->getDiscountWithTax();

        $creditNote->setTotalPrice($totalPrice);
        $creditNote->setTotalPriceWithTax($totalPriceWithTax);

        return $this;
    }

    protected function performComment(Form $formValidate, CreditNote $creditNote, SecurityContext $securityContext)
    {
        /** @var string $orderId */
        $comment = trim($formValidate->get('comment')->getData());

        if (null !== $comment && !empty($comment)) {
            $creditNote->addCreditNoteComment(
                (new CreditNoteComment())
                    ->setComment($comment)
                    ->setAdminId($securityContext->getAdminUser()->getId())
            );
        }

        return $this;
    }

    protected function performOrder(Form $formValidate, CreditNote $creditNote)
    {
        /** @var int $orderId */
        $orderId = $formValidate->get('order_id')->getData();

        if (null !== $orderId && !empty($orderId)) {
            $order = OrderQuery::create()->findPk($orderId);

            $creditNote
                ->setOrder($order)
                ->setCustomer($order->getCustomer())
                ->setCurrency($order->getCurrency());

            if ($order->getStatusId() == 1 || $order->getStatusId() == 5) {
                throw new \Exception('This order is not invoiced');
            }
        }

        return $this;
    }

    protected function performCurrency(Form $formValidate, CreditNote $creditNote)
    {
        /** @var int $currencyId */
        $currencyId = $formValidate->get('currency_id')->getData();

        if (null !== $creditNote->getOrder()) {
            $creditNote->setCurrency($creditNote->getOrder()->getCurrency());
        } elseif ($creditNote->getCurrency() === null) {
            if (!empty($currencyId) && $currency = CurrencyQuery::create()->findPk($currencyId)) {
                $creditNote->setCurrency($currency);
            } else {
                $creditNote->setCurrency(CurrencyQuery::create()->findOneByByDefault(true));
            }
        }

        return $this;
    }

    protected function performCustomer(Form $formValidate, CreditNote $creditNote)
    {
        /** @var int $customerId */
        $customerId = $formValidate->get('customer_id')->getData();

        // check if order
        if (!empty($orderId) && null !== $order = OrderQuery::create()->findPk($orderId)) {
            $creditNote
                ->setOrder($order)
                ->setCurrency($order->getCurrency())
                ->setCustomer($order->getCustomer());
        } elseif (!empty($customerId) && null !== $customer = CustomerQuery::create()->findPk($customerId)) {
            $creditNote->setCustomer($customer);
        }

        return $this;
    }

    protected function performType(Form $formValidate, CreditNote $creditNote)
    {
        /** @var int $typeId */
        $typeId = $formValidate->get('type_id')->getData();

        if (!empty($typeId) && null !== $type = CreditNoteTypeQuery::create()->findPk($typeId)) {
            $creditNote->setCreditNoteType($type);
        } elseif (null === $creditNote->getTypeId()) {
            $creditNote->setCreditNoteType(CreditNoteTypeQuery::create()->findOne());
        }

        return $this;
    }

    protected function performStatus(Form $formValidate, CreditNote $creditNote)
    {
        /** @var int $statusId */
        $statusId = $formValidate->get('status_id')->getData();

        if (!empty($statusId) && null !== $status = CreditNoteStatusQuery::create()->findPk($statusId)) {
            $creditNote->setCreditNoteStatus($status);
        } elseif (null === $creditNote->getStatusId()) {
            $creditNote->setCreditNoteStatus(CreditNoteStatusQuery::create()->findOne());
        }

        return $this;
    }

    protected function performFreeAmounts(Form $formValidate, CreditNote $creditNote)
    {
        /** @var string[] $freeAmountTitles */
        $freeAmountTitles = $formValidate->get('free_amount_title')->getData();

        /** @var float[] $freeAmountPrices */
        $freeAmountPrices = $formValidate->get('free_amount_price')->getData();

        /** @var float[] $freeAmountPricesWithTax */
        $freeAmountPricesWithTax = $formValidate->get('free_amount_price_with_tax')->getData();

        /** @var int[] $freeAmountTaxRuleIds */
        $freeAmountTaxRuleIds = $formValidate->get('free_amount_tax_rule_id')->getData();

        /** @var string[] $freeAmountTaxRuleIds */
        $freeAmountIds = $formValidate->get('free_amount_id')->getData();

        /** @var string[] $freeAmountTypes */
        $freeAmountTypes = $formValidate->get('free_amount_type')->getData();

        /** @var string $freeAmountTypes */
        $action = $formValidate->get('action')->getData();

        foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
            if (empty($creditNoteDetail->getOrderProductId())) {
                foreach ($freeAmountTitles as $key => $freeAmountTitle) {
                    if ($freeAmountIds[$key] == $creditNoteDetail->getId()) {
                        $creditNoteDetail
                            ->setTitle($freeAmountTitle)
                            ->setPrice($freeAmountPrices[$key])
                            ->setTaxRuleId($freeAmountTaxRuleIds[$key])
                            ->setType($freeAmountTypes[$key])
                            ->setPriceWithTax($freeAmountPricesWithTax[$key]);
                    }
                }
            }
        }

        /**
         * @var int $key
         * @var int $freeAmountTitle
         */
        foreach ($freeAmountTitles as $key => $freeAmountTitle) {
            if (empty($freeAmountIds[$key])) {
                $creditNote->addCreditNoteDetail(
                    (new CreditNoteDetail())
                        ->setTitle($freeAmountTitle)
                        ->setPrice($freeAmountPrices[$key])
                        ->setTaxRuleId($freeAmountTaxRuleIds[$key])
                        ->setType($freeAmountTypes[$key])
                        ->setQuantity(1)
                        ->setPriceWithTax($freeAmountPricesWithTax[$key])
                );
            }
        }

        if (null !== $creditNote->getOrder() && $creditNote->getCreditNoteType()->getCode() === CreditNoteHelper::TYPE_ORDER_FULL_REFUND) {
            if (!(float) $creditNote->getOrder()->getPostage()) {
                foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
                    if ($creditNoteDetail->getType() == 'shipping') {
                        $creditNote->removeCreditNoteDetail($creditNoteDetail);
                    }
                }
            } else {
                $findShipping = false;
                foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
                    if ($creditNoteDetail->getType() == 'shipping') {
                        $findShipping = true;
                        $creditNoteDetail
                            ->setPrice(
                                $creditNote->getOrder()->getPostage() - $creditNote->getOrder()->getPostageTax()
                            )
                            ->setPriceWithTax(
                                $creditNote->getOrder()->getPostage()
                            );
                    }
                }

                if (!$findShipping) {
                    $creditNote->addCreditNoteDetail(
                        (new CreditNoteDetail())
                            ->setQuantity(1)
                            ->setTitle('Frais de port')
                            ->setPrice($creditNote->getOrder()->getPostage() - $creditNote->getOrder()->getPostageTax())
                            ->setType('shipping')
                            ->setPriceWithTax($creditNote->getOrder()->getPostage())
                    );
                }
            }
        }

        if ('refresh' === $action || $action === 'update') {
            foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
                $find = false;
                if (empty($creditNoteDetail->getOrderProductId())) {
                    foreach ($freeAmountTitles as $key => $freeAmountTitle) {
                        if ($freeAmountIds[$key] == $creditNoteDetail->getId()) {
                            $find = true;
                        }
                    }

                    if (!$find) {
                        $creditNote->removeCreditNoteDetail($creditNoteDetail);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param Form $formValidate
     * @param CreditNote $creditNote
     * @return $this
     */
    protected function performOrderProducts(Form $formValidate, CreditNote $creditNote)
    {
        if (null === $creditNote->getOrder()) {
            return $this;
        }

        /** @var string $freeAmountTypes */
        $action = $formValidate->get('action')->getData();

        /** @var int[] $orderProductQuantities */
        $orderProductQuantities = $formValidate->get('order_product_quantity')->getData();

        foreach ($creditNote->getOrder()->getOrderProducts() as $orderProduct) {
            $creditNoteDetail = null;
            if (null !== $creditNote->getId()) {
                $creditNoteDetail = CreditNoteDetailQuery::create()
                    ->filterByCreditNoteId($creditNote->getId())
                    ->filterByOrderProductId($orderProduct->getId())
                    ->findOne();
            }

            if (null === $creditNoteDetail) {
                $creditNoteDetail = new CreditNoteDetail;
            }

            if ($creditNote->getCreditNoteType()->getCode() === CreditNoteHelper::TYPE_ORDER_FULL_REFUND) {
                $creditNoteDetail->setQuantity($orderProduct->getQuantity());
            } else {
                if (isset($orderProductQuantities[$orderProduct->getId()])) {
                    $creditNoteDetail->setQuantity($orderProductQuantities[$orderProduct->getId()]);
                }
            }

            if ((float) $creditNoteDetail->getQuantity() <= 0) {
                $creditNote->removeCreditNoteDetail($creditNoteDetail);
                continue;
            }

            if ((int) $orderProduct->getWasInPromo()) {
                $orderProductWithoutTax = ((float) $orderProduct->getPromoPrice());
                $orderProductWithTax = (float) $orderProduct->getPromoPrice();
            } else {
                $orderProductWithoutTax = ((float) $orderProduct->getPrice());
                $orderProductWithTax = (float) $orderProduct->getPrice();
            }

            $orderProductTaxes = $orderProduct->getOrderProductTaxes();
            /** @var OrderProductTax $orderProductTax */
            foreach ($orderProductTaxes as $orderProductTax) {
                if ((int) $orderProduct->getWasInPromo()) {
                    $orderProductWithTax += (float) $orderProductTax->getPromoAmount();
                } else {
                    $orderProductWithTax += (float) $orderProductTax->getAmount();
                }
            }

            $creditNoteDetail
                ->setOrderProduct($orderProduct)
                ->setTitle($orderProduct->getTitle())
                ->setPrice($orderProductWithoutTax)
                ->setType('product')
                ->setPriceWithTax($orderProductWithTax);

            if (null !== $pse = ProductSaleElementsQuery::create()->findOneById($orderProduct->getProductSaleElementsId())) {
                if ($pse->getProduct() === null) {
                    $creditNoteDetail->setTaxRuleId(TaxRuleQuery::create()->findOneByIsDefault(true)->getId());
                } else {
                    $creditNoteDetail
                        ->setTaxRuleId(
                            $pse
                                ->getProduct()
                                ->getTaxRuleId()
                        )
                    ;
                }
            }

            $creditNote->addCreditNoteDetail(
                $creditNoteDetail
            );
        }

        if ($action === 'update') {
            foreach ($creditNote->getCreditNoteDetails() as $creditNoteDetail) {
                if ((float) $creditNoteDetail->getQuantity() === 0.0 && $creditNoteDetail->getType() === 'product') {
                    $creditNote->removeCreditNoteDetail($creditNoteDetail);
                }
            }
        }

        return $this;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Propel\Runtime\Exception\PropelException
     * @Route("/credit-note/ajax/search/customer", name="_search_customer", methods="GET")
     */
    public function searchCustomerAction(Request $request)
    {
        $customerQuery = CustomerQuery::create()
            ->innerJoinAddress()
            ->groupById()
            ->limit(20);

        $this->whereConcatRegex($customerQuery, [
            'customer.FIRSTNAME',
            'customer.LASTNAME',
            'customer.EMAIL',
            'address.COMPANY',
            'address.PHONE'
        ], $request->get('q'));

        $customerQuery
            ->withColumn(AddressTableMap::COMPANY, 'COMPANY')
            ->withColumn(AddressTableMap::ADDRESS1, 'ADDRESS')
            ->withColumn(AddressTableMap::CITY, 'CITY')
            ->withColumn(AddressTableMap::ZIPCODE, 'ZIPCODE')
            ->withColumn(AddressTableMap::PHONE, 'PHONE');

        $customers = $customerQuery->find();

        $json = [
            'incomplete_results' => count($customers) ? false : true,
            'items' => []
        ];

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $json['items'][] = [
                'id' => $customer->getId(),
                'company' => $customer->getVirtualColumn('COMPANY'),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'ref' => $customer->getRef(),
                'address' => $this->formatAddress($customer)
            ];
        }

        return new JsonResponse($json);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Propel\Runtime\Exception\PropelException
     * @Route("/credit-note/ajax/search/order", name="_search_order", methods="GET")
     */
    public function searchOrderAction(Request $request)
    {
        $orderQuery = OrderQuery::create();

        $orderQuery->useOrderStatusQuery()
                ->filterById([1,5], Criteria::NOT_IN)
            ->endUse();

        if (null !== $customerId = $request->get('customerId')) {
            if ((int) $customerId > 0) {
                $orderQuery->filterByCustomerId((int) $customerId);
            }
        }

        $orderQuery->innerJoinOrderAddressRelatedByInvoiceOrderAddressId()
        ->groupById()
        ->limit(20);

        $this->whereConcatRegex($orderQuery, [
            'order.REF',
            'order_address.LASTNAME',
            'order_address.FIRSTNAME',
            'order_address.COMPANY',
            'order_address.PHONE'
        ], $request->get('q'));

        $orderQuery
            ->withColumn(OrderAddressTableMap::FIRSTNAME, 'FIRSTNAME')
            ->withColumn(OrderAddressTableMap::LASTNAME, 'LASTNAME')
            ->withColumn(OrderAddressTableMap::COMPANY, 'COMPANY')
            ->withColumn(OrderAddressTableMap::ADDRESS1, 'ADDRESS')
            ->withColumn(OrderAddressTableMap::CITY, 'CITY')
            ->withColumn(OrderAddressTableMap::ZIPCODE, 'ZIPCODE')
            ->withColumn(OrderAddressTableMap::PHONE, 'PHONE');

        $orders = $orderQuery->find();

        $json = [
            'incomplete_results' => count($orders) ? false : true,
            'items' => []
        ];

        /** @var Order $order */
        foreach ($orders as $order) {
            $json['items'][] = [
                'id' => $order->getId(),
                'ref' => $order->getRef(),
                'company' => $order->getVirtualColumn('COMPANY'),
                'firstname' => $order->getVirtualColumn('FIRSTNAME'),
                'lastname' => $order->getVirtualColumn('LASTNAME'),
                'address' => $this->formatAddress($order),
            ];
        }

        return new JsonResponse($json);
    }

    /**
     * @Route("/module/credit-note/search", name="_search_credit_note", methods="POST")
     */
    public function searchCreditNoteAction(Request $request, ParserContext $parserContext, Translator $translator)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], [CreditNoteModule::DOMAIN_MESSAGE], AccessManager::VIEW)) {
            return $response;
        }
        $baseForm = $this->createForm(CreditNoteSearchForm::getName());
        $error_message = false;

        try {
            $form = $this->validateForm($baseForm);

            $request->request->set(CreditNoteModule::PARSED_DATA, $form->getData());

        } catch (FormValidationException $ex) {
            $error_message = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $error_message = $ex->getMessage();
        }

        if (false !== $error_message) {
            $this->setupFormErrorContext(
                $translator->trans("Searching credit notes"),
                $error_message,
                $baseForm,
                null
            );
        }
        $parserContext->addForm($baseForm);

        return $this->render(
            'credit-note-list',
            [
            ]
        );
    }

    /**
     * @param ActiveRecordInterface $model
     * @return mixed
     */
    protected function formatAddress(ActiveRecordInterface $model)
    {
        /** @var Order|Customer $model */
        return implode(' ', [$model->getVirtualColumn('ADDRESS'), $model->getVirtualColumn('ZIPCODE'), $model->getVirtualColumn('CITY')]);
    }
}
