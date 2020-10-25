/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:用QT封装的Http通信类
*/
/************************************************************************/

#pragma once
#include "MCommon.h"
#include <QObject>
#include <QtNetwork/QNetworkAccessManager>
#include <QtNetwork/QNetworkRequest>
#include <QtNetwork/QNetworkReply>
#include <map>
#include <functional>
#include <string>
#include <vector>



struct MRequestData;

/************************************************************************/
/* 
服务器应答数据
*/
/************************************************************************/
struct MResponseData
{
	//服务器范围的原始字节流
	QByteArray data;
	//解析为json后的
	QJsonObject root;
	//服务器返回的状态码
	int status;
	//请求时的数据包
	MRequestData *request;
	//其他数据，根据需要添加，一般是用来从request到response之间传递数据用
	void *other;
};

/************************************************************************/
/* 
请求服务器的数据
*/
/************************************************************************/
struct MRequestData
{
	//请求的url地址
	QString url;
	//请求的原始字节流
	QByteArray data;
	//当服务器应答后的回调函数
	std::function<void(int, const MResponseData &)> callback;
	//是否需要权限
	bool auth;
	//其他数据，根据需要添加，一般是用来从request到response之间传递数据用
	void *other;
};



class MHttp : public QObject
{
	Q_OBJECT

public:
	MHttp(QObject *parent=nullptr);
	~MHttp();

	/************************************************************************/
	/* 
	以下两个都是post请求函数
	@param url 地址
	@param data 请求的参数
	@param callback 当服务器应答时候的回调函数
	@param auth 是否需要权限
	@param other 其他，一般是用来从request到response之间传递数据用
	*/
	/************************************************************************/
	void post(const QString &url, 
		const QJsonObject &data, 
		std::function<void(int, const MResponseData &)> callback,
		bool auth = true,
		void *other = NULL
		);
	void post(const QString &url, 
		const QByteArray &data, 
		std::function<void(int, const MResponseData &)> callback,
		bool auth = true,
		void *other = NULL
		);

	/************************************************************************/
	/* 
	用来向服务器传输图片数据
	@param url 地址
	@param data 图片数据
	@param callback 当服务器应答时候的回调函数
	@param auth 是否需要权限
	*/
	/************************************************************************/
	void postImage(const QString &url, 
		const QByteArray &data, 
		std::function<void(int, const MResponseData &)> callback,
		bool auth = true);

	/************************************************************************/
	/* 
	设置token
	服务器通过token来控制用户权限，当用户登录时会获取一个token。当向服务器请求数据时，
	将token带上，服务器就会知道是谁在请求数据。token是有过期机制的
	*/
	/************************************************************************/
	void setToken(const QString &token);

private:
	/************************************************************************/
	/* 
	用于图像数据上传时的数据包装，熟悉前端的小伙伴应该知道multi part指的是什么，
	就是通常的form数据和image的binary数据打成一个数据包
	*/
	/************************************************************************/
	QByteArray makeMultipart(const QByteArray &boundary, const QByteArray &data, const QByteArray &md5);

	/************************************************************************/
	/* 
	刷新token时的回调函数
	由于token有过期机制，因此当token过期的时候就需要refresh一下
	*/
	/************************************************************************/
	void refreshTokenCallback(int status, const MResponseData &data);
private slots:
	/************************************************************************/
	/* 
	Http服务器所有的应答都会调用这个函数
	*/
	/************************************************************************/
	void replayFinished(QNetworkReply *reply);

private:
	QNetworkAccessManager *m_manager;
	//累加器，用来做request的ID
	int m_count;
	//存储所有那些没有被应答的请求
	std::map<int, MRequestData> m_cbMap;
	//token
	QString m_token;
	//是否正在等待token refresh，因为在refresh过程中不能做需要auth的操作
	bool m_waitTokenUpdate;
	//如果正在等待token refresh，则所有request都会放在这个容器中，等refresh完后再请求
	std::vector<MRequestData> m_waitList;
	//Http服务器的主机域名
	QString m_hostUrl;
};
