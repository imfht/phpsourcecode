FCKConfig.EditorAreaCSS = FCKConfig.BasePath + 'css/fck_editorarea.css';
FCKConfig.ToolbarComboPreviewCSS = '';

FCKConfig.DocType = '';

FCKConfig.BaseHref = '';

FCKConfig.SkinPath = FCKConfig.BasePath + 'skins/default/';
FCKConfig.PreloadImages = [ FCKConfig.SkinPath + 'images/toolbar.start.gif', FCKConfig.SkinPath + 'images/toolbar.buttonarrow.gif' ];

FCKConfig.PluginsPath = FCKConfig.BasePath + 'plugins/';
FCKConfig.Plugins.Add('insertcode');

FCKConfig.AutoDetectLanguage = true;
FCKConfig.DefaultLanguage = 'zh-cn';
FCKConfig.ContentLangDirection = 'ltr';

FCKConfig.ProcessHTMLEntities = true;
FCKConfig.IncludeLatinEntities = true;
FCKConfig.IncludeGreekEntities = true;

FCKConfig.ProcessNumericEntities = false;

FCKConfig.AdditionalNumericEntities = '';		// Single Quote: "'"

FCKConfig.FillEmptyBlocks = true;

FCKConfig.FormatSource = true;
FCKConfig.FormatOutput = true;
FCKConfig.FormatIndentator = '    ';

FCKConfig.ForceStrongEm = true;
FCKConfig.StartupFocus = false;
FCKConfig.ForcePasteAsPlainText = false;
FCKConfig.TabSpaces = 0;
FCKConfig.IgnoreEmptyParagraphValue = true;
FCKConfig.FloatingPanelsZIndex = 10000;

FCKConfig.ToolbarSets["Default"] = [
    ['Source', 'Preview', 'PasteText', '-', 'Undo', 'Redo', '-', 'TextColor', 'BGColor', '-', 'OrderedList', 'UnorderedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter', 'JustifyRight'],
    '/',
    ['FontName', 'FontSize', '-', 'Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Link', 'Unlink', 'Image', 'Flash', 'Table', 'Rule', '-', 'InsertCode', 'About']
];

FCKConfig.ToolbarSets["Basic"] = [
    ['Source', '-', 'Bold', 'Italic', '-', 'OrderedList', 'UnorderedList', '-', 'Link', 'Unlink', 'Image', '-', 'About']
];

FCKConfig.FontColors = '000000,993300,333300,003300,003366,000080,333399,333333,800000,FF6600,808000,808080,008080,0000FF,666699,808080,FF0000,FF9900,99CC00,339966,33CCCC,3366FF,800080,999999,FF00FF,FFCC00,FFFF00,00FF00,00FFFF,00CCFF,993366,C0C0C0,FF99CC,FFCC99,FFFF99,CCFFCC,CCFFFF,99CCFF,CC99FF,FFFFFF';

FCKConfig.FontNames = 'Arial;Comic Sans MS;Courier New;Tahoma;Times New Roman;Verdana';
FCKConfig.FontSizes = '1/xx-small;2/x-small;3/small;4/medium;5/large;6/x-large;7/xx-large';

FCKConfig.MaxUndoLevels = 15;

FCKConfig.ProtectedTags = '';

// The option switches between trying to keep the html structure or do the changes so the content looks like it was in Word
FCKConfig.CleanWordKeepsStructure = false;
