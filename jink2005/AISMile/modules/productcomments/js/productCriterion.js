/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function getProductCriterionForm()
{
	if (document.forms)
		return (document.forms['product_criterion_form']);
	else
		return (document.product_criterion_form);
}

function getProductCriterion(path, id_product, id_lang)
{
	$.get(path + 'productcommentscriterion.php', { id_product: id_product, id_lang: id_lang },
	function(data){
		document.getElementById('product_criterions').innerHTML = data;
	});
}
