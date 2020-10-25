<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

// The usage of this file is deprecated !!!
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/ProductCommentCriterion.php');
include_once(dirname(__FILE__).'/ProductComment.php');
include_once(dirname(__FILE__).'/productcomments.php');

$productCom = new productcomments();

if (Tools::getValue('action') && Tools::getValue('id_product_comment') && Context::getContext()->cookie->id_customer)
{
	if (Tools::getValue('action') == 'report')
	{
		if (!ProductComment::isAlreadyReport(Tools::getValue('id_product_comment'), Context::getContext()->cookie->id_customer) &&
			ProductComment::reportComment((int)Tools::getValue('id_product_comment'), (int)Context::getContext()->cookie->id_customer))
			die('0');
	}
	else if (Tools::getValue('action') == 'usefulness' && Tools::getValue('value') && Tools::getValue('value'))
	{
		if (!ProductComment::isAlreadyUsefulness(Tools::getValue('id_product_comment'), Context::getContext()->cookie->id_customer) &&
			ProductComment::setCommentUsefulness((int)Tools::getValue('id_product_comment'),
												 (bool)Tools::getValue('value'),
												 Context::getContext()->cookie->id_customer))
			die('0');
	}
}
else if (Tools::getValue('action') && Tools::getValue('secure_key') == $productCom->secure_key)
{
		$review = Tools::jsonDecode(Tools::getValue('review'));
		$id_product = 0;
		$content = null;
		$title = null;
		$name = null;
		$grades = array();
		foreach ($review as $entry)
		{
			if ($entry->key == 'id_product')
				$id_product = $entry->value;
			else if ($entry->key == 'title')
				$title = $entry->value;
			else if ($entry->key == 'content')
				$content = $entry->value;
			else if ($entry->key == 'customer_name')
				$name = $entry->value;
			else if (strstr($entry->key, 'grade'))
			{
				$id = array(explode('_', $entry->key));
				$grades[] = array('id' => $id['0']['0'], 'grade' => $entry->value);
			}
		}

		if ($title == '' || $content == '' || !$id_product || count($grades) == 0)
			die('0');

		$allow_guests = (int)Configuration::get('PRODUCT_COMMENTS_ALLOW_GUESTS');

		if (Context::getContext()->customer->id || (!Context::getContext()->customer->id && $allow_guests))
		{
			$id_guest = (!$id_customer = (int)Context::getContext()->cookie->id_customer) ? (int)Context::getContext()->cookie->id_guest : false;
			$customerComment = ProductComment::getByCustomer((int)($id_product), Context::getContext()->cookie->id_customer, true, (int)$id_guest);

			if (!$customerComment || ($customerComment && (strtotime($customerComment['date_add']) + Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME')) < time()))
			{
				$errors = array();
				$customer_name = false;
				if ($id_guest && (!$customer_name = Context::getContext()->customer->firstname.' '.Context::getContext()->customer->lastname))
					$errors[] = $productCom->l('Please fill your name');

				if (!count($errors) && $content)
				{
					$comment = new ProductComment();
					$comment->content = strip_tags($content);
					$comment->id_product = (int)$id_product;
					$comment->id_customer = (int)Context::getContext()->cookie->id_customer;
					$comment->id_guest = (int)$id_guest;
					$comment->customer_name = pSQL($customer_name);
					if (!$comment->id_customer)
						$comment->customer_name = pSQL($name);
					$comment->title = pSQL($title);
					$comment->grade = 0;
					$comment->validate = 0;


					$tgrade = 0;
					$comment->save();
					foreach ($grades as $grade)
					{
						$tgrade += $grade['grade'];
						$productCommentCriterion = new ProductCommentCriterion((int)Tools::getValue('id_product_comment_criterion_'.$grade['id']));
						if ($productCommentCriterion->id)
							$productCommentCriterion->addGrade($comment->id, $grade['grade']);
					}

					if ((count($grades) - 1) >= 0)
						$comment->grade = (int)($tgrade / ((int)count($grades)));

					if (!$comment->save())
						$errors[] = $productCom->l('An error occurred while saving your comment.');
					else
						Context::getContext()->smarty->assign('confirmation', $productCom->l('Comment posted.').((int)(Configuration::get('PRODUCT_COMMENTS_MODERATE')) ? ' '.$productCom->l('Awaiting moderator validation.') : ''));

				}
				else
					$errors[] = $productCom->l('Comment text is required.');
			}
			else
				$errors[] = sprintf($productCom->l('You should wait %d seconds before posting a new comment.'), Configuration::get('PRODUCT_COMMENTS_MINIMAL_TIME'));
		}
}

die('1');

