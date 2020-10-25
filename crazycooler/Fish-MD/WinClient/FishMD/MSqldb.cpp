/************************************************************************/
/*
Copyright 2017 wtulip Inc.
License GPL
Author:crazycooler
Description:sqliteµÄ²Ù×÷·â×°
*/
/************************************************************************/
#include "MSqldb.h"
#include "utils/MStringGenral.h"

MKVdb::MKVdb(sqlite3pp::database *db)
{
	m_db = db;
}

MKVdb::~MKVdb()
{

}

void MKVdb::set(const std::string &key, const std::string &value)
{
	sqlite3pp::command cmd(*m_db,
		"insert or replace into kv (key,value) values (?,?)");
	cmd.bind(1, key, sqlite3pp::nocopy);
	cmd.bind(2, value, sqlite3pp::nocopy);
	cmd.execute();
}

std::string MKVdb::getWithDefault(const std::string &key, const std::string &dft)
{
	std::string value;
	if (get(key, value))
		return value;
	else
		return dft;
}

bool MKVdb::get(const std::string &key, std::string &value)
{
	sqlite3pp::query qry(*m_db, "select value from kv where key=? limit 1");
	qry.bind(1, key,sqlite3pp::nocopy);
	
	auto it = qry.begin();
	if (it == qry.end())
		return false;

	value = (*it).get<char const *>(0);
	return true;
}

void MKVdb::del(const std::string &key)
{
	sqlite3pp::command cmd(*m_db,
		"delete from kv where key = ?");
	cmd.bind(1, key, sqlite3pp::nocopy);
	cmd.execute();
}

bool MKVdb::has(const std::string &key)
{
	sqlite3pp::query qry(*m_db, "select 1 from kv where key=? limit 1");
	qry.bind(1, key, sqlite3pp::nocopy);

	return qry.begin() != qry.end();
}
//////////////////////////////////////////////////////////////////////////
MDocdb::MDocdb(sqlite3pp::database *db)
{
	m_db = db;
}

MDocdb::~MDocdb()
{

}

void MDocdb::updateDocAfterUpload(const std::string &docId, const std::string &content, int version)
{
	sqlite3pp::command cmd(*m_db,
		"update doc set src_data=?,version=?,change_flag=0,create_flag=2 where id=?");
	cmd.bind(1, content, sqlite3pp::nocopy);
	cmd.bind(2, version);
	cmd.bind(3, docId, sqlite3pp::nocopy);
	cmd.execute();
}

void MDocdb::refreshChangeFlag()
{
	sqlite3pp::command cmd1(*m_db,
		"update doc set change_flag=1 where create_flag=2 and src_data != cur_data");
	cmd1.execute();

	sqlite3pp::command cmd2(*m_db,
		"update doc set create_flag=0 where create_flag=2");
	cmd2.execute();
}

void MDocdb::updateDocLocal(const std::string &docId, const std::string &curData)
{
	sqlite3pp::command cmd(*m_db,
		"update doc set cur_data=?,change_flag=1 where id = ?");
	cmd.bind(1, curData, sqlite3pp::nocopy);
	cmd.bind(2, docId, sqlite3pp::nocopy);
	cmd.execute();
}

void MDocdb::createDocLocal(const std::string &docId, const std::string &curData)
{
	sqlite3pp::command cmd(*m_db,
		"insert into doc (id,src_data,cur_data,version,change_flag,create_flag) values (?,?,?,?,?,?)");
	cmd.bind(1, docId, sqlite3pp::nocopy);
	cmd.bind(2, "", sqlite3pp::nocopy);
	cmd.bind(3, curData, sqlite3pp::nocopy);
	cmd.bind(4, int(0));
	cmd.bind(5, int(0));
	cmd.bind(6, int(1));
	cmd.execute();
}



void MDocdb::delDocsLocal(const std::vector<std::string> &docIds)
{
	std::string str = mstr::join("','",docIds);
	str = "('" + str + "')";

	std::string sql = "delete from doc where id in " + str;

	sqlite3pp::command cmd(*m_db,sql.c_str());
	cmd.execute();
}

void MDocdb::getChangeDocs(std::vector<MDocData> &docs)
{
	sqlite3pp::query qry(*m_db, "select id,src_data,cur_data,version,change_flag,create_flag from doc where change_flag = 1 or create_flag = 1");

	for (auto it = qry.begin(); it != qry.end(); ++it)
	{
		MDocData doc;

		doc.id = (*it).get<char const *>(0);
		doc.src_data = (*it).get<char const *>(1);
		doc.cur_data = (*it).get<char const *>(2);
		doc.version = (*it).get<int>(3);
		doc.change_flag = (*it).get<int>(4);
		doc.create_flag = (*it).get<int>(5);

		docs.push_back(doc);
	}
}

void MDocdb::resetChangeFlag(const std::string &docIds)
{
	sqlite3pp::command cmd(*m_db,
		"update doc set change_flag=? where id = ?");
	cmd.bind(1, int(0));
	cmd.bind(2, docIds, sqlite3pp::nocopy);
	cmd.execute();
}


bool MDocdb::getDoc(const std::string &docId, MDocData &data)
{
	sqlite3pp::query qry(*m_db, "select src_data,cur_data,version,change_flag,create_flag from doc where id=? limit 1");
	qry.bind(1, docId, sqlite3pp::nocopy);

	auto it = qry.begin();
	if (it == qry.end())
		return false;

	data.src_data = (*it).get<char const *>(0);
	data.cur_data = (*it).get<char const *>(1);
	data.version = (*it).get<int>(2);
	data.change_flag = (*it).get<int>(3);
	data.create_flag = (*it).get<int>(4);

	return true;
}

