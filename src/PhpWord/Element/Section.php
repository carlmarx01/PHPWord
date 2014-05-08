<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2014 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord\Element;

use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Section as SectionSettings;

/**
 * Section
 */
class Section extends AbstractContainer
{
    /**
     * Section settings
     *
     * @var \PhpOffice\PhpWord\Style\Section
     */
    private $settings;

    /**
     * Section headers, indexed from 1, not zero
     *
     * @var Header[]
     */
    private $headers = array();

    /**
     * Section footers, indexed from 1, not zero
     *
     * @var Footer[]
     */
    private $footers = array();

    /**
     * Create new instance
     *
     * @param int $sectionCount
     * @param array $settings
     */
    public function __construct($sectionCount, $settings = null)
    {
        $this->container = 'section';
        $this->sectionId = $sectionCount;
        $this->setDocPart($this->container, $this->sectionId);
        $this->settings = new SectionSettings();
        $this->setSettings($settings);
    }

    /**
     * Set section settings
     *
     * @param array $settings
     */
    public function setSettings($settings = null)
    {
        if (!is_null($settings) && is_array($settings)) {
            foreach ($settings as $key => $value) {
                if (is_null($value)) {
                    continue;
                }
                $this->settings->setSettingValue($key, $value);
            }
        }
    }

    /**
     * Get Section Settings
     *
     * @return \PhpOffice\PhpWord\Style\Section
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Add a Title Element
     *
     * @param string $text
     * @param int $depth
     * @return \PhpOffice\PhpWord\Element\Title
     */
    public function addTitle($text, $depth = 1)
    {
        $title = new Title($text, $depth);
        $title->setDocPart($this->getDocPart(), $this->getDocPartId());
        if ($this->phpWord instanceof PhpWord) {
            $bookmarkId = $this->phpWord->addTitle($title);
            $title->setBookmarkId($bookmarkId);
        }
        $this->addElement($title);

        return $title;
    }

    /**
     * Add a PageBreak Element
     */
    public function addPageBreak()
    {
        $this->addElement(new PageBreak());
    }

    /**
     * Add table element
     *
     * @param mixed $style
     * @return \PhpOffice\PhpWord\Element\Table
     * @todo Merge with the same function on Footer
     */
    public function addTable($style = null)
    {
        $table = new Table($this->getDocPart(), $this->getDocPartId(), $style);
        $this->addElement($table);

        return $table;
    }

    /**
     * Add a Table-of-Contents Element
     *
     * @param mixed $fontStyle
     * @param mixed $tocStyle
     * @param integer $minDepth
     * @param integer $maxDepth
     * @return \PhpOffice\PhpWord\Element\TOC
     */
    public function addTOC($fontStyle = null, $tocStyle = null, $minDepth = 1, $maxDepth = 9)
    {
        $toc = new TOC($fontStyle, $tocStyle, $minDepth, $maxDepth);
        $this->addElement($toc);

        return $toc;
    }

    /**
     * Add header
     *
     * @param string $type
     * @return Header
     * @since 0.10.0
     */
    public function addHeader($type = Header::AUTO)
    {
        return $this->addHeaderFooter($type, true);
    }

    /**
     * Add footer
     *
     * @param string $type
     * @return Footer
     * @since 0.10.0
     */
    public function addFooter($type = Header::AUTO)
    {
        return $this->addHeaderFooter($type, false);
    }

    /**
     * Get header elements
     *
     * @return Header[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get footer elements
     *
     * @return Footer[]
     */
    public function getFooters()
    {
        return $this->footers;
    }

    /**
     * Is there a header for this section that is for the first page only?
     *
     * If any of the Header instances have a type of Header::FIRST then this method returns true.
     * False otherwise.
     *
     * @return boolean
     */
    public function hasDifferentFirstPage()
    {
        foreach ($this->headers as $header) {
            if ($header->getType() == Header::FIRST) {
                return true;
            }
        }
        return false;
    }

    /**
     * Add header/footer
     *
     * @param string $type
     * @param boolean $header
     * @return Header|Footer
     * @throws \PhpOffice\PhpWord\Exception\Exception
     * @since 0.10.0
     */
    private function addHeaderFooter($type = Header::AUTO, $header = true)
    {
        $collectionArray = $header ? 'headers' : 'footers';
        $containerClass = __NAMESPACE__ . '\\' . ($header ? 'Header' : 'Footer');
        $collection = &$this->$collectionArray;

        if (in_array($type, array(Header::AUTO, Header::FIRST, Header::EVEN))) {
            $index = count($collection);
            $container = new $containerClass($this->sectionId, ++$index, $type);
            $container->setPhpWord($this->phpWord);

            $collection[$index] = $container;
            return $container;
        } else {
            throw new Exception('Invalid header/footer type.');
        }

    }

    /**
     * Create header
     *
     * @return Header
     * @deprecated 0.10.0
     * @codeCoverageIgnore
     */
    public function createHeader()
    {
        return $this->addHeader();
    }

    /**
     * Create footer
     *
     * @return Footer
     * @deprecated 0.10.0
     * @codeCoverageIgnore
     */
    public function createFooter()
    {
        return $this->addFooter();
    }

    /**
     * Get footer
     *
     * @return Footer
     * @deprecated 0.10.0
     * @codeCoverageIgnore
     */
    public function getFooter()
    {
        if (empty($this->footers)) {
            return null;
        } else {
            return $this->footers[1];
        }
    }
}
