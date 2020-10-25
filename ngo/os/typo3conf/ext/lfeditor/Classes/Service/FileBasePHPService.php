<?php

namespace SGalinski\Lfeditor\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) sgalinski Internet Services (https://www.sgalinski.de)
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use SGalinski\Lfeditor\Exceptions\LFException;
use SGalinski\Lfeditor\Utility\SgLib;
use SGalinski\Lfeditor\Utility\Typo3Lib;

/**
 * base workspace class (php)
 */
class FileBasePHPService extends FileBaseService {
	/**
	 * extended init
	 *
	 * @param string $file name of the file (can be a path, if you need this (no check))
	 * @param string $path path to the file
	 *
	 * @return void
	 * @throws LFException
	 */
	public function init($file, $path, $metaFile) {
		$this->setVar(['fileType' => 'php']);
		parent::init($file, $path, $metaFile);
	}

	/**
	 * reads a language file
	 *
	 * @throws LFException raised if the file does not contain a locallang array
	 * @param string $file language file
	 * @param string $langKey language shortcut (not used)
	 * @return array language content
	 */
	protected function readLLFile($file, $langKey) {
		if (!is_file($file)) {
			throw new LFException('failure.select.noLangfile');
		}

		include($file);

		/** @var array $LOCAL_LANG */
		if (!is_array($LOCAL_LANG) || !count($LOCAL_LANG)) {
			throw new LFException('failure.search.noFileContent', 0, '(' . $file . ')');
		}

		/** @var array $LFMETA */
		if ($langKey == 'default') {
			$this->meta = $LFMETA;
		}

		return $LOCAL_LANG;
	}

	/**
	 * checks the given content, if its a localized language file reference
	 *
	 * @param mixed $content language content (only one language)
	 * @param string $langKey language shortcut
	 * @return string localized file (absolute) or a boolean false
	 */
	protected function getLocalizedFile($content, $langKey) {
		if ((string) $content != 'EXT') {
			return '';
		}

		return Typo3Lib::fixFilePath(
			dirname($this->absFile) .
			'/' . $this->nameLocalizedFile($langKey)
		);
	}

	/**
	 * checks a filename, if its a localized file
	 *
	 * @param string $filename filename
	 * @param string $langKey language shortcut
	 * @return boolean true(localized) or false
	 */
	public function checkLocalizedFile($filename, $langKey) {
		if (!preg_match('/^.*\.(' . $langKey . ')\.php$/', $filename)) {
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * get the name of a localized file
	 *
	 * @param string $langKey language shortcut
	 * @return string localized file (only filename)
	 */
	public function nameLocalizedFile($langKey) {
		return SgLib::setFileExtension($langKey . '.php', basename($this->relFile));
	}

	/**
	 * prepares the meta data for writing into a file
	 *
	 * @return string meta data for writing purposes
	 */
	private function prepareMeta() {
		if (!is_array($this->meta) || !count($this->meta)) {
			return '';
		}

		$metaData = '';
		foreach ($this->meta as $metaIndex => $value) {
			$value = preg_replace('/[^\\\]\'/', '\\\'', $value);
			$metaData .= "\t" . '\'' . $metaIndex . '\' => \'' . $value . '\',' . "\n";
		}

		return $metaData;
	}

	/**
	 * generates the header data of a language file
	 *
	 * @return string header data
	 */
	private function getHeader() {
		$extKey = basename($this->absPath);

		$header = '<?php' . "\n";
		$header .= "/**\n * local language labels of module \"$extKey\"\n";
		$header .= " *\n * This file is detected by the translation tool\n";
		$header .= " *\n * Modified/Created by extension 'lfeditor'\n */\n\n";

		return $header;
	}

	/**
	 * generates the footer data of a language file
	 *
	 * @return string footer data
	 */
	private function getFooter() {
		return '?>' . "\n";
	}

	/**
	 * prepares the content of a language file
	 *
	 * @param array $localLang content of the given language
	 * @param string $lang language shortcut
	 * @return string language part of the main file
	 */
	private function getLangContent($localLang, $lang) {
		$content = "\t'$lang' => array (\n";
		if (is_array($localLang) && count($localLang)) {
			ksort($localLang);
			foreach ($localLang as $const => $value) {
				$value = preg_replace("/([^\\\])'/", "$1\'", $value);
				$value = str_replace("\r", '', $value);
				$content .= "\t\t'$const' => '$value',\n";
			}
		}
		$content .= "\t),\n";

		return $content;
	}

	/**
	 * prepares the content of a localized language file
	 *
	 * @param array $localLang content of the given language
	 * @param string $lang language shortcut
	 * @return string language content
	 */
	private function getLangContentLoc($localLang, $lang) {
		$content = '$LOCAL_LANG[\'' . $lang . '\'] = array (' . "\n";
		if (is_array($localLang) && count($localLang)) {
			ksort($localLang);
			foreach ($localLang as $const => $value) {
				$value = preg_replace("/([^\\\])'/", "$1\'", $value);
				$value = str_replace("\r", '', $value);
				$content .= "\t'$const' => '$value',\n";
			}
		}
		$content .= ");\n";

		return $content;
	}

	/**
	 * prepares the final content
	 *
	 * @param array | NULL $editedLanguages
	 * @return array language files as key and content as value
	 */
	protected function prepareFileContents($editedLanguages = NULL) {
		// prepare Content
		$mainFileContent = '';
		$languages = SgLib::getSystemLanguages();
		$languageFiles = array();
		foreach ($languages as $lang) {
			// get content of localized and main file
			if ($this->checkLocalizedFile(basename($this->originLang[$lang]), $lang)) {
				if (is_array($this->localLang[$lang]) && count($this->localLang[$lang])) {
					$languageFiles[$this->originLang[$lang]] = $this->getHeader();
					$languageFiles[$this->originLang[$lang]] .=
						$this->getLangContentLoc($this->localLang[$lang], $lang);
					$languageFiles[$this->originLang[$lang]] .= $this->getFooter();
					$mainFileContent .= "\t'$lang' => 'EXT',\n";
				} else {
					$mainFileContent .= "\t'$lang' => '',\n";
				}
			} else {
				$mainFileContent .= $this->getLangContent($this->localLang[$lang], $lang);
			}
		}

		// only a localized file?
		if ($this->checkLocalizedFile(basename($this->absFile), implode('|', SgLib::getSystemLanguages()))) {
			return $languageFiles;
		}

		// prepare Content for the main file
		$languageFiles[$this->absFile] = $this->getHeader();
		$languageFiles[$this->absFile] .= '$LFMETA = array (' . "\n";
		$languageFiles[$this->absFile] .= $this->prepareMeta();
		$languageFiles[$this->absFile] .= ');' . "\n\n";
		$languageFiles[$this->absFile] .= '$LOCAL_LANG = array (' . "\n";
		$languageFiles[$this->absFile] .= $mainFileContent;
		$languageFiles[$this->absFile] .= ');' . "\n";
		$languageFiles[$this->absFile] .= $this->getFooter();

		return $languageFiles;
	}
}

?>
