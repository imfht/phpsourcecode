{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

					<div style="clear:both;height:0;line-height:0">&nbsp;</div>
					</div>
					<div style="clear:both;height:0;line-height:0">&nbsp;</div>
				</div>
		{if $display_footer}
				{hook h="displayBackOfficeFooter"}
				<div id="footer">
					<div class="footerLeft">
						<a href="http://www.milebiz.com/" target="_blank">MileBiz&trade; {$ps_version}</a><br />
						<span>{l s='Load time: '}{number_format(microtime(true) - $timer_start, 3, '.', '')}s</span>
					</div>
					<div class="footerRight">
						{if $iso_is_fr}
							<span>Questions / Renseignements / Formations :</span> <strong>+33 (0)1.40.18.30.04</strong> de 09h &agrave; 18h
						{/if}
						|&nbsp;<a href="http://www.milebiz.com/contact_us/" target="_blank" class="footer_link">{l s='Contact'}</a>
						|&nbsp;<a href="http://forge.milebiz.com" target="_blank" class="footer_link">{l s='Bug Tracker'}</a>
						|&nbsp;<a href="http://bbs.milebiz.com" target="_blank" class="footer_link">{l s='Forum'}</a>	
					</div>
				</div>
			</div>
		</div>
		<div id="ajax_confirmation" style="display:none"></div>
		{* ajaxBox allows*}
		<div id="ajaxBox" style="display:none"></div>
		{/if}
		<div id="scrollTop"><a href="#top"></a></div>
	</body>
</html>