void MDocdb::setDoc(const MDocData &data)
{
	sqlite3pp::command cmd(*m_db,
		"insert or replace into doc (id,src_data,cur_data,version,change_flag,create_flag) values (?,?,?,?,?,?)");
	cmd.bind(1, data.id, sqlite3pp::nocopy);
	cmd.bind(2, data.src_data, sqlite3pp::nocopy);
	cmd.bind(3, data.cur_data, sqlite3pp::nocopy);
	cmd.bind(4, data.version);
	cmd.bind(5, data.change_flag);
	cmd.bind(6, data.create_flag);
	cmd.execute();
}

//////////////////////////////////////////////////////////////////////////
MDirDB::MDirDB(sqlite3pp::database *db)
{
	m_db = db;
}

MDirDB::~MDirDB()
{
	//
}

void MDirDB::updateDocAfterUpload(const std::string &dirId, const std::string &content, int version)
{
	sqlite3pp::command cmd(*m_db,
		"update dir set src_data=?,version=?,change_flag=0 where id = ?");
	cmd.bind(1, content,sqlite3pp::nocopy);
	cmd.bind(2, version);
	cmd.bind(3, dirId, sqlite3pp::nocopy);
	cmd.execute();
}

void MDirDB::refreshChangeFlag(const std::string &dirId)
{
	sqlite3pp::command cmd(*m_db,
		"update dir set change_flag=1 where id = ? and src_data != cur_data");
	cmd.bind(1, dirId, sqlite3pp::nocopy);
	cmd.execute();
}

bool MDirDB::getDir(const std::string &dirId, MDirData &data)
{
	sqlite3pp::query qry(*m_db, "select src_data,cur_data,version,change_flag from dir where id=? limit 1");
	qry.bind(1, dirId, sqlite3pp::nocopy);

	auto it = qry.begin();
	if (it == qry.end())
		return false;

	data.src_data = (*it).get<char const *>(0);
	data.cur_data = (*it).get<char const *>(1);
	data.version = (*it).get<int>(2);
	data.change_flag = (*it).get<int>(3);

	return true;
}

void MDirDB::setDir(const MDirData &data)
{
	sqlite3pp::command cmd(*m_db,
		"insert or replace into dir (id,src_data,cur_data,version,change_flag) values (?,?,?,?,?)");
	cmd.bind(1, data.id, sqlite3pp::nocopy);
	cmd.bind(2, data.src_data, sqlite3pp::nocopy);
	cmd.bind(3, data.cur_data, sqlite3pp::nocopy);
	cmd.bind(4, data.version);
	cmd.bind(5, data.change_flag);
	cmd.execute();
}

bool MDirDB::isChange(const std::string &dirId)
{
	sqlite3pp::query qry(*m_db, "select change_flag from dir where id=? limit 1");
	qry.bind(1, dirId, sqlite3pp::nocopy);

	auto it = qry.begin();
	if (it == qry.end())
		return false;

	return (*it).get<int>(0) == 1;
}

void MDirDB::resetChangeFlag(const std::string &dirId)
{
	sqlite3pp::command cmd(*m_db,
		"update dir set change_flag=? where id = ?");
	cmd.bind(1, int(0));
	cmd.bind(2, dirId, sqlite3pp::nocopy);
	cmd.execute();
}

void MDirDB::changeDirLocal(const std::string &dirId, const std::string &content)
{
	sqlite3pp::command cmd(*m_db,
		"update dir set cur_data=?,change_flag=? where id = ?");
	cmd.bind(1, content, sqlite3pp::nocopy);
	cmd.bind(2, int(1));
	cmd.bind(3, dirId, sqlite3pp::nocopy);
	cmd.execute();
}

//////////////////////////////////////////////////////////////////////////
MSqldb::MSqldb()
{
	m_kvdb = NULL;
	m_docdb = NULL;
	m_dirdb = NULL;
}


MSqldb::~MSqldb()
{
	if (m_kvdb)
	{
		delete m_kvdb;
		m_kvdb = NULL;
	}

	if (m_docdb)
	{
		delete m_docdb;
		m_docdb = NULL;
	}

	if (m_dirdb)
	{
		delete m_dirdb;
		m_dirdb = NULL;
	}
}

bool MSqldb::instance(const std::string &workPath)
{
	std::string dbPath = workPath + "/qmd.db";
	if (0 != m_db.connect(dbPath.c_str(), SQLITE_OPEN_READWRITE | SQLITE_OPEN_CREATE))
		return false;

	m_db.execute("CREATE TABLE IF NOT EXISTS 'doc' (\
		'id'  TEXT NOT NULL,\
		'src_data'  TEXT,\
		'cur_data' TEXT,\
		'version'  INTEGER DEFAULT 0,\
		'change_flag'  INTEGER DEFAULT 0,\
		'create_flag'  INTEGER DEFAULT 0,\
		PRIMARY KEY('id'))");
	m_db.execute("CREATE TABLE IF NOT EXISTS 'kv' (\
		'key'  TEXT NOT NULL,\
		'value'  TEXT,\
		PRIMARY KEY('key'))");
	m_db.execute("CREATE TABLE 'dir' (\
		'id'  TEXT NOT NULL,\
		'src_data'  TEXT,\
		'cur_data'  TEXT,\
		'version'  INTEGER DEFAULT 0,\
		'change_flag'  INTEGER DEFAULT 0,\
		PRIMARY KEY('id'))");
	
	m_kvdb = new MKVdb(&m_db);
	m_docdb = new MDocdb(&m_db);
	m_dirdb = new MDirDB(&m_db);

	return true;
}