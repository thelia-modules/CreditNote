<?php

namespace CreditNote\EventListener;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;

/**
 * @author Léa Normandon <lnormandon@openstudio.fr>
 */
class CreditNoteLoopListener implements EventSubscriberInterface
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    /**
     * @param LoopExtendsBuildModelCriteriaEvent $event
     */
    public function extendCreditNoteLoop(LoopExtendsBuildModelCriteriaEvent $event)
    {

        $data = $this->requestStack->getCurrentRequest()->request->get(CreditNote::PARSED_DATA);

        if ($data != null) {

            /** @var CreditNoteQuery $search */
            $search = $event->getModelCriteria();

            if ($data['ref'] != null) {
                $search->filterByRef("%".$data['ref']."%", Criteria::LIKE);
            }

            if ($data['creditNoteDateMin'] != null) {
                $search->filterByCreatedAt($data['creditNoteDateMin'], Criteria::GREATER_EQUAL);
            }

            if ($data['creditNoteDateMax'] != null) {
                $search->filterByCreatedAt($data['creditNoteDateMax'], Criteria::LESS_EQUAL);
            }
        }
    }


    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'credit-note') => ['extendCreditNoteLoop', 128],
        ];
    }
}
