<?php

namespace CreditNote\Model;

use CreditNote\Model\Base\CreditNoteComment as BaseCreditNoteComment;

class CreditNoteComment extends BaseCreditNoteComment
{
    use \CreditNote\Model\Tools\ModelEventDispatcherTrait;
}
