<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Event;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CreditNoteEvents
{
    const PROPEL_PRE_INSERT = "credit-note.pre.insert.";

    const PROPEL_POST_INSERT = "credit-note.post.insert.";

    const PROPEL_PRE_UPDATE = "credit-note.pre.update.";

    const PROPEL_POST_UPDATE = "credit-note.post.update.";

    const PROPEL_PRE_DELETE = "credit-note.pre.delete.";

    const PROPEL_POST_DELETE = "credit-note.post.delete.";

    public static function preInsert($tableName)
    {
        return self::PROPEL_PRE_INSERT . $tableName;
    }

    public static function postInsert($tableName)
    {
        return self::PROPEL_POST_INSERT . $tableName;
    }

    public static function preUpdate($tableName)
    {
        return self::PROPEL_PRE_UPDATE . $tableName;
    }

    public static function postUpdate($tableName)
    {
        return self::PROPEL_POST_UPDATE . $tableName;
    }

    public static function preDelete($tableName)
    {
        return self::PROPEL_PRE_DELETE . $tableName;
    }

    public static function postDelete($tableName)
    {
        return self::PROPEL_POST_DELETE . $tableName;
    }
}
