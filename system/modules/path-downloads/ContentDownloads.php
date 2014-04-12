<?php

namespace PathDownload;

class ContentDownloads extends \ContentElement
{

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'ce_downloads';


    /**
     * Return if there are no files
     * @return string
     */
    public function generate()
    {
        $this->multiPathSRC = deserialize($this->multiPathSRC);

        // Use the home directory of the current user as file source
        if ($this->useHomeDir && FE_USER_LOGGED_IN)
        {
            $this->import('FrontendUser', 'User');

            if ($this->User->assignDir && is_dir(TL_ROOT . '/' . $this->User->homeDir))
            {
                $this->multiPathSRC = array($this->User->homeDir);
            }
        }

        // Return if there are no files
        if (!is_array($this->multiPathSRC) || empty($this->multiPathSRC))
        {
            return '';
        }

        $file = \Input::get('file', true);

        // Send the file to the browser
        if ($file != '' && (in_array($file, $this->multiPathSRC) || in_array(dirname($file), $this->multiPathSRC)) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', basename($file)))
        {
            \Controller::sendFileToBrowser($file);
        }

        return parent::generate();
    }


    /**
     * Generate the content element
     */
    protected function compile()
    {
        $files   = array();
        $auxDate = array();

        $allowedDownload = trimsplit(',', strtolower($GLOBALS['TL_CONFIG']['allowedDownload']));

        // Get all files
        foreach ($this->multiPathSRC as $file)
        {
            if (isset($files[$file]) || !file_exists(TL_ROOT . '/' . $file))
            {
                continue;
            }

            // Single files
            if (is_file(TL_ROOT . '/' . $file))
            {
                $objFile = new \File($file, true);

                if (in_array($objFile->extension, $allowedDownload) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', basename($file)))
                {
                    $this->parseMetaFile(dirname($file), true);
                    $arrMeta = $this->arrMeta[$objFile->basename];

                    if ($arrMeta[0] == '')
                    {
                        $arrMeta[0] = specialchars($objFile->basename);
                    }

                    $strHref = $this->Environment->request;

                    // Remove an existing file parameter (see #5683)
                    if (preg_match('/(&(amp;)?|\?)file=/', $strHref))
                    {
                        $strHref = preg_replace('/(&(amp;)?|\?)file=[^&]+/', '', $strHref);
                    }

                    $strHref .= (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos($strHref, '?') !== false) ? '&amp;' : '?') . 'file=' . $this->urlEncode($file);

                    $files[$file] = array
                    (
                        'link'      => $arrMeta[0],
                        'title'     => $arrMeta[0],
                        'href'      => $strHref,
                        'caption'   => $arrMeta[2],
                        'filesize'  => $this->getReadableSize($objFile->filesize, 1),
                        'icon'      => TL_ASSETS_URL . 'assets/contao/images/' . $objFile->icon,
                        'mime'      => $objFile->mime,
                        'meta'      => $arrMeta,
                        'extension' => $objFile->extension,
                        'path'      => $objFile->dirname
                    );

                    $auxDate[] = $objFile->mtime;
                }

                continue;
            }

            $subfiles = scan(TL_ROOT . '/' . $file);
            $this->parseMetaFile($file);

            // Folders
            foreach ($subfiles as $subfile)
            {
                if (is_dir(TL_ROOT . '/' . $file . '/' . $subfile))
                {
                    continue;
                }

                $objFile = new \File($file . '/' . $subfile);

                if (in_array($objFile->extension, $allowedDownload) && !preg_match('/^meta(_[a-z]{2})?\.txt$/', basename($subfile)))
                {
                    $arrMeta = $this->arrMeta[$objFile->basename];

                    if ($arrMeta[0] == '')
                    {
                        $arrMeta[0] = specialchars($objFile->basename);
                    }

                    $strHref = $this->Environment->request;

                    // Remove an existing file parameter (see #5683)
                    if (preg_match('/(&(amp;)?|\?)file=/', $strHref))
                    {
                        $strHref = preg_replace('/(&(amp;)?|\?)file=[^&]+/', '', $strHref);
                    }

                    $strHref .= (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos($strHref, '?') !== false) ? '&amp;' : '?') . 'file=' . $this->urlEncode($file . '/' . $subfile);

                    $files[$file . '/' . $subfile] = array
                    (
                        'link'      => $arrMeta[0],
                        'title'     => $arrMeta[0],
                        'href'      => $strHref,
                        'caption'   => $arrMeta[2],
                        'filesize'  => $this->getReadableSize($objFile->filesize, 1),
                        'icon'      => TL_ASSETS_URL . 'assets/contao/images/' . $objFile->icon,
                        'mime'      => $objFile->mime,
                        'meta'      => $arrMeta,
                        'extension' => $objFile->extension,
                        'path'      => $objFile->dirname
                    );

                    $auxDate[] = $objFile->mtime;
                }
            }
        }

        // Sort array
        switch ($this->sortBy)
        {
            default:
            case 'name_asc':
                uksort($files, 'basename_natcasecmp');
                break;

            case 'name_desc':
                uksort($files, 'basename_natcasercmp');
                break;

            case 'date_asc':
                array_multisort($files, SORT_NUMERIC, $auxDate, SORT_ASC);
                break;

            case 'date_desc':
                array_multisort($files, SORT_NUMERIC, $auxDate, SORT_DESC);
                break;

            case 'meta':
                $arrFiles = array();
                foreach ($this->arrAux as $k)
                {
                    if (strlen($k))
                    {
                        $arrFiles[] = $files[$k];
                    }
                }
                $files = $arrFiles;
                break;
        }

        $this->Template->files = array_values($files);
    }
}