<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Helper;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CreditNoteHelper
{
    const STATUS_PROPOSED = 'proposed';
    const STATUS_REFUSED = 'refused';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_USED = 'used';

    const TYPE_ORDER_FULL_REFUND = 'order_full_refund';
    const TYPE_BACK_PRODUCT = 'back_product';
    const TYPE_BILLING_ERROR = 'billing_error';
    const TYPE_REBATE = 'rebate';
    const TYPE_DISCOUNT = 'discount';
}
