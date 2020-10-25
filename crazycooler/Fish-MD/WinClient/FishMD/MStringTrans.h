/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:字符串编码格式的转换
*/
/************************************************************************/

#pragma once

/************************************************************************/
/* 
ansi 转 unicode
*/
/************************************************************************/
inline QString str2qstr(const std::string str)
{
	return QString::fromLocal8Bit(str.data());
}


/************************************************************************/
/* 
unicode 转 ansi
*/
/************************************************************************/
inline std::string qstr2str(const QString qstr)
{
	QByteArray cdata = qstr.toLocal8Bit();
	return std::string(cdata);
}