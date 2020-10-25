{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<div id="statsContainer">
	<div id="calendar">
				<form action="{$current}&token={$token}{if $action && $table}&{$action}{$table}{/if}{if $identifier && $id}&{$identifier}={$id}{/if}" method="post" id="calendar_form" name="calendar_form">
					<input type="submit" name="submitDateDay" class="button submitDateDay" value="{$translations.Day}">
					<input type="submit" name="submitDateMonth" class="button submitDateMonth" value="{$translations.Month}">
					<input type="submit" name="submitDateYear" class="button submitDateYear" value="{$translations.Year}">
					<input type="submit" name="submitDateDayPrev" class="button submitDateDayPrev" value="{$translations.Day}-1">
					<input type="submit" name="submitDateMonthPrev" class="button submitDateMonthPrev" value="{$translations.Month}-1">
					<input type="submit" name="submitDateYearPrev" class="button submitDateYearPrev" value="{$translations.Year}-1">
					<p><span>{if isset($translations.From)}{$translations.From}{else}{l s='From:'}{/if}</span>
						<input type="text" name="datepickerFrom" id="datepickerFrom" value="{$datepickerFrom|escape}" class="datepicker">
					</p>
					<p><span>{if isset($translations.To)}{$translations.To}{else}<span>{l s='From:'}</span>{/if}</span>
						<input type="text" name="datepickerTo" id="datepickerTo" value="{$datepickerTo|escape}" class="datepicker">
					</p>
					<input type="submit" name="submitDatePicker" id="submitDatePicker" class="button" value="{if isset($translations.Save)}{$translations.Save}{else}{l s='Save'}{/if}" />
				</form>

				<script type="text/javascript">
					$(document).ready(function() {
						if ($("form#calendar_form .datepicker").length > 0)
							$("form#calendar_form .datepicker").datepicker({
								prevText: '',
								nextText: '',
								dateFormat: 'yy-mm-dd'
							});
					});
				</script>
	</div>