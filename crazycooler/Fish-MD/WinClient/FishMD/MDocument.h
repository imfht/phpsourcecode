/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:用于和Markdown对应web的交互功能，这个是c++和javascript两种语言交互的桥梁
*/
/************************************************************************/
#pragma once
#include "MCommon.h"
#include <QObject>

class MResponseData;

class MDocument : public QObject
{
	Q_OBJECT
	//导出initData给javascript
	Q_PROPERTY(QString initData MEMBER m_initData NOTIFY initDataChanged)

public:
	MDocument(QObject *parent=nullptr);
	~MDocument();

public:
	/************************************************************************/
	/* 
	获得当前显示的markdown文档的数据
	@param mback 表示获得数据后的操作描述
	*/
	/************************************************************************/
	void getValue(const QString &mback = "");

	/************************************************************************/
	/* 
	设置当前markdown文档中数据
	*setValue后，仍然可以将m_changeFlag设置为false
	*/
	/************************************************************************/
	void setValue(const QString &value);

	/************************************************************************/
	/* 
	一些基本操作，现在暂时没有用到
	*/
	/************************************************************************/
	void undo();
	void redo();
	void preview();
	void showToolbar(bool isShow);

	/************************************************************************/
	/* 
	切换doc的显示模式，EDITOR_MODE_EDIT : EDITOR_MODE_PREVIEW
	*/
	/************************************************************************/
	void changeMode(int mode);

	/************************************************************************/
	/* 
	web初始化数据的设置，用户传递给javascript
	*/
	/************************************************************************/
	void setInitData(const QString &initData) { m_initData = initData; }

	/************************************************************************/
	/* 
	粘贴图片
	*/
	/************************************************************************/
	void pasteImage();

	/************************************************************************/
	/* 
	获得当前文档的显示模式
	*/
	/************************************************************************/
	int getEditorMode() { return m_editorMode; }

	/************************************************************************/
	/* 
	保存文档
	*/
	/************************************************************************/
	void save();

signals:
	/************************************************************************/
	/* 
	用于给markdown页面发送Action（操作）
	@param action 操作类型
	@param value  对应操作需要的数据
	@param mback  操作完成后，后续其它操作执行的描述
	*/
	/************************************************************************/
	void doActionEmit(const QString &action,const QString &value = "",const QString &mback = "");

	/************************************************************************/
	/* 
	文档完成保存后的信号
	*/
	/************************************************************************/
	void afterSave();

	/************************************************************************/
	/* 
	接收javascript端对initData修改后的信号
	*/
	/************************************************************************/
	void initDataChanged(QString initData);
public slots:
	/************************************************************************/
	/* 
	接收javascript端发送回来的信号
	@param action : 操作类型
	@param value:具体格式根据操作类型决定，一般用json
	@param mback:doActionEmit发送的原样返回
	*/
	/************************************************************************/
	void onEventNotify(const QString &action,const QString &value,const QString &mback="");
	

public:
	/************************************************************************/
	/* 
	加载文档
	@param id 文档ID
	@param isEditMode 是否编辑模式加载
	*/
	/************************************************************************/
	void loadDoc(const QString& id, bool isEditMode=false);

	/************************************************************************/
	/* 
	加载新文档
	@param id 文档ID
	@param content 文档内容
	*/
	/************************************************************************/
	void loadNewDoc(const QString& id,const QString &content);


	/************************************************************************/
	/* 
	图片发送给服务器后的应答
	*/
	/************************************************************************/
	void pasteImageCallback(int status, const MResponseData &data);

protected:
	/************************************************************************/
	/* 
	定时器，用来定时做doc缓存
	*/
	/************************************************************************/
	virtual void timerEvent(QTimerEvent *event);

private:
	void __loadDoc(const QString& id, bool isEditMode = false);
	void __loadNewDoc(const QString &id, const QString &content);
	void __updateDocToLocal(const QString &id, const QString &content);

private:
	/************************************************************************/
	/* 
	处理javascript的事件"getvalue"
	onEventNotify 中做消息分发
	*/
	/************************************************************************/
	void onGetValue(const QString &value, const QString &mback);

	/************************************************************************/
	/* 
	处理javascript的事件"changeMode"
	onEventNotify 中做消息分发
	*/
	/************************************************************************/
	void onChangeMode(const QString &mode);

private:
	//用于初始化的配置信息
	QString m_initData;

	//计时器ID
	int m_nTimerId;

	//当前文档的ID
	QString m_curDocId;
	//当前文档的原始数据，也就是最后一次修改本地数据库的数据
	QString m_srcDocData;

	//markdown当前的模式
	int m_editorMode;
	//markdown想要的模式
	int m_wantEditorMode;
};
