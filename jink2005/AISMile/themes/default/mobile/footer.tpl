{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

			<div id="footer">
				<div class="ui-grid-a">
					{hook h="displayMobileFooterChoice"}
				</div><!-- /grid-a -->

				<div id="full-site-section" class="center">
					<a href="{$link->getPageLink('index', true)}?no_mobile_theme" data-ajax="false">{l s='Browse the full site'}</a>
				</div>

				<div data-role="footer" data-theme="a" id="bar_footer">
					<div id="link_bar_footer" class="ui-grid-a">
						<div class="ui-block-a">
							<a href="{$link->getPageLink('index', true)}" data-ajax="false">{$PS_SHOP_NAME}</a>
						</div>
						{if $conditions}
						<div class="ui-block-b">
							<a href="{$link->getCMSLink($id_cgv)}" data-ajax="false">{l s='Terms of service'}</a>
						</div>
						{/if}
					</div>
				</div>
			</div><!-- /footer -->
		</div><!-- /page -->
	</body>
</html>
