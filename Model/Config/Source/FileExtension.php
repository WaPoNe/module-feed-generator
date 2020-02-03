<?php
/**
 * WaPoNe
 *
 * @category   WaPoNe
 * @package    WaPoNe_FeedGenerator
 * @copyright  Copyright (c) 2020 WaPoNe (https://www.fantetti.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace WaPoNe\FeedGenerator\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class FileExtension
 * @package WaPoNe\FeedGenerator\Model\Config\Source
 */
class FileExtension implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array("value" => 'csv', "label" => "CSV"),
            array("value" => 'xml', "label" => "XML")
        );

        return $options;
    }
}
