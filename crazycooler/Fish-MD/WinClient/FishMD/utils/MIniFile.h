#pragma once
#include <boost/property_tree/ptree.hpp>
#include <string>

class MIniFile
{
public:
	MIniFile();
	MIniFile(const std::string &path);
	~MIniFile();
	

public:
	bool load(const std::string& path);
	bool save(const std::string& path = "");

	template<class Type>
	Type get(const std::string &key) const;

	template<class Type>
	Type get(const std::string &key,
		const Type &default_value) const;

	template<class Type>
	void put(const std::string &key,const Type &value);

	bool del(const std::string &key);

	void clear() { m_data.clear(); }

	bool has(const std::string &key);

	std::string toJson();


protected:
	boost::property_tree::ptree m_data;
	std::string m_path;
};


template<class Type>
Type MIniFile::get(const std::string &key) const
{
	return m_data.get<Type>(key);
}

template<class Type>
Type MIniFile::get(const std::string &key,
	const Type &default_value) const
{
	return m_data.get<Type>(key, default_value);
}

template<class Type>
void MIniFile::put(const std::string &key,const Type &value)
{
	m_data.put(key,value);
}
