<?php
namespace fhu\Paginator;

use fhu\Paginator\Exception\NumberOfEntriesNotSpecifiedException;
use fhu\Paginator\Exception\ParameterNotSpecifiedInUrlException;

class Paginator
{
    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @var int
     */
    protected $maxEntriesPerPage = 100;

    /**
     * @var int
     */
    protected $entriesPerPage = 20;

    /**
     * @var int
     */
    protected $maxNumberOfPaginationButtons = 10;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var array
     */
    protected $parts = [];

    /**
     * @var string
     */
    protected $pageVar = 'page';

    /**
     * @var string
     */
    protected $noeppVar = 'noepp';

    /**
     * @var array
     */
    protected $translation = array(
        'Total page' => '%d page',
        'Total entry' => '%d record',
        'Total pages' => '%d pages',
        'Total entries' => '%d records',
        'First' => '&lt;&lt;',
        'Last' => '&gt;&gt;',
        'Next' => 'Next',
        'Previous' => 'Previous',
        'and' => 'and',
    );

    public function __construct()
    {
        $this->buildPartsFromRequest();
    }

    /**
     * Returns pagination HTML code.
     *
     * @param boolean $boolShowSummary
     * @return string HTML code
     * @throws NumberOfEntriesNotSpecifiedException
     */
    public function render($boolShowSummary = false)
    {
        $numberOfEntries        = $this->getCount();
        $currentPage            = $this->getPageNumber();
        $numberOfEntriesPerPage = $this->getNumberOfEntriesPerPage();
        $translation            = $this->getTranslation();
        $html                   = '';

        $html .= '
<div class="pagination">
    <ul>';

        if ($numberOfEntries > 0) {
            $page = ($currentPage - 1 < 0) ? 0 : $currentPage - 1;

            $class = ' class="control"';
            if ($page == $currentPage) {
                $class = ' class="disabled"';
            }

            $html .= '
        <li' . $class . '>
            <a href="' . $this->getUrl(0) . '">' . $translation['First'] . '</a>
        </li>
        <li' . $class . '>
            <a href="' . $this->getUrl($page) . '">' . $translation['Previous'] . '</a>
        </li>';
        }

        $numberOfPages = intval($numberOfEntries / $numberOfEntriesPerPage);
        if ($numberOfEntries % $numberOfEntriesPerPage != 0)
            $numberOfPages++;

        if ($currentPage + 1 > $numberOfPages)
            $currentPage = $numberOfPages - 1;

        $maxNumberOfPaginationButtons = $this->getMaxNumberOfPaginationButtons();
        $buttonStartIndex = $currentPage - (intval($maxNumberOfPaginationButtons / 2)) + 1;

        if ($currentPage > $numberOfPages - intval($maxNumberOfPaginationButtons / 2))
            $buttonStartIndex -= intval($maxNumberOfPaginationButtons / 2) - ($numberOfPages - $currentPage);

        if ($buttonStartIndex < 1)
            $buttonStartIndex = 1;

        $buttonEndIndex = $currentPage + (intval($maxNumberOfPaginationButtons / 2)) + 1;

        if ($currentPage < intval($maxNumberOfPaginationButtons / 2))
            $buttonEndIndex += intval($maxNumberOfPaginationButtons / 2) - $currentPage;

        if ($buttonEndIndex > $numberOfPages)
            $buttonEndIndex = $numberOfPages;

        for ($pageNumber = $buttonStartIndex; $pageNumber <= $buttonEndIndex; $pageNumber++) {
            $strActive = '';

            if (($pageNumber - 1) == $currentPage)
                $strActive = ' class="active"';

            $html .= '
        <li' . $strActive . '>
            <a href="' . $this->getUrl($pageNumber - 1) . '">' . $pageNumber . '</a>
        </li>
           ';
        }

        if ($numberOfEntries > 0) {

            $page = (($currentPage + 1 < $numberOfPages - 1) ? $currentPage + 1 : $currentPage);

            $class = ' class="control"';
            if ($page == $numberOfPages - 1) {
                $class = ' class="disabled"';
            }

            $html .= '
        <li' . $class . '>
            <a href="' . $this->getUrl($page + 1) . '">' . $translation['Next'] . '</a>
        </li>
        <li' . $class . '>
            <a href="' . $this->getUrl($numberOfPages - 1) . '">' . $translation['Last'] . '</a>
        </li>';
        }
        $html .= '
    </ul>';

        if ($boolShowSummary) {
            $html .= '
             <br />
             <small class="paginator_summary">
                ' . sprintf($translation[(($numberOfPages == 1) ? 'Total page' : 'Total pages')], $numberOfPages) . '
                ' . $translation['and'] . '
                ' . sprintf($translation[(($numberOfEntries == 1) ? 'Total entry' : 'Total entries')], $numberOfEntries) . '
             </small>
          ';
        }

        $html .= '
</div>';

        return $html;
    }

    /**
     * Returns the number of entries.
     *
     * @return int
     */
    public function getCount() {
        return $this->count;
    }

    /**
     * Stores the number of entries. This value will not be used by the class and must be used only as a helper.
     *
     * @param int $count Number of entries
     * @return void
     */
    public function setCount($count) {
        $this->count = $count;
    }

    /**
     * Returns the current page number (that is being shown in the browser).
     *
     * @return int
     */
    public function getPageNumber() {
        // Page number
        if(isset($_GET[$this->pageVar]))
            $currentPage = $_GET[$this->pageVar];
        else
            $currentPage = 0;

        // Error validation
        if(!is_numeric($currentPage) || $currentPage < 0)
            $currentPage = 0;

        return $currentPage;
    }

    /**
     * Returns the number of entries per page. The value is read from the querystring.
     *
     * @return int
     */
    public function getNumberOfEntriesPerPage() {
        $maxEntriesPerPage = $this->getMaxEntriesPerPage();

        // Number of entries per page
        if(isset($_GET[$this->noeppVar]))
            $numberOfEntriesPerPage = $_GET[$this->noeppVar];
        else
            $numberOfEntriesPerPage = $this->getEntriesPerPage();

        // Error validation
        if(!is_numeric($numberOfEntriesPerPage) || $numberOfEntriesPerPage > $maxEntriesPerPage)
            $numberOfEntriesPerPage = $maxEntriesPerPage;

        return $numberOfEntriesPerPage;
    }

    /**
     * @param int $maxEntriesPerPage
     */
    public function setMaxEntriesPerPage($maxEntriesPerPage)
    {
        $this->maxEntriesPerPage = $maxEntriesPerPage;
    }

    /**
     * @return int
     */
    public function getMaxEntriesPerPage()
    {
        return $this->maxEntriesPerPage;
    }

    /**
     * @param int $entriesPerPage
     */
    public function setEntriesPerPage($entriesPerPage)
    {
        $this->entriesPerPage = $entriesPerPage;
    }

    /**
     * @return int
     */
    public function getEntriesPerPage()
    {
        return $this->entriesPerPage;
    }

    /**
     * Returns the maximum number of pagination buttons a single page can have.
     *
     * @return int
     */
    public function getMaxNumberOfPaginationButtons() {
        return $this->maxNumberOfPaginationButtons;
    }

    /**
     * @param int $maxPaginationButtons
     */
    public function setMaxNumberOfPaginationButtons($maxPaginationButtons)
    {
        $this->maxNumberOfPaginationButtons = $maxPaginationButtons;
    }

    /**
     * Returns the offset and row count that can be used with SQL LIMIT clause.
     *
     * @return array
     */
    public function getQueryLimit() {
        $intCurrentPage = $this->getPageNumber();
        $intNumberOfEntriesPerPage = $this->getNumberOfEntriesPerPage();
        $intCurrentOffset = $intCurrentPage * $intNumberOfEntriesPerPage;

        return array('offset' => $intCurrentOffset, 'number' => $intNumberOfEntriesPerPage);
    }

    /**
     * Creates URL schema from request url.
     */
    protected function buildPartsFromRequest()
    {
        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);
        $parts = [];

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parts);
        }

        $this->parts = $parts;
    }

    /**
     * Sets an URL schema. You can use parameters that will be replaced later:
     *
     * %PAGE%: page number
     * %NOEPP%: number of entries per page
     *
     * @param string $url
     * @throws ParameterNotSpecifiedInUrlException
     */
    public function setUrl($url)
    {
        if (strpos($url, '%PAGE%') === false) {
            throw new ParameterNotSpecifiedInUrlException('Parameter %PAGE% not specified in Paginator url.');
        }

        if (strpos($url, '%NOEPP%') === false) {
            throw new ParameterNotSpecifiedInUrlException('Parameter %NOEPP% not specified in Paginator url.');
        }

        $this->url = $url;
    }

    /**
     * Returns the URL with the page number.
     *
     * @param $page
     * @return string
     */
    public function getUrl($page)
    {
        $numberOfEntriesPerPage = $this->getNumberOfEntriesPerPage();

        if ($this->url) {
            $url = $this->url;
            $url = str_replace('%PAGE%', $page, $url);
            $url = str_replace('%NOEPP%', $numberOfEntriesPerPage, $url);

            return $url;
        } else {
            $this->parts[$this->pageVar] = $page;
            $this->parts[$this->noeppVar] = $numberOfEntriesPerPage;
            return '?' . http_build_query($this->parts);
        }
    }

    /**
     * @param array $translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
    }

    /**
     * @return array
     */
    public function getTranslation()
    {
        return $this->translation;
    }
}