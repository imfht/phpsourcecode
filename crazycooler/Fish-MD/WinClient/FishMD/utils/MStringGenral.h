#pragma once

#include <string>
#include <vector>

namespace mstr
{
	std::string join(const std::string &interval, const std::vector<std::string> &vecStr);

	//is_any_one
	void split(std::vector<std::string> &result, const std::string input, const std::string &interval);


	void split_regex(std::vector<std::string> &result, const std::string input, const std::string &interval);

	void trim_left(std::string &str);
	std::string trim_left(const std::string &str);

	void trim_right(std::string &str);
	std::string trim_right(const std::string &str);

	void trim(std::string &str);
	std::string trim(const std::string &str);

	bool regex_match(const std::string &str, const std::string &expr);
}
