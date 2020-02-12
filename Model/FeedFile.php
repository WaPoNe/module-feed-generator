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

use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem\Io\File;
use \Magento\Framework\Exception\FileSystemException;

/**
 * Class FeedFile
 * @package WaPoNe\FeedGenerator\Model
 */
class FeedFile
{
    const FEED_FILE_NAME = 'feed';
    const FEED_FILE_TYPE_CSV = 'csv';
    const FEED_FILE_TYPE_XML = 'xml';
    const FIELD_SEPARATOR = '|';

    protected $_finalFeedFile;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $_io;

    /**
     * FeedFile constructor.
     *
     * @param DirectoryList $directoryList
     * @param File $io
     */
    public function __construct(
        DirectoryList $directoryList,
        File $io
    )
    {
        $this->_directoryList = $directoryList;
        $this->_io = $io;
    }

    /**
     * Write the feed file
     *
     * @param $feedDirectory
     * @param $feedType
     * @param $feedProducts
     * @return array
     */
    public function writeFeedFile($feedDirectory, $feedType, $feedProducts)
    {
        // directory check and creation
        try {
            // Feed Directory
            $feedDir = $this->_directoryList->getPath(DirectoryList::ROOT) . DIRECTORY_SEPARATOR . $feedDirectory . DIRECTORY_SEPARATOR;
            // Feed File
            $feedFilename = self::FEED_FILE_NAME . '.' . $feedType;
            // Feed Complete File
            $feedCompleteFilename = $feedDir . $feedFilename;

            $this->_io->checkAndCreateFolder($feedDir, 0775);
            // opening file in writable mode
            $this->_finalFeedFile = fopen($feedCompleteFilename, 'w');
            if ($this->_finalFeedFile === false) {
                return array("success" => false, "message" => "Opening file $feedCompleteFilename error!");
            }
        } catch (FileSystemException $exception) {
            return array("success" => false, "message" => "Directory $feedDir creation error: " . $exception->getMessage());
        } catch (\Exception $exception)  {
            return array("success" => false, "message" => "Directory check and creation error: " . $exception->getMessage());
        }

        switch ($feedType) {
            case self::FEED_FILE_TYPE_CSV:
                $ret = $this->_writeCSVFeedFile($feedProducts);
                break;
            case self::FEED_FILE_TYPE_XML:
                $ret = $this->_writeXMLFeedFile($feedProducts);
                break;
            default:
                $ret = $this->_writeCSVFeedFile($feedProducts);
        }

        // closing file
        fclose($this->_finalFeedFile);

        if (!$ret["success"]) {
            return $ret;
        }

        return array("success" => true);
    }

    /**
     * Writing feed file in CSV format
     *
     * @param $feedProducts
     * @return array
     */
    private function _writeCSVFeedFile($feedProducts)
    {
        try {
            foreach ($feedProducts as $feedProduct)
            {
                fwrite($this->_finalFeedFile, $feedProduct->getSku() . self::FIELD_SEPARATOR);
                fwrite($this->_finalFeedFile,$feedProduct->getName() . self::FIELD_SEPARATOR);
                fwrite($this->_finalFeedFile,$feedProduct->getPrice() . "\n");
            }
        } catch (\Exception $exception)  {
            return array("success" => false, "message" => "CSV Feed File creation error: " . $exception->getMessage());
        }

        return array("success" => true);
    }

    /**
     * Writing feed file in XML format
     *
     * @param $feedProducts
     * @return array
     */
    private function _writeXMLFeedFile($feedProducts)
    {
        try {
            fwrite($this->_finalFeedFile, "<Products>\n");

            foreach ($feedProducts as $feedProduct)
            {
                fwrite($this->_finalFeedFile, "<Product>\n");

                fwrite($this->_finalFeedFile,"<sku>" . $feedProduct->getSku() . "</sku>\n");
                fwrite($this->_finalFeedFile,"<name>" . $feedProduct->getName() . "</name>\n");
                fwrite($this->_finalFeedFile,"<price>" . $feedProduct->getPrice() . "</price>\n");

                fwrite($this->_finalFeedFile, "</Product>\n");
            }

            fwrite($this->_finalFeedFile, "</Products>");
        } catch (\Exception $exception)  {
            return array("success" => false, "message" => "XML Feed File creation error: " . $exception->getMessage());
        }

        return array("success" => true);
    }
}