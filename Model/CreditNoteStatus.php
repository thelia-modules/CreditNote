<?php

namespace CreditNote\Model;

use CreditNote\Model\Base\CreditNoteStatus as BaseCreditNoteStatus;

class CreditNoteStatus extends BaseCreditNoteStatus
{
    use \CreditNote\Model\Tools\ModelEventDispatcherTrait;
}
