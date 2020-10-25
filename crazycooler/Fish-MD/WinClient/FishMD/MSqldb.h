/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:sqlite的操作封装
*/
/************************************************************************/

#pragma once

#include <string>
#include <vector>
#include "sqlite3pp.h"
#include <boost/lexical_cast.hpp>

class MSqldb;

/************************************************************************/
/* 
key-value 数据库
*/
/************************************************************************/
class MKVdb
{
public:
	MKVdb(sqlite3pp::database *db);
	~MKVdb();

	void set(const std::string &key,const std::string &value);
	std::string getWithDefault(const std::string &key, const std::string &dft = "");
	bool get(const std::string &key, std::string &value);
	void del(const std::string &key);
	bool has(const std::string &key);

	template<typename T>
	void setT(const std::string &key, const T &value)
	{
		std::stringstream ss;
		ss << value;
		set(key, ss.str());
	}

	template<typename T>
	T getT(const std::string &key, const T &value)
	{
		std::string s;
		if (get(key, s))
		{
			return boost::lexical_cast<T>(s);
		}
		else
		{
			return value;
		} 
	}

private:
	sqlite3pp::database *m_db;
};

/************************************************************************/
/*
doc数据库中数据结构
*/
/************************************************************************/
struct MDocData 
{
	std::string id;
	std::string src_data;
	std::string cur_data;
	int version;
	int change_flag;
	int create_flag;
};

/************************************************************************/
/*
dir数据库中数据结构
*/
/************************************************************************/
struct MDirData
{
	std::string id;
	std::string src_data;
	std::string cur_data;
	int version;
	int change_flag;
};

/************************************************************************/
/*
doc数据库操作封装
用来在本地对doc做缓存
*/
/************************************************************************/
class MDocdb
{
public:
	MDocdb(sqlite3pp::database *db);
	~MDocdb();
	/************************************************************************/
	/* 
	创建本地doc，这时候的doc没有和服务器同步
	@param docId	文档ID
	@param curData	文档内容
	*/
	/************************************************************************/
	void createDocLocal(const std::string &docId, const std::string &curData);

	/************************************************************************/
	/* 
	删除本地文档
	@param docIds	文档IDs
	*/
	/************************************************************************/
	void delDocsLocal(const std::vector<std::string> &docIds);

	/************************************************************************/
	/* 
	更新本地文档，这是doc还没有和服务器同步
	@param docID	文档ID
	@param curData	文档内容
	*/
	/************************************************************************/
	void updateDocLocal(const std::string &docId, const std::string &curData);

	/************************************************************************/
	/* 
	将修改标志清零
	@param docIds 文档IDs
	*/
	/************************************************************************/
	void resetChangeFlag(const std::string &docIds);

	/************************************************************************/
	/* 
	获取被修改，但没有同步到服务器的文档
	@param docs 引用返回被修改的文档
	*/
	/************************************************************************/
	void getChangeDocs(std::vector<MDocData> &docs);

	/************************************************************************/
	/* 
	被修改的doc同步到服务器后，重新设置该doc
	因为doc有src_data和cur_data两个，
	src_data一般记录服务器最后一次更新的数据，
	cur_data为本地修改过后的缓存值
	@param docId 文档ID
	@param content 文档内容
	@param version 文档版本
	*/
	/************************************************************************/
	void updateDocAfterUpload(const std::string &docId, const std::string &content, int version);

	/************************************************************************/
	/*
	将内容修改了的doc，修改标志重新设置为1
	*/
	/************************************************************************/
	void refreshChangeFlag();

	/************************************************************************/
	/* 
	获取doc值
	@param docId 文档ID
	@param data  文档数据
	*/
	/************************************************************************/
	bool getDoc(const std::string &docId, MDocData &data);

	/************************************************************************/
	/* 
	设置doc值
	@param data 文档数据
	*/
	/************************************************************************/
	void setDoc(const MDocData &data);

private:
	sqlite3pp::database *m_db;
};


/************************************************************************/
/* 
dir（目录）数据库操作封装
用来在本地对dir做缓存
*/
/************************************************************************/
class MDirDB
{
public:
	MDirDB(sqlite3pp::database *db);
	~MDirDB();
	/************************************************************************/
	/* 
	修改本地的dir，还没有同步到服务器
	*/
	/************************************************************************/
	void changeDirLocal(const std::string &dirId, const std::string &content);

	/************************************************************************/
	/* 
	将修改标志清零
	@param dirId 目录ID
	*/
	/************************************************************************/
 	void resetChangeFlag(const std::string &dirId);


	/************************************************************************/
	/*
	目录是否被修改
	@param dirId 目录ID
	*/
	/************************************************************************/
	bool isChange(const std::string &dirId);

	/************************************************************************/
	/*
	被修改的dir同步到服务器后，重新设置该dir
	因为dir有src_data和cur_data两个，
	src_data一般记录服务器最后一次更新的数据，
	cur_data为本地修改过后的缓存值
	@param dirId 目录ID
	@param content 目录内容
	@param version 目录版本
	*/
	/************************************************************************/
	void updateDocAfterUpload(const std::string &dirId, const std::string &content, int version);

	/************************************************************************/
	/* 
	将内容修改了的dir，修改标志重新设置为1
	@param dirId 目录ID
	*/
	/************************************************************************/
	void refreshChangeFlag(const std::string &dirId);

	/************************************************************************/
	/* 
	获取dir数据
	@param dirId 目录ID
	@param data 目录数据
	*/
	/************************************************************************/
	bool getDir(const std::string &dirId, MDirData &data);

	/************************************************************************/
	/*
	设置dir数据
	@param data 目录数据
	*/
	/************************************************************************/
	void setDir(const MDirData &data);

private:
	sqlite3pp::database *m_db;
};

class MSqldb
{
public:
	MSqldb();
	~MSqldb();

	bool instance(const std::string &workPath);

	MKVdb *getKVDB() { return m_kvdb; }
	MDocdb *getDocDB() { return m_docdb; }
	MDirDB *getDirDB() { return m_dirdb; }
private:
	MKVdb *m_kvdb;
	MDocdb *m_docdb;
	MDirDB *m_dirdb;

	sqlite3pp::database m_db;
};

