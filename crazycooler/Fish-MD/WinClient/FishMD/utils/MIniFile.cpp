#include "utils/MIniFile.h"
#include <fstream>
#include <iostream>
#include <boost/property_tree/ini_parser.hpp>
#include <boost/property_tree/json_parser.hpp>

using namespace std;



MIniFile::MIniFile()
{
}

MIniFile::MIniFile(const std::string &path)
{
	boost::property_tree::ini_parser::read_ini(path, m_data);
	m_path = path;
}

MIniFile::~MIniFile()
{
}


bool MIniFile::load(const std::string& path)
{
	try
	{
		boost::property_tree::ini_parser::read_ini(path, m_data);
	}
	catch (std::exception& e)
	{
		cout << e.what() << endl;
		return false;
	}

	m_path = path;
	return true;
	
	
}

bool MIniFile::save(const std::string& path)
{
	m_path = path.empty() ? m_path : path;
	if (m_path.empty())
		return false;

	try
	{
		boost::property_tree::ini_parser::write_ini(m_path, m_data);
	}
	catch (std::exception& e)
	{
		cout << e.what() << endl;
		return false;
	}

	return true;
}

bool MIniFile::del(const std::string &key)
{
	try
	{
		m_data.erase(key);
	}
	catch (std::exception& e)
	{
		cout << e.what() << endl;
		return false;
	}

	return true;
	
}

bool MIniFile::has(const std::string &key)
{
	try
	{
		m_data.get_child(key);
		return true;
	}
	catch (std::exception&)
	{
		return false;
	}

	
}

std::string MIniFile::toJson()
{
	stringstream ss;
	boost::property_tree::json_parser::write_json(ss, m_data, false);
	return ss.str();
}
