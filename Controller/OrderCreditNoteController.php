<?php

namespace CreditNote\Controller;

use CreditNote\Controller\Traits\FormErrorsInFlashbag;
use CreditNote\CreditNote;
use CreditNote\Event\OrderCreditNoteEvent;
use CreditNote\Event\OrderCreditNoteEvents;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;

/**
 * Order credit note back-office controller.
 */
class OrderCreditNoteController extends BaseAdminController
{
    use FormErrorsInFlashbag;

    /**
     * Attempt to create an order credit note.
     * Adds error or success message and redirects to the order edit page, on the module tab.
     * @return mixed|Response
     */
    public function createAction()
    {
        $authResponse = $this->checkAuth(AdminResources::MODULE, CreditNote::getModuleCode(), AccessManager::CREATE);
        if (null !== $authResponse) {
            return $authResponse;
        }

        $baseForm = $this->createForm("order_credit_note.create");

        try {
            $validatedForm = $this->validateForm($baseForm, "POST");
            $formData = $validatedForm->getData();

            $orderCreditNoteCreationEvent = new OrderCreditNoteEvent();

            $orderCreditNoteCreationEvent->setOrderId($formData["order_id"]);
            $orderCreditNoteCreationEvent->setAmount($formData["amount"]);
            $orderCreditNoteCreationEvent->setMessage($formData["message"]);

            $this->dispatch(OrderCreditNoteEvents::CREATE, $orderCreditNoteCreationEvent);

            if (null !== $orderCreditNoteCreationEvent->getOrderCreditNote()) {
                $this->getSession()->getFlashBag()->set(
                    $baseForm->getName() . ".success_message",
                    $this->getTranslator()->trans(
                        "Credit note created.",
                        [],
                        CreditNote::MESSAGE_DOMAIN_BO
                    )
                );
            }
        } catch (FormValidationException $e) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        if (isset($errorMessage)) {
            $this->getSession()->getFlashBag()->set($baseForm->getName() . ".error_message", $errorMessage);
            $this->setFormErrorsInFlashbag($baseForm->getForm(), "");
        }

        return $this->generateRedirectFromRoute(
            "admin.order.update.view",
            [
                "tab" => "modules",
            ],
            [
                "order_id" => $baseForm->getForm()->get("order_id")->getData(),
            ]
        );
    }
}
