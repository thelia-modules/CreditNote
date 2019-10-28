<?php
/*************************************************************************************/
/*      This file is part of the module CreditNote                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace CreditNote\Smarty\Plugins;

use CommerceGuys\Addressing\Model\Address;
use CreditNote\Model\CreditNoteAddressQuery;
use CreditNote\Model\CreditNoteQuery;
use Symfony\Component\DependencyInjection\Container;
use Thelia\Tools\AddressFormat;
use Symfony\Component\HttpFoundation\RequestStack;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\Exception\SmartyPluginException;
use TheliaSmarty\Template\SmartyPluginDescriptor;

/**
 * @author Gilles Bourgeat >gilles.bourgeat@gmail.com>
 */
class CreditNoteFormat extends AbstractSmartyPlugin
{
    /** @var RequestStack */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     *
     * display an address in expected format
     *
     * available parameters :
     *  address => the id of the address to display
     *  order_address => the id of the order address to display
     *  from_country_id => the country id
     *  dec_point => separator for the decimal point
     *  thousands_sep => thousands separator
     *  symbol => Currency symbol
     *
     *  ex : {format_money number="1246.12" decimals="1" dec_point="," thousands_sep=" " symbol="€"} will output "1 246,1 €"
     *
     * @param $params
     * @param  null                                                   $template
     * @throws \TheliaSmarty\Template\Exception\SmartyPluginException
     * @return string                                                 the expected number formatted
     */
    public function formatAddress($params, $template = null)
    {
        $postal = filter_var(
            $this->getParam($params, "postal", null),
            FILTER_VALIDATE_BOOLEAN
        );

        $html = filter_var(
            $this->getParam($params, "html", true),
            FILTER_VALIDATE_BOOLEAN
        );

        $htmlTag = $this->getParam($params, "html_tag", "p");
        $originCountry = $this->getParam($params, "origin_country", null);
        $locale = $this->getParam($params, "locale", $this->getSession()->getLang()->getLocale());

        // extract html attributes
        $htmlAttributes = [];
        foreach ($params as $k => $v) {
            if (strpos($k, 'html_') !== false && $k !== 'html_tag') {
                $htmlAttributes[substr($k, 5)] = $v;
            }
        }

        // get address or order address
        $address = null;
        if (null !== $id = $this->getParam($params, "credit_note_id", null)) {
            if (null === $address = CreditNoteAddressQuery::create()->findOneById(
                    CreditNoteQuery::create()->findOneById($id)->getInvoiceAddressId()
                )
            ) {
                return '';
            }
        } else {
            // try to parse arguments to build address
            $address = $this->getAddressFormParams($params);
        }

        if (null === $address) {
            throw new SmartyPluginException(
                "Either address, order_address or full list of address fields should be provided"
            );
        }

        $addressFormat = AddressFormat::getInstance();
        if ($postal) {
            if ($address instanceof Address) {
                $formattedAddress = $addressFormat->postalLabelFormat($address, $locale, $originCountry);
            } else {
                $formattedAddress = $addressFormat->postalLabelFormatTheliaAddress($address, $locale, $originCountry);
            }
        } else {
            if ($address instanceof Address) {
                $formattedAddress = $addressFormat->format($address, $locale, $html, $htmlTag, $htmlAttributes);
            } else {
                $formattedAddress = $addressFormat->formatTheliaAddress($address, $locale, $html, $htmlTag, $htmlAttributes);
            }
        }

        return $formattedAddress;
    }

    protected function getAddressFormParams($params)
    {
        // Check if there is arguments
        $addressArgs = [
            'country_code',
            'administrative_area',
            'locality',
            'dependent_locality',
            'postal_code',
            'sorting_code',
            'address_line1',
            'address_line2',
            'organization',
            'recipient',
            'locale'
        ];
        $valid = false;

        $address = new Address();

        foreach ($addressArgs as $arg) {
            if (null !== $argVal = $this->getParam($params, $arg, null)) {
                $valid = true;
                $functionName = 'with' . Container::camelize($arg);
                $address = $address->$functionName($argVal);
            }
        }

        if (false === $valid) {
            return null;
        }

        return $address;
    }

    /**
     * @return SmartyPluginDescriptor[]
     */
    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor("function", "credit_note_format_address", $this, "formatAddress"),
        );
    }

    protected function getSession()
    {
        return $this->requestStack->getCurrentRequest()->getSession();
    }
}
