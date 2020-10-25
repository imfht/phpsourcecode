#pragma warning(disable:4996)
#include "utils/MStringGenral.h"
#include <sstream>

#include <boost/algorithm/string.hpp>
#include <boost/algorithm/string/regex.hpp>



namespace mstr
{
	std::string join(const std::string &interval, const std::vector<std::string> &vecStr)
	{
		std::stringstream ss;
		for (uint32_t i = 0; i < vecStr.size(); i++)
		{
			if (i != 0)
				ss << interval;
			ss << vecStr[i];
		}
		return ss.str();
	}

	void split(std::vector<std::string> &result, const std::string input, const std::string &interval)
	{
		boost::algorithm::split(result, input, boost::algorithm::is_any_of(interval));
	}

	void split_regex(std::vector<std::string> &result, const std::string input, const std::string &interval)
	{
		using boost::regex;
		//boost::regex pat(interval, boost::regex::perl);
		boost::regex pat(interval);
		boost::algorithm::split_regex(result, input, pat);
	}

	void trim_left(std::string &str)
	{
		boost::algorithm::trim_left(str);
	}

	std::string trim_left(const std::string &str)
	{
		return boost::algorithm::trim_left_copy(str);
	}

	void trim_right(std::string &str)
	{
		boost::algorithm::trim_right(str);
	}

	std::string trim_right(const std::string &str)
	{
		return boost::algorithm::trim_right_copy(str);
	}

	void trim(std::string &str)
	{
		boost::algorithm::trim(str);
	}

	std::string trim(const std::string &str)
	{
		return boost::algorithm::trim_copy(str);
	}

	bool regex_match(const std::string &str, const std::string &expr)
	{
		boost::regex regExpr(expr);
		return boost::regex_match(str, regExpr);
	}
}
