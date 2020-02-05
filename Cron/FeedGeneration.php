<?php
/**
 * WaPoNe
 *
 * @category   WaPoNe
 * @package    WaPoNe_FeedGenerator
 * @copyright  Copyright (c) 2020 WaPoNe (https://www.fantetti.net)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace WaPoNe\FeedGenerator\Cron;

use \WaPoNe\FeedGenerator\Model\FeedProducts;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Psr\Log\LoggerInterface;

/**
 * Class FeedGeneration
 * @package WaPoNe\FeedGenerator\Cron
 */
class FeedGeneration
{
    const EXTENSION_STATUS_PATH = 'feed_generator/general/status';
    const FEED_TYPE_PATH = 'feed_generator/general/feed_type';

    /**
     * @var FeedProducts
     */
    protected $_feedProducts;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * FeedGeneration constructor.
     *
     * @param FeedProducts $feedProducts
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        FeedProducts $feedProducts,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    )
    {
        $this->_feedProducts = $feedProducts;
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
    }

    /**
     * Generate feed file
     */
    public function generateFeed()
    {
        $moduleStatus = $this->_scopeConfig->getValue(self::EXTENSION_STATUS_PATH);
        if (!$moduleStatus) {
            $this->_logger->warning("Extension disabled");
            return;
        }

        $this->_logger->debug("Feed Generation :: Start");
        // get feed products
        $_feedProducts = $this->_feedProducts->getFeedProducts();
        foreach ($_feedProducts as $_feedProduct)
        {
            $this->_logger->debug($_feedProduct->getSku() . ' - ' .$_feedProduct->getName() . ' -> ' .$_feedProduct->getPrice());
        }

        $feedType = $this->_scopeConfig->getValue(self::FEED_TYPE_PATH);
        $this->_logger->debug("Feed Type: $feedType");

        $this->_logger->debug("Feed Generation :: End");
    }
}