<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Helper;

use Propel\Runtime\ActiveQuery\ModelCriteria;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
trait CriteriaSearchHelper
{
    /**
     * @param string $q
     * @return string
     */
    public function getRegex($q)
    {
        $q = explode(' ', $q);

        $words = array();

        foreach ($q as $v) {
            $v = trim($v);
            if (strlen($v) > 2 && preg_match('/^[a-z0-9]+$/i', $v)) {
                $words[] = $v;
            }
        }

        if (!count($words)) {
            return null;
        }

        $regex = array();
        $regex[] = '.*' . implode('.+', $words) . '.*';
        if (count($words) > 1) {
            $regex[] = '.*' . implode('.+', array_reverse($words)) . '.*';
        }

        return implode('|', $regex);
    }

    /**
     * @param ModelCriteria $query
     * @param array $columns
     * @param string $q
     */
    public function whereConcatRegex(ModelCriteria $query, array $columns, $q)
    {
        $query->where("CONCAT_WS(' ', " . implode(',', $columns). ") REGEXP ?", self::getRegex($q), \PDO::PARAM_STR);
    }
}
