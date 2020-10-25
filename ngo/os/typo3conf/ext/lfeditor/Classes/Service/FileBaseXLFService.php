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
use SGalinski\Lfeditor\Utility\ExtensionUtility;
use SGalinski\Lfeditor\Utility\SgLib;
use SGalinski\Lfeditor\Utility\Typo3Lib;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * base workspace class (xlf)
 */
class FileBaseXLFService extends FileBaseService {
	/**
	 * extended init
	 *
	 * @param string $file name of the file (can be a path, if you need this (no check))
	 * @param string $path path to the file
	 * @param $metaFile
	 * @return void
	 * @throws LFException
	 */
	public function init($file, $path, $metaFile) {
		$this->setFileType('xlf');
		parent::init($file, $path, $metaFile);
	}

	/**
	 * calls the parent function and convert all values from utf-8 to the original charset
	 *
	 * @throws LFException raised if the parent read file method fails
	 * @return void
	 */
	public function readFile() {
		$this->readXlfFile();

		foreach ($this->localLang['default'] as $constant => $value) {
			if (empty($value)) {
				$this->localLang['default'][$constant] = '';
			}
		}
	}

	/**
	 * reads a language file
	 *
	 * @throws LFException thrown if no data can be found
	 * @param string $file language file
	 * @param string $langKey language shortcut
	 * @return array language content
	 */
	protected function readLLFile($file, $langKey) {
		if (!\is_file($file)) {
			throw new LFException('failure.select.noLangfile');
		}

		// LIBXML_NOCDATA treat CDATA node as text node.
		// htmlentities in CDATA are not modified,
		// and those - normaly encoded if XML is valid - outside CDATA will be decoded.

		$xmlContent = \simplexml_load_string(\file_get_contents($file), 'SimpleXMLElement', LIBXML_NOCDATA);

		// Cast XML to associative array with json_decode/encode.
		// This way, value is XML are casted to string.
		$xmlContent = \json_decode(\json_encode($xmlContent), TRUE);

		// check data
		if (!\is_array($xmlContent['file']['body'])) {
			throw new LFException('failure.search.noFileContent', 0, '(' . $file . ')');
		}

		if ($langKey === 'default') {
			$this->meta = $xmlContent['file']['header'];
		}
		$this->meta['@attributes'] = $xmlContent['file']['@attributes'];

		return $xmlContent['file']['body'];
	}

	/**
	 * Resolves the trans-unit parts of the xlf language files into flat
	 * source arrays with the target language or the source if the target is en
	 *
	 * @param array $sourceData
	 * @return array
	 */
	public function resolveTranslationUnitsArrayIntoFlatArray(array $sourceData) {
		$flatData = array();

		if (isset($sourceData['trans-unit']['@attributes'])) {
			$sourceData['trans-unit'] = array($sourceData['trans-unit']);
		}

		foreach ((array) $sourceData['trans-unit'] as $data) {
			if (isset($data['@attributes']['id'])) {
				$constant = $data['@attributes']['id'];
				if ($data['target']) {
					$flatData[$constant] = $data['target'];
				} else {
					$flatData[$constant] = $data['source'];
				}

				if (empty($flatData[$constant])) {
					$flatData[$constant] = '';
				}
			}
		}

		return $flatData;
	}

	/**
	 * Reads the absolute language file with all localized sub files
	 *
	 * @throws LFException
	 * @return void
	 */
	public function readXlfFile() {
		// read absolute file
		$localLang['default'] = $this->readLLFile($this->absFile, 'default');
		$localLang['default'] = $this->resolveTranslationUnitsArrayIntoFlatArray($localLang['default']);

		// loop all languages
		$originLang = array();
		$languages = SgLib::getSystemLanguages();
		foreach ($languages as $lang) {
			$originLang[$lang] = $this->absFile;
			if ($lang === 'default') {
				continue;
			}

			// get localized file
			$lFile = $this->getLocalizedFile($localLang[$lang], $lang);
			if ($this->checkLocalizedFile(\basename($lFile), $lang)) {
				$originLang[$lang] = $lFile;
				$localLang[$lang] = array();

				if (!\is_file($lFile)) {
					continue;
				}

				// read the content
				$localSubLanguage = $this->readLLFile($lFile, $lang);
				$localLang[$lang] = $this->resolveTranslationUnitsArrayIntoFlatArray($localSubLanguage);

				// merge arrays and save origin of current language
				ArrayUtility::mergeRecursiveWithOverrule($localLang, $localSubLanguage);
			}
		}

		// copy all to object variables, if everything was ok
		$this->localLang = $localLang;
		$this->originLang = $originLang;
	}

	/**
	 * checks the localLang array to find localized version of the language
	 * (checks l10n directory too)
	 *
	 * @param string $content language content (only one language)
	 * @param string $langKey language shortcut
	 * @return string localized file (absolute)
	 */
	protected function getLocalizedFile($content, $langKey) {
		$file = '';
		if ($this->session->getDataByKey('editingMode') !== 'extension') {
			$file = $this->getLocalizedFileName($this->absFile, $langKey);
		}
		if (!\is_file($file)) {
			$file = \dirname($this->absFile) . '/' . $this->nameLocalizedFile($langKey);
		}
		return Typo3Lib::fixFilePath($file);
	}

	/**
	 * checks a filename if its a localized file reference
	 *
	 * @param string $filename
	 * @param string $langKey language shortcut
	 * @return boolean true(localized) or false
	 */
	public function checkLocalizedFile($filename, $langKey) {
		if (!\preg_match('/^(' . $langKey . ')\..*\.xlf$/', $filename)) {
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
		return $langKey . '.' . \basename($this->relFile);
	}

	/**
	 * generates the xml header
	 *
	 * @return string xml header
	 */
	private function getXMLHeader() {
		return '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>' . "\n";

	}

	/**
	 * converts the array to a xml string
	 *
	 * @param array $phpArray
	 * @param string $targetLanguage
	 * @param array $enLanguage
	 * @return string xml content
	 */
	private function array2xml($phpArray, $targetLanguage, $enLanguage) {
		$targetLanguage = \htmlspecialchars($targetLanguage);
		$targetLanguageAttribute = ($targetLanguage !== 'default' ? ' target-language="' . $targetLanguage . '"' : '');

		$lfeditorExtConf = ExtensionUtility::getExtensionConfiguration();
		$date = ($lfeditorExtConf['changeXlfDate'] ? \gmdate('Y-m-d\TH:i:s\Z') : $this->meta['@attributes']['date']);

		$xmlString = $this->getXMLHeader() . '<xliff version="1.0">
	<file source-language="en"' . $targetLanguageAttribute . ' datatype="plaintext" original="messages" date="' . $date . '">
		###HEADER###
		###BODY###
	</file>
</xliff>';

		$header = '<header/>';
		if (\is_array($phpArray['header']) && \count($phpArray['header'])) {
			$header = '<header>' . "\n";
			foreach ($phpArray['header'] as $tagName => $tagValue) {
				$tagName = \htmlspecialchars($tagName);
				$header .= "\t\t\t" . '<' . $tagName . '>' . \htmlspecialchars(
						$tagValue
					) . '</' . $tagName . '>' . "\n";
			}
			$header .= "\t\t" . '</header>';
		}
		$xmlString = \str_replace('###HEADER###', $header, $xmlString);

		$body = '<body/>';
		if (\is_array($phpArray['data']) && \count($phpArray['data'])) {
			$body = '<body>' . "\n";
			$startCDATA = "<![CDATA[";
			$endCDATA = "]]>";
			$approved = ($targetLanguage !== 'default' ? ' approved="yes"' : '');
			foreach ($phpArray['data'] as $constant => $value) {
				$enValue = $enLanguage[$constant];

				// CDATA markers are stripped when reading the XML file from the disk,
				// and html entities are not decoded.
				// So, for simplicity and safety, when writing back the data to the file,
				// we will always enclose value in CDATA, so html entities are preserved
				// and XML remains valid.
				$value = $startCDATA.$value.$endCDATA;
				$enValue = $startCDATA.$enValue.$endCDATA;

				$body .= "\t\t\t" . '<trans-unit id="' . \htmlspecialchars(
						$constant
					) . '"' . $approved . $this->addPreserveSpaceAttribute(
						$value, $enValue, $targetLanguage
					) . '>' . "\n";

				if ($targetLanguage !== 'default') {
					$body .= "\t\t\t\t" . '<source>' . $enValue . '</source>' . "\n";
					$body .= "\t\t\t\t" . '<target>' . $value . '</target>' . "\n";
				} else {
					$body .= "\t\t\t\t" . '<source>' . $value . '</source>' . "\n";
				}
				$body .= "\t\t\t" . '</trans-unit>' . "\n";
			}
			$body .= "\t\t" . '</body>';
		}
		$xmlString = \str_replace('###BODY###', $body, $xmlString);

		return $xmlString;
	}

	/**
	 * prepares the content of a language file
	 *
	 * @param array $localLang
	 * @return array new xml array
	 */
	private function getLangContent($localLang) {
		$content = array();
		if (!\is_array($localLang) || !\count($localLang)) {
			return $content;
		}

		\ksort($localLang);
		foreach ($localLang as $const => $value) {
			$content[$const] = \str_replace("\r", '', $value);
		}

		return $content;
	}

	/**
	 * prepares the meta array for nicer saving
	 *
	 * @return array meta content
	 */
	private function prepareMeta() {
		if (\is_array($this->meta) && \count($this->meta)) {
			foreach ($this->meta as $label => $value) {
				$this->meta[$label] = \str_replace("\r", '', $value);
			}
		}
		$this->addGeneratorString();

		$metadata = $this->meta;
		unset($metadata['@attributes']);

		return $metadata;
	}

	/**
	 * prepares the final content
	 *
	 * @param array | NULL $editedLanguages
	 * @return array language files as key and content as value
	 */
	protected function prepareFileContents($editedLanguages = NULL) {
		// prepare Content
		$metaData = $this->prepareMeta();
		$languages = SgLib::getSystemLanguages();
		$languageFiles = array();
		$enLanguage = $this->getLangContent($this->localLang['default']);
		foreach ($languages as $lang) {
			if ($lang === 'default') {
				continue;
			}
			// If default language content and $lang language content are not edited, skip this file.
			if ($editedLanguages !== NULL &&
				!\in_array('default', $editedLanguages, TRUE) && !\in_array($lang, $editedLanguages, TRUE)
			) {
				continue;
			}

			if (\is_array($this->localLang[$lang]) && \count($this->localLang[$lang])) {
				$file = $this->originLang[$lang];
				$data = array(
					'header' => $metaData,
					'data' => $this->getLangContent($this->localLang[$lang]),
				);
				$languageFiles[$file] .= $this->array2xml($data, $lang, $enLanguage);
			}
		}

		// only a localized file?
		if ($this->checkLocalizedFile(\basename($this->absFile), \implode('|', SgLib::getSystemLanguages()))) {
			return $languageFiles;
		}
		if ($editedLanguages !== NULL && !\in_array('default', $editedLanguages, TRUE)) {
			return $languageFiles;
		}

		// prepare content for the main file
		$data = array(
			'header' => $metaData,
			'data' => $enLanguage,
		);
		$languageFiles[$this->absFile] = $this->array2xml($data, 'default', $enLanguage);

		return $languageFiles;
	}

	/**
	 * If constant's source (en) or target value contains multiple spaces or LF character,
	 * returns string ' xml:space="preserve"'.
	 *
	 * @param string $value
	 * @param string $enValue
	 * @param string $targetLanguage
	 * @return string
	 */
	protected function addPreserveSpaceAttribute($value, $enValue, $targetLanguage) {
		$valueContainsSpacesOrLF = \strpos($value, '  ') !== FALSE || \strpos($value, "\n") !== FALSE;
		$enValueContainsSpacesOrLF = FALSE;
		if ($targetLanguage !== 'default') {
			$enValueContainsSpacesOrLF = \strpos($enValue, '  ') !== FALSE || \strpos($enValue, "\n") !== FALSE;
		}
		return ($valueContainsSpacesOrLF || $enValueContainsSpacesOrLF ? ' xml:space="preserve"' : '');
	}
}

?>
