{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- MODULE ReferralProgram -->
<p id="referralprogram">
	<img src="{$module_template_dir}referralprogram.gif" alt="{l s='Referral program' mod='referralprogram'}" class="icon" />
	{l s='You have earned a voucher worth %s thanks to your sponsor!' sprintf=$discount_display mod='referralprogram'}
	{l s='Enter voucher name %s to receive the reduction on this order.' sprintf=$discount->name mod='referralprogram'}
	<a href="{$link->getModuleLink('referralprogram', 'program', [], true)}" title="{l s='Referral program' mod='referralprogram'}">{l s='View your referral program.' mod='referralprogram'}</a>
</p>
<br />
<!-- END : MODULE ReferralProgram -->