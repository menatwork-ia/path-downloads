<?php

namespace PathDownload;

class ContentDownload extends \ContentElement
{

    /**
     * File object
     * @var File
     */
    protected $objFile;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_download';


    /**
     * Return if the file does not exist
     * @return string
     */
    public function generate()
    {
        // Return if there is no file
        if (!strlen($this->singlePathSRC) || !is_file(TL_ROOT . '/' . $this->singlePathSRC))
        {
            return '';
        }

        $objFile         = new \File($this->singlePathSRC, true);
        $allowedDownload = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['allowedDownload']));

        // Return if the file type is not allowed
        if (!in_array($objFile->extension, $allowedDownload))
        {
            return '';
        }

        $this->objFile = $objFile;

        // Send the file to the browser
        if (strlen(\Input::get('file', true)) && \Input::get('file', true) == $this->singlePathSRC)
        {
            \Controller::sendFileToBrowser($this->singlePathSRC);
        }

        return parent::generate();
    }


    /**
     * Generate the content element
     */
    protected function compile()
    {
        if (!strlen($this->linkTitle))
        {
            $this->linkTitle = $this->objFile->basename;
        }

        $strHref = \Environment::get('request');

        // Remove an existing file parameter (see #5683)
        if (preg_match('/(&(amp;)?|\?)file=/', $strHref))
        {
            $strHref = preg_replace('/(&(amp;)?|\?)file=[^&]+/', '', $strHref);
        }

        $strHref .= (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos($strHref, '?') !== false) ? '&amp;' : '?') . 'file=' . $this->urlEncode($this->singlePathSRC);

        $this->Template->link      = $this->linkTitle;
        $this->Template->title     = specialchars($this->linkTitle);
        $this->Template->href      = $strHref;
        $this->Template->filesize  = $this->getReadableSize($this->objFile->filesize, 1);
        $this->Template->icon      = TL_ASSETS_URL . 'assets/contao/images/' . $this->objFile->icon;
        $this->Template->mime      = $this->objFile->mime;
        $this->Template->extension = $this->objFile->extension;
        $this->Template->path      = $this->objFile->dirname;
    }
}