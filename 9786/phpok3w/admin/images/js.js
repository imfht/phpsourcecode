function InitSelect(Obj,ChannelId,ChkID)
{
	var cTmp = "";
	var j = 1;
	Obj.length = 0;
	Obj.options[Obj.length] = new Option("点这里选择分类", "");
	for(var i=0;i<Ok3w_ClassArr.length;i++)
	{
		if(Ok3w_ClassArr[i][0]==ChannelId)
		{
			Obj.options[Obj.length] = new Option(Ok3w_ClassArr[i][2], Ok3w_ClassArr[i][1]);
			if(Ok3w_ClassArr[i][1]==ChkID)
				Obj.selectedIndex = j;
			j = j + 1;
			
			if(i+1<Ok3w_ClassArr.length)
			{
				if(Ok3w_ClassArr[i+1][0]!=ChannelId)
					break;
			}
		}
	}
}