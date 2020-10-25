/*
  +----------------------------------------------------------------------+
  | PHP Version 7                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2018 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author:                                                              |
  +----------------------------------------------------------------------+
*/

/* $Id$ */

#ifndef PHP_IUP_H
#define PHP_IUP_H

extern zend_module_entry iup_module_entry;
#define phpext_iup_ptr &iup_module_entry

#define PHP_IUP_VERSION "0.1.0" /* Replace with version number for your extension */

#ifdef PHP_WIN32
#	define PHP_IUP_API __declspec(dllexport)
#elif defined(__GNUC__) && __GNUC__ >= 4
#	define PHP_IUP_API __attribute__ ((visibility("default")))
#else
#	define PHP_IUP_API
#endif

#ifdef ZTS
#include "TSRM.h"
#endif

/*
  	Declare any global variables you may need between the BEGIN
	and END macros here:

ZEND_BEGIN_MODULE_GLOBALS(iup)
	zend_long  global_value;
	char *global_string;
ZEND_END_MODULE_GLOBALS(iup)
*/

/* Always refer to the globals in your function as IUP_G(variable).
   You are encouraged to rename these macros something shorter, see
   examples in any other php module directory.
*/
#define IUP_G(v) ZEND_MODULE_GLOBALS_ACCESSOR(iup, v)

#if defined(ZTS) && defined(COMPILE_DL_IUP)
ZEND_TSRMLS_CACHE_EXTERN()
#endif

#include "ext/iup/iup3/include/iup.h"
#include "ext/iup/iup3/include/iupcbs.h"
#include "ext/iup/iup3/include/iupcontrols.h"
#include "ext/iup/iup3/include/iupdraw.h"
#include "ext/iup/iup3/include/iupgl.h"
#include "ext/iup/iup3/include/iupglcontrols.h"
#include "ext/iup/iup3/include/iupim.h"
#include "ext/iup/iup3/include/iupole.h"
#include "ext/iup/iup3/include/iupweb.h"
#include "ext/iup/iup3/include/iup_config.h"
#include "ext/iup/iup3/include/iup_mglplot.h"
#include "ext/iup/iup3/include/iup_plot.h"
#include "ext/iup/iup3/include/iup_scintilla.h"

#include "ext/iup/im3/include/im.h"
// // #include "ext/iup/im3/include/im_attrib_flat.h"
// #include "ext/iup/im3/include/im_binfile.h"
// #include "ext/iup/im3/include/im_capture.h"
// // #include "ext/iup/im3/include/im_color.h"
// #include "ext/iup/im3/include/im_colorhsi.h"
// #include "ext/iup/im3/include/im_complex.h"
// #include "ext/iup/im3/include/im_convert.h"
// #include "ext/iup/im3/include/im_counter.h"
// #include "ext/iup/im3/include/im_dib.h"
// #include "ext/iup/im3/include/im_file.h"
// #include "ext/iup/im3/include/im_format.h"
// #include "ext/iup/im3/include/im_format_all.h"
// #include "ext/iup/im3/include/im_format_avi.h"
// #include "ext/iup/im3/include/im_format_ecw.h"
// #include "ext/iup/im3/include/im_format_jp2.h"
// #include "ext/iup/im3/include/im_format_raw.h"
// #include "ext/iup/im3/include/im_format_wmv.h"
#include "ext/iup/im3/include/im_image.h"
// #include "ext/iup/im3/include/im_kernel.h"
// #include "ext/iup/im3/include/im_lib.h"
// // #include "ext/iup/im3/include/im_math.h"
// #include "ext/iup/im3/include/im_math_op.h"
// #include "ext/iup/im3/include/im_old.h"
// #include "ext/iup/im3/include/im_palette.h"
// #include "ext/iup/im3/include/im_plus.h"
// #include "ext/iup/im3/include/im_process.h"
// #include "ext/iup/im3/include/im_process_ana.h"
// #include "ext/iup/im3/include/im_process_glo.h"
// #include "ext/iup/im3/include/im_process_loc.h"
// #include "ext/iup/im3/include/im_process_pnt.h"
// #include "ext/iup/im3/include/im_raw.h"
// #include "ext/iup/im3/include/im_util.h"

#include "ext/iup/cd5/include/cd.h"
// #include "ext/iup/cd5/include/cdcairo.h"
// #include "ext/iup/cd5/include/cdcgm.h"
// #include "ext/iup/cd5/include/cdclipbd.h"
// #include "ext/iup/cd5/include/cddbuf.h"
// #include "ext/iup/cd5/include/cddebug.h"
// #include "ext/iup/cd5/include/cddgn.h"
// #include "ext/iup/cd5/include/cddxf.h"
// #include "ext/iup/cd5/include/cdemf.h"
// #include "ext/iup/cd5/include/cdgdiplus.h"
// #include "ext/iup/cd5/include/cdgl.h"
// #include "ext/iup/cd5/include/cdim.h"
// #include "ext/iup/cd5/include/cdimage.h"
// #include "ext/iup/cd5/include/cdirgb.h"
// #include "ext/iup/cd5/include/cdmf.h"
// #include "ext/iup/cd5/include/cdmf_private.h"
// #include "ext/iup/cd5/include/cdnative.h"
// #include "ext/iup/cd5/include/cdpdf.h"
// #include "ext/iup/cd5/include/cdpicture.h"
// #include "ext/iup/cd5/include/cdpptx.h"
// #include "ext/iup/cd5/include/cdprint.h"
// #include "ext/iup/cd5/include/cdps.h"
// #include "ext/iup/cd5/include/cdsvg.h"
// #include "ext/iup/cd5/include/cdwmf.h"
// #include "ext/iup/cd5/include/cd_canvas.hpp"
// #include "ext/iup/cd5/include/cd_old.h"
// #include "ext/iup/cd5/include/cd_plus.h"
// #include "ext/iup/cd5/include/cd_private.h"
// #include "ext/iup/cd5/include/wd.h"
// #include "ext/iup/cd5/include/wd_old.h"

void event_register_callback();
int event_set_callback(Ihandle *ih , char * event_name);
void event_del_callback(zend_string * event_key);
int event_common(char * event_name, Ihandle *ih);

PHP_FUNCTION(IupDebug);
PHP_FUNCTION(IupOpen);
PHP_FUNCTION(IupClose);
PHP_FUNCTION(IupIsOpened);
PHP_FUNCTION(IupMainLoop);
PHP_FUNCTION(IupLoopStep);
PHP_FUNCTION(IupLoopStepWait);
PHP_FUNCTION(IupMainLoopLevel);
PHP_FUNCTION(IupImageLibOpen);
PHP_FUNCTION(IupFlush);
PHP_FUNCTION(IupExitLoop);
PHP_FUNCTION(IupPostMessage);
PHP_FUNCTION(IupRecordInput);
PHP_FUNCTION(IupPlayInput);
PHP_FUNCTION(IupUpdate);
PHP_FUNCTION(IupUpdateChildren);
PHP_FUNCTION(IupRedraw);
PHP_FUNCTION(IupRefresh);
PHP_FUNCTION(IupRefreshChildren);
PHP_FUNCTION(IupExecute);
PHP_FUNCTION(IupExecuteWait);
PHP_FUNCTION(IupHelp);
PHP_FUNCTION(IupLog);
PHP_FUNCTION(IupLoad);
PHP_FUNCTION(IupLoadBuffer);
PHP_FUNCTION(IupVersion);
PHP_FUNCTION(IupVersionDate);
PHP_FUNCTION(IupVersionNumber);
PHP_FUNCTION(IupVersionShow);
PHP_FUNCTION(IupSetLanguage);
PHP_FUNCTION(IupGetLanguage);
PHP_FUNCTION(IupSetLanguageString);
PHP_FUNCTION(IupStoreLanguageString);
PHP_FUNCTION(IupGetLanguageString);
PHP_FUNCTION(IupSetLanguagePack);
PHP_FUNCTION(IupDestroy);
PHP_FUNCTION(IupDetach);
PHP_FUNCTION(IupAppend);
PHP_FUNCTION(IupInsert);
PHP_FUNCTION(IupGetChild);
PHP_FUNCTION(IupGetChildPos);
PHP_FUNCTION(IupGetChildCount);
PHP_FUNCTION(IupGetNextChild);
PHP_FUNCTION(IupGetBrother);
PHP_FUNCTION(IupGetParent);
PHP_FUNCTION(IupGetDialog);
PHP_FUNCTION(IupGetDialogChild);
PHP_FUNCTION(IupReparent);
PHP_FUNCTION(IupPopup);
PHP_FUNCTION(IupShow);
PHP_FUNCTION(IupShowXY);
PHP_FUNCTION(IupHide);
PHP_FUNCTION(IupMap);
PHP_FUNCTION(IupUnmap);
PHP_FUNCTION(IupResetAttribute);
PHP_FUNCTION(IupGetAllAttributes);
PHP_FUNCTION(IupSetAtt);
PHP_FUNCTION(IupSetAttributes);
PHP_FUNCTION(IupGetAttributes);
PHP_FUNCTION(IupSetAttribute);
PHP_FUNCTION(IupSetStrAttribute);
PHP_FUNCTION(IupSetStrf);
PHP_FUNCTION(IupSetInt);
PHP_FUNCTION(IupSetFloat);
PHP_FUNCTION(IupSetDouble);
PHP_FUNCTION(IupSetRGB);
PHP_FUNCTION(IupGetAttribute);
PHP_FUNCTION(IupGetInt);
PHP_FUNCTION(IupGetInt2);
PHP_FUNCTION(IupGetIntInt);
PHP_FUNCTION(IupGetFloat);
PHP_FUNCTION(IupGetDouble);
PHP_FUNCTION(IupGetRGB);
PHP_FUNCTION(IupSetAttributeId);
PHP_FUNCTION(IupSetStrAttributeId);
PHP_FUNCTION(IupSetStrfId);
PHP_FUNCTION(IupSetIntId);
PHP_FUNCTION(IupSetFloatId);
PHP_FUNCTION(IupSetDoubleId);
PHP_FUNCTION(IupSetRGBId);
PHP_FUNCTION(IupGetAttributeId);
PHP_FUNCTION(IupGetIntId);
PHP_FUNCTION(IupGetFloatId);
PHP_FUNCTION(IupGetDoubleId);
PHP_FUNCTION(IupGetRGBId);
PHP_FUNCTION(IupSetAttributeId2);
PHP_FUNCTION(IupSetStrAttributeId2);
PHP_FUNCTION(IupSetStrfId2);
PHP_FUNCTION(IupSetIntId2);
PHP_FUNCTION(IupSetFloatId2);
PHP_FUNCTION(IupSetDoubleId2);
PHP_FUNCTION(IupSetRGBId2);
PHP_FUNCTION(IupGetAttributeId2);
PHP_FUNCTION(IupGetIntId2);
PHP_FUNCTION(IupGetFloatId2);
PHP_FUNCTION(IupGetDoubleId2);
PHP_FUNCTION(IupGetRGBId2);
PHP_FUNCTION(IupSetGlobal);
PHP_FUNCTION(IupSetStrGlobal);
PHP_FUNCTION(IupGetGlobal);
PHP_FUNCTION(IupSetFocus);
PHP_FUNCTION(IupGetFocus);
PHP_FUNCTION(IupPreviousField);
PHP_FUNCTION(IupNextField);
PHP_FUNCTION(IupSetCallback);
PHP_FUNCTION(IupGetCallback);
PHP_FUNCTION(IupSetCallbacks);
PHP_FUNCTION(IupGetFunction);
PHP_FUNCTION(IupSetFunction);
PHP_FUNCTION(IupGetHandle);
PHP_FUNCTION(IupSetHandle);
PHP_FUNCTION(IupGetAllNames);
PHP_FUNCTION(IupGetAllDialogs);
PHP_FUNCTION(IupGetName);
PHP_FUNCTION(IupSetAttributeHandle);
PHP_FUNCTION(IupGetAttributeHandle);
PHP_FUNCTION(IupSetAttributeHandleId);
PHP_FUNCTION(IupGetAttributeHandleId);
PHP_FUNCTION(IupSetAttributeHandleId2);
PHP_FUNCTION(IupGetAttributeHandleId2);
PHP_FUNCTION(IupGetClassName);
PHP_FUNCTION(IupGetClassType);
PHP_FUNCTION(IupGetAllClasses);
PHP_FUNCTION(IupGetClassAttributes);
PHP_FUNCTION(IupGetClassCallbacks);
PHP_FUNCTION(IupSaveClassAttributes);
PHP_FUNCTION(IupCopyClassAttributes);
PHP_FUNCTION(IupSetClassDefaultAttribute);
PHP_FUNCTION(IupClassMatch);
PHP_FUNCTION(IupCreatek);
PHP_FUNCTION(IupCreatev);
PHP_FUNCTION(IupCreatep);
PHP_FUNCTION(IupFill);
PHP_FUNCTION(IupSpace);
PHP_FUNCTION(IupRadio);
PHP_FUNCTION(IupVbox);
PHP_FUNCTION(IupVboxv);
PHP_FUNCTION(IupZbox);
PHP_FUNCTION(IupZboxv);
PHP_FUNCTION(IupHbox);
PHP_FUNCTION(IupHboxv);
PHP_FUNCTION(IupNormalizer);
PHP_FUNCTION(IupNormalizerv);
PHP_FUNCTION(IupCbox);
PHP_FUNCTION(IupCboxv);
PHP_FUNCTION(IupSbox);
PHP_FUNCTION(IupSplit);
PHP_FUNCTION(IupScrollBox);
PHP_FUNCTION(IupFlatScrollBox);
PHP_FUNCTION(IupGridBox);
PHP_FUNCTION(IupGridBoxv);
PHP_FUNCTION(IupExpander);
PHP_FUNCTION(IupDetachBox);
PHP_FUNCTION(IupBackgroundBox);
PHP_FUNCTION(IupFrame);
PHP_FUNCTION(IupFlatFrame);
PHP_FUNCTION(IupImage);
PHP_FUNCTION(IupImageRGB);
PHP_FUNCTION(IupImageRGBA);
PHP_FUNCTION(IupItem);
PHP_FUNCTION(IupSubmenu);
PHP_FUNCTION(IupSeparator);
PHP_FUNCTION(IupMenu);
PHP_FUNCTION(IupMenuv);
PHP_FUNCTION(IupButton);
PHP_FUNCTION(IupFlatButton);
PHP_FUNCTION(IupFlatToggle);
PHP_FUNCTION(IupDropButton);
PHP_FUNCTION(IupFlatLabel);
PHP_FUNCTION(IupFlatSeparator);
PHP_FUNCTION(IupCanvas);
PHP_FUNCTION(IupDialog);
PHP_FUNCTION(IupUser);
PHP_FUNCTION(IupThread);
PHP_FUNCTION(IupLabel);
PHP_FUNCTION(IupList);
PHP_FUNCTION(IupFlatList);
PHP_FUNCTION(IupText);
PHP_FUNCTION(IupMultiLine);
PHP_FUNCTION(IupToggle);
PHP_FUNCTION(IupTimer);
PHP_FUNCTION(IupClipboard);
PHP_FUNCTION(IupProgressBar);
PHP_FUNCTION(IupVal);
PHP_FUNCTION(IupFlatVal);
PHP_FUNCTION(IupTabs);
PHP_FUNCTION(IupTabsv);
PHP_FUNCTION(IupFlatTabs);
PHP_FUNCTION(IupFlatTabsv);
PHP_FUNCTION(IupTree);
PHP_FUNCTION(IupLink);
PHP_FUNCTION(IupAnimatedLabel);
PHP_FUNCTION(IupDatePick);
PHP_FUNCTION(IupCalendar);
PHP_FUNCTION(IupColorbar);
PHP_FUNCTION(IupGauge);
PHP_FUNCTION(IupDial);
PHP_FUNCTION(IupColorBrowser);
PHP_FUNCTION(IupSpin);
PHP_FUNCTION(IupSpinbox);
PHP_FUNCTION(IupStringCompare);
PHP_FUNCTION(IupSaveImageAsText);
PHP_FUNCTION(IupImageGetHandle);
PHP_FUNCTION(IupTextConvertLinColToPos);
PHP_FUNCTION(IupTextConvertPosToLinCol);
PHP_FUNCTION(IupConvertXYToPos);
PHP_FUNCTION(IupStoreGlobal);
PHP_FUNCTION(IupStoreAttribute);
PHP_FUNCTION(IupSetfAttribute);
PHP_FUNCTION(IupStoreAttributeId);
PHP_FUNCTION(IupSetfAttributeId);
PHP_FUNCTION(IupStoreAttributeId2);
PHP_FUNCTION(IupSetfAttributeId2);
PHP_FUNCTION(IupTreeSetUserId);
PHP_FUNCTION(IupTreeGetUserId);
PHP_FUNCTION(IupTreeGetId);
PHP_FUNCTION(IupTreeSetAttributeHandle);
PHP_FUNCTION(IupFileDlg);
PHP_FUNCTION(IupMessageDlg);
PHP_FUNCTION(IupColorDlg);
PHP_FUNCTION(IupFontDlg);
PHP_FUNCTION(IupProgressDlg);
PHP_FUNCTION(IupGetFile);
PHP_FUNCTION(IupMessage);
PHP_FUNCTION(IupMessagef);
PHP_FUNCTION(IupMessageError);
PHP_FUNCTION(IupMessageAlarm);
PHP_FUNCTION(IupAlarm);
PHP_FUNCTION(IupScanf);
PHP_FUNCTION(IupListDialog);
PHP_FUNCTION(IupGetText);
PHP_FUNCTION(IupGetColor);
PHP_FUNCTION(IupGetParam);
PHP_FUNCTION(IupGetParamv);
PHP_FUNCTION(IupParam);
PHP_FUNCTION(IupParamBox);
PHP_FUNCTION(IupParamBoxv);
PHP_FUNCTION(IupLayoutDialog);
PHP_FUNCTION(IupElementPropertiesDialog);
PHP_FUNCTION(IupGlobalsDialog);
PHP_FUNCTION(IupClassInfoDialog);


// more.c

PHP_FUNCTION(IupConfig);
PHP_FUNCTION(IupConfigLoad);
PHP_FUNCTION(IupConfigSave);
PHP_FUNCTION(IupConfigSetVariableStr);
PHP_FUNCTION(IupConfigSetVariableStrId);
PHP_FUNCTION(IupConfigSetVariableInt);
PHP_FUNCTION(IupConfigSetVariableIntId);
PHP_FUNCTION(IupConfigSetVariableDouble);
PHP_FUNCTION(IupConfigSetVariableDoubleId);
PHP_FUNCTION(IupConfigGetVariableStr);
PHP_FUNCTION(IupConfigGetVariableStrId);
PHP_FUNCTION(IupConfigGetVariableInt);
PHP_FUNCTION(IupConfigGetVariableIntId);
PHP_FUNCTION(IupConfigGetVariableDouble);
PHP_FUNCTION(IupConfigGetVariableDoubleId);
PHP_FUNCTION(IupConfigGetVariableStrDef);
PHP_FUNCTION(IupConfigGetVariableStrIdDef);
PHP_FUNCTION(IupConfigGetVariableIntDef);
PHP_FUNCTION(IupConfigGetVariableIntIdDef);
PHP_FUNCTION(IupConfigGetVariableDoubleDef);
PHP_FUNCTION(IupConfigGetVariableDoubleIdDef);
PHP_FUNCTION(IupConfigCopy);
PHP_FUNCTION(IupConfigSetListVariable);
PHP_FUNCTION(IupConfigRecentInit);
PHP_FUNCTION(IupConfigRecentUpdate);
PHP_FUNCTION(IupConfigDialogShow);
PHP_FUNCTION(IupConfigDialogClosed);

#ifdef PHP_IUP_SCINTILLA
PHP_FUNCTION(IupScintillaOpen);
PHP_FUNCTION(IupScintilla);
PHP_FUNCTION(IupScintillaDlg);
#endif

PHP_FUNCTION(IupWebBrowserOpen);
PHP_FUNCTION(IupWebBrowser);

/*PHP_FUNCTION(IupTuioOpen);
PHP_FUNCTION(IupTuioClient);*/

PHP_FUNCTION(IupOleControlOpen);
PHP_FUNCTION(IupOleControl);

PHP_FUNCTION(IupLoadImage);
PHP_FUNCTION(IupSaveImage);
PHP_FUNCTION(IupLoadAnimation);
PHP_FUNCTION(IupLoadAnimationFrames);
PHP_FUNCTION(IupGetNativeHandleImage);
PHP_FUNCTION(IupGetImageNativeHandle);
PHP_FUNCTION(IupImageFromImImage);
PHP_FUNCTION(IupImageToImImage);

PHP_FUNCTION(IupDrawBegin);
PHP_FUNCTION(IupDrawEnd);
PHP_FUNCTION(IupDrawSetClipRect);
PHP_FUNCTION(IupDrawGetClipRect);
PHP_FUNCTION(IupDrawResetClip);
PHP_FUNCTION(IupDrawParentBackground);
PHP_FUNCTION(IupDrawLine);
PHP_FUNCTION(IupDrawRectangle);
PHP_FUNCTION(IupDrawArc);
PHP_FUNCTION(IupDrawPolygon);
PHP_FUNCTION(IupDrawText);
PHP_FUNCTION(IupDrawImage);
PHP_FUNCTION(IupDrawSelectRect);
PHP_FUNCTION(IupDrawFocusRect);
PHP_FUNCTION(IupDrawGetSize);
PHP_FUNCTION(IupDrawGetTextSize);
PHP_FUNCTION(IupDrawGetImageInfo);

PHP_FUNCTION(IupControlsOpen);
PHP_FUNCTION(IupCells);
PHP_FUNCTION(IupMatrix);
PHP_FUNCTION(IupMatrixList);
PHP_FUNCTION(IupMatrixEx);
PHP_FUNCTION(IupMatrixSetFormula);
PHP_FUNCTION(IupMatrixSetDynamic);

PHP_FUNCTION(IupPlotOpen);
PHP_FUNCTION(IupPlot);
PHP_FUNCTION(IupPlotBegin);
PHP_FUNCTION(IupPlotAdd);
PHP_FUNCTION(IupPlotAddStr);
PHP_FUNCTION(IupPlotAddSegment);
PHP_FUNCTION(IupPlotEnd);
PHP_FUNCTION(IupPlotLoadData);
PHP_FUNCTION(IupPlotSetFormula);
PHP_FUNCTION(IupPlotInsert);
PHP_FUNCTION(IupPlotInsertStr);
PHP_FUNCTION(IupPlotInsertSegment);
PHP_FUNCTION(IupPlotInsertStrSamples);
PHP_FUNCTION(IupPlotInsertSamples);
PHP_FUNCTION(IupPlotAddSamples);
PHP_FUNCTION(IupPlotAddStrSamples);
PHP_FUNCTION(IupPlotGetSample);
PHP_FUNCTION(IupPlotGetSampleStr);
PHP_FUNCTION(IupPlotGetSampleSelection);
PHP_FUNCTION(IupPlotGetSampleExtra);
PHP_FUNCTION(IupPlotSetSample);
PHP_FUNCTION(IupPlotSetSampleStr);
PHP_FUNCTION(IupPlotSetSampleSelection);
PHP_FUNCTION(IupPlotSetSampleExtra);
PHP_FUNCTION(IupPlotTransform);
PHP_FUNCTION(IupPlotTransformTo);
PHP_FUNCTION(IupPlotFindSample);
PHP_FUNCTION(IupPlotFindSegment);
#endif	/* PHP_IUP_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */
