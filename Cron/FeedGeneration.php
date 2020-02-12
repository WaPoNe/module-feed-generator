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

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \WaPoNe\FeedGenerator\Model\FeedProducts;
use \WaPoNe\FeedGenerator\Model\FeedFile;
use \Psr\Log\LoggerInterface;

/**
 * Class FeedGeneration
 * @package WaPoNe\FeedGenerator\Cron
 */
class FeedGeneration
{
    const EXTENSION_STATUS_PATH = 'feed_generator/general/status';
    const FEED_DIRECTORY_PATH = 'feed_generator/general/feed_directory';
    const FEED_TYPE_PATH = 'feed_generator/general/feed_type';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var FeedProducts
     */
    protected $_feedProducts;
    /**
     * @var FeedFile
     */
    protected $_feedFile;
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * FeedGeneration constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param FeedProducts $feedProducts
     * @param FeedFile $feedFile
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        FeedProducts $feedProducts,
        FeedFile $feedFile,
        LoggerInterface $logger
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_feedProducts = $feedProducts;
        $this->_feedFile = $feedFile;
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

        /* get feed products */
        $_feedProducts = $this->_feedProducts->getFeedProducts();

        // feed directory
        $feedDirectory = $this->_scopeConfig->getValue(self::FEED_DIRECTORY_PATH);
        if (!$feedDirectory) {
            $this->_logger->warning("Feed Directory not configured");
            return;
        }
        // feed type
        $feedType = $this->_scopeConfig->getValue(self::FEED_TYPE_PATH);
        if (!$feedType) {
            $this->_logger->warning("Feed Type not configured");
            return;
        }

        /* write feed file */
        $ret = $this->_feedFile->writeFeedFile($feedDirectory, $feedType, $_feedProducts);
        if (!$ret["success"]) {
            $this->_logger->error($ret["message"]);
            return;
        }

        $this->_logger->debug("Feed Generation :: End");
    }
}
