<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

/**
* 删除注释
* 参数 字符串 $output 被处理的字符串
* 返回 $output 被处后的字符串
**/
function remove_comments(&$output)
{
	$lines = explode("\n", $output);
	$output = "";
	$linecount = count($lines);

	$in_comment = false;
	for($i = 0; $i < $linecount; $i++)
	{
		if( preg_match("/^\/\*/", preg_quote($lines[$i])) )
		{
			$in_comment = true;
		}

		if( !$in_comment )
		{
			$output .= $lines[$i] . "\n";
		}

		if( preg_match("/\*\/$/", preg_quote($lines[$i])) )
		{
			$in_comment = false;
		}
	}

	unset($lines);
	return $output;
}

/**
* 删除注释
* 参数 字符串 $sql 被处理的 SQL 语句
* 返回 $output 被处理后的字符串
**/
function remove_remarks($sql)
{
	//分割sql
	$lines = explode("\n", $sql);

	$sql = "";
	//计算sql条数
	$linecount = count($lines);
	$output = "";

	for ($i = 0; $i < $linecount; $i++)
	{
		
		if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
		{
			//if ($lines[$i][0] != "#")
			if ( substr($lines[$i], 0, 1) != '#' ) 
			{
				$output .= $lines[$i] . "\n";
			}
			else
			{
				$output .= "\n";
			}
			$lines[$i] = "";
		}
	}
	
	return $output;
	
}

/**
* 将SQL语句进行分隔
* 参数 字符串 $sql 被处理的SQL
* 参数 字符串 $delimiter SQL分界符 
* 返回 处理后的SQL语句
**/
function split_sql_file($sql, $delimiter)
{
	$tokens = explode($delimiter, $sql);

	$sql = "";
	$output = array();

	$matches = array();

	$token_count = count($tokens);
	for ($i = 0; $i < $token_count; $i++)
	{
		if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
		{
			$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
			$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
			
			$unescaped_quotes = $total_quotes - $escaped_quotes;

			if (($unescaped_quotes % 2) == 0)
			{
				$output[] = $tokens[$i];
				$tokens[$i] = "";
			}
			else
			{
				$temp = $tokens[$i] . $delimiter;
				$tokens[$i] = "";

				$complete_stmt = false;
				
				for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
				{
					$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
					$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
			
					$unescaped_quotes = $total_quotes - $escaped_quotes;
					
					if (($unescaped_quotes % 2) == 1)
					{
						$output[] = $temp . $tokens[$j];

						$tokens[$j] = "";
						$temp = "";

						$complete_stmt = true;
						$i = $j;
					}
					else
					{
						$temp .= $tokens[$j] . $delimiter;
						$tokens[$j] = "";
					}
					
				} 
			} 
		}
	}

	return $output;
}

?>