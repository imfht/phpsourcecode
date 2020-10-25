# Extension manual phpexcel_service

phpexcel_service provides the library [PHPExcel](https://phpexcel.codeplex.com) for TYPO3.
This version contains the version 1.8.0 and requires TYPO3 6.0 or greater.

The [documentation of PHPExcel](https://github.com/PHPOffice/PHPExcel/wiki/User%20Documentation) itself can be found on github.

## Build Status

|master|develop|
|------|-------|
|[![dce Master branch](http://ci.v.ieweg.de/build-status/image/6?branch=master)](http://ci.v.ieweg.de/build-status/view/6?branch=master)|[![dce Develop Branch](http://ci.v.ieweg.de/build-status/image/6?branch=develop)](http://ci.v.ieweg.de/build-status/view/6?branch=develop)|


## Installation

In earlier versions of this extension the PHPExcel library itself wasn't shipped
with the extension, due file size limitations of TER. But in this version the
library is already included. No further steps are necessary after installing.


## How to use?

It is very easy to integrate PHPExcel to your projects. The extension provides a service
for TYPO3, which helps you to instanciate the PHPExcel library.

Example:
```
    /** @var \ArminVieweg\PhpexcelService\Service\Phpexcel $phpExcelService */
	$phpExcelService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstanceService('phpexcel');
	$phpExcel = $phpExcelService->getPHPExcel();

	// Your excel magic goes here...

	/** @var \PHPExcel_Writer_Excel2007 $excelWriter */
	$excelWriter = $phpExcelService->getInstanceOf('PHPExcel_Writer_Excel2007', $phpExcel);
	$excelWriter->save('...');
```

## Support

Because this extension is just a small wrapper for PHPExcel I have decided to
create no forge project. If you have questions about this extension (not PHPExcel itself)
feel free to contact me by mail or [twitter](https://twitter.com/ArminVieweg). You'll find my mail address in ext_emconf.php.

For own purposes I have created a [Bitbucket repository](https://bitbucket.org/ArminVieweg/phpexcel_service) for this extension.