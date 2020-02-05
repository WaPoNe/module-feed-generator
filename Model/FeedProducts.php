<?php
/**
 * WaPoNe
 *
 * @category   WaPoNe
 * @package    WaPoNe_FeedGenerator
 * @copyright  Copyright (c) 2020 WaPoNe (https://www.fantetti.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace WaPoNe\FeedGenerator\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

/**
 * Class FeedProducts
 * @package WaPoNe\FeedGenerator\Model
 */
class FeedProducts
{
    const MIN_PRICE_FILER_PATH = 0;
    const PRICE_FILER_PATH = 'feed_generator/filters/price';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * FeedProducts constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $productCollectionFactory
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Get feed products to put into the feed file
     *
     * @return mixed
     */
    public function getFeedProducts()
    {
        $priceFilter = ($this->_scopeConfig->getValue(self::PRICE_FILER_PATH))
            ? (int)$this->_scopeConfig->getValue(self::PRICE_FILER_PATH)
            : self::MIN_PRICE_FILER_PATH;

        $collection = $this->_productCollectionFactory->create();

        $collection
            ->addFieldToSelect('name', 'SKU')
            ->addFieldToFilter('price', array('gt' => $priceFilter))
            ->setOrder('created_at')
            ->setPageSize(5);

        return $collection;
    }
}