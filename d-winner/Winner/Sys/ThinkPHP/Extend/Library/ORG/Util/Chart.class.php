<?php
/**
 *+--------------------------------------------------
 *          图表管理类
 *+--------------------------------------------------
 * 
 */

class Chart extends Think{
 /**
  * @@生成三维饼图
  * @param string $title
  * @param array $data
  * @param int $size
  * @param int $height
  * @param int $width
  * @param array $legend
  */
  static function create3dpie($title,$data=array(),$size=40,$height=100,$width=80,$legend=array(),$slice=0){
      //包含相关的文件
  	  vendor("Jpgraph.jpgraph");
      vendor("Jpgraph.jpgraph_pie");
      vendor("Jpgraph.jpgraph_pie3d");
      // 创建图表
	  $graph = new PieGraph($width,$height,"auto");
	  $graph->SetShadow();
	  // 设置标题
	  $graph->title->Set(iconv("utf-8","gb2312",$title));
	  //$graph->title->SetFont(FF_VERDANA,FS_BOLD,18); 
	  $graph->title->SetFont(FF_SIMSUN,FS_BOLD,18); 
	  $graph->title->SetColor("darkblue");
	  $graph->legend->Pos(0.1,0.2);
	  $graph->SetFrame(false,'#ffffff',0);//去掉周围的边框
	  // Create 3D pie plot
	  $p1 = new PiePlot3d($data);
	  $p1->SetTheme("sand");
	  $p1->SetCenter(0.4);
	  $p1->SetSize($size);
	  // Adjust projection angle
	  $p1->SetAngle(70);
	  // As a shortcut you can easily explode one numbered slice with
	  if($slice==0)
	     $p1->ExplodeSlice(3);
	  // Setup the slice values
	  $p1->value->SetFont(FF_ARIAL,FS_BOLD,10);
	  //$p1->value->SetFont(FF_SIMSUN,FS_BOLD,11);
	  $p1->value->SetColor("navy"); 
	  $graph->legend->SetFont(FF_SIMSUN,FS_BOLD,8);
      //编码转化
	  foreach ($legend as $k => $v) {
          $legend[$k] = iconv('utf-8', 'gb2312', $v);
      }
	  $p1->SetLegends($legend);
	  $graph->Add($p1);
	  $graph->Stroke();
  }	
 
  /**
   * 设置柱状图
   */
  static function createcolumnar($title,$data=array(),$size=40,$height=100,$width=80,$legend=array()){
  	    //载入文件
		vendor("Jpgraph.jpgraph");
		vendor("Jpgraph.jpgraph_bar");
		// Some data
		// Create the graph and setup the basic parameters 
		$graph = new Graph($width,$height,'auto');	
		$graph->img->SetMargin(40,30,40,40);
		$graph->SetScale("textint");
		$graph->SetShadow();
		$graph->SetFrame(false); // No border around the graph
		
		// Add some grace to the top so that the scale doesn't
		// end exactly at the max value. 
		$graph->yaxis->scale->SetGrace(20);
		
		// Setup X-axis labels
        //编码转化
	    foreach ($legend as $k => $v) {
            $legend[$k] = iconv('utf-8', 'gb2312', $v);
        }
		$graph->xaxis->SetTickLabels($legend);
		$graph->xaxis->SetFont(FF_SIMSUN);
		
		// Setup graph title ands fonts
		$graph->title->Set(iconv("utf-8","gb2312",$title));
		$graph->title->SetFont(FF_SIMSUN,FS_BOLD,11);
		//$graph->xaxis->title->Set("Year 2002");
		$graph->xaxis->title->SetFont(FF_FONT2,FS_BOLD);
		                              
		// Create a bar pot
		$bplot = new BarPlot($data);
		$bplot->SetFillColor("#0080C0");
		$bplot->SetWidth(0.3);
		//$bplot->SetShadow();
		
		// Setup the values that are displayed on top of each bar
		$bplot->value->Show();
		// Must use TTF fonts if we want text at an arbitrary angle
		$bplot->value->SetFont(FF_ARIAL,FS_BOLD);
		$bplot->value->SetAngle(0);
		// Black color for positive values and darkred for negative values
		$bplot->value->SetColor("black","darkred");
		$graph->Add($bplot);
		
		// Finally stroke the graph
		$graph->Stroke();
  }
  
 /**
  * 环形图
  */
  function createring($title,$data=array(),$size=40,$height=100,$width=80,$legend=array()){
    // Example of pie with center circle
    vendor("Jpgraph.jpgraph");
    vendor("Jpgraph.jpgraph_pie");
    // Some data
    //$data = array(50,28,25,27,30,30);

	// A new pie graph
	$graph = new PieGraph(700,350,'auto');
	//$graph->SetShadow();
	// Setup title
	$graph->title->Set(iconv("utf-8","gb2312","{$title}"));
	$graph->title->SetFont(FF_SIMSUN,FS_BOLD,14);
	$graph->title->SetMargin(2); // Add a little bit more margin from the top
	$graph->legend->Pos(0.1,0.1);
	$graph->SetFrame(false,'#ffffff',0);//去掉周围的边框
	// Create the pie plot
	$p1 = new PiePlotC($data);
	
	// Set size of pie
	$p1->SetTheme("sand");
	$p1->SetCenter(0.4);
	$p1->SetSize(0.35);
	
	// Label font and color setup
	$p1->value->SetFont(FF_ARIAL,FS_BOLD,10);
	$p1->value->SetColor('black');
	// Setup the title on the center circle
	$p1->midtitle->Set("");
	$p1->midtitle->SetFont(FF_SIMSUN,FS_NORMAL,10);
	// Set color for mid circle
	$p1->SetMidColor('white');
	// Use percentage values in the legends values (This is also the default)
	$p1->SetLabelType(PIE_VALUE_PER);
	$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL,8);
    //编码转化
    foreach ($legend as $k => $v) {
       $legend[$k] = iconv('utf-8', 'gb2312', $v);
    }
		  $p1->SetLegends($legend);
	// Add plot to pie graph
	$graph->Add($p1);
	
	// .. and send the image on it's marry way to the browser
	$graph->Stroke();
  }
  
  /**
   * 线图
   */
  function createmonthline($title,$data=array(),$size=40,$height=100,$width=80,$legend=array()){
    vendor("Jpgraph.jpgraph");
    vendor("Jpgraph.jpgraph_line");

	$labels = $legend;
	//编码转化
	foreach ($labels as $k => $v) {
	   $labels[$k] = iconv('utf-8', 'gb2312', $v);
	}
	$data = $data;
	$graph = new Graph($width,$height,"auto");
	$graph->img->SetMargin(40,40,40,40);    
	$graph->img->SetAntiAliasing();
	$graph->SetScale("textlin");
	$graph->SetShadow();
	$graph->title->Set(iconv('utf-8', 'gb2312',"{$title}"));
	$graph->title->SetFont(FF_SIMSUN,FS_NORMAL,14);
	$graph->SetFrame(false,'#ffffff',0);//去掉周围的边框
	$graph->xaxis->SetFont(FF_SIMSUN,FS_NORMAL,9);
	$graph->xaxis->SetTickLabels($labels);
	$graph->xaxis->SetLabelAngle(0);
	
	$p1 = new LinePlot($data);
	$p1->mark->SetType(MARK_FILLEDCIRCLE);
	$p1->mark->SetFillColor("#0080C0");
	$p1->mark->SetWidth(4);
	$p1->SetColor("#000000");
	$p1->SetCenter();
	$graph->Add($p1);
	
	$graph->Stroke(); 
  }
  /**
   * 横柱图
   * 
   */
  function createhorizoncolumnar($title,$subtitle,$data=array(),$size=40,$height=100,$width=80,$legend=array()){
  	vendor("Jpgraph.jpgraph");
	vendor("Jpgraph.jpgraph_bar");
	$datay = $data;
	$datax = $legend;
    //编码转化
	foreach ($datax as $k => $v) {
	   $datax[$k] = iconv('utf-8', 'gb2312', $v);
	}
	// Size of graph
	$count = count($datay);
	$addheight = 0;
	if($count>10){
		$addheight = ($count-10)*20;
	}
	$height=$height+$addheight;
	
	// Set the basic parameters of the graph 
	$graph = new Graph($width,$height,'auto');
	$graph->SetScale("textlin");
	
	// No frame around the image
	$graph->SetFrame(false);
	$graph->SetFrame(false,'#ffffff',0);//去掉周围的边框
	
	// Rotate graph 90 degrees and set margin
	$graph->Set90AndMargin(70,10,50,30);
	
	// Set white margin color
	$graph->SetMarginColor('white');
	
	// Use a box around the plot area
	$graph->SetBox();
	
	// Use a gradient to fill the plot area
	$graph->SetBackgroundGradient('white','white',GRAD_HOR,BGRAD_PLOT);
	
	// Setup title
	$graph->title->Set(iconv('utf-8', 'gb2312',"{$title}"));
	$graph->title->SetFont(FF_SIMSUN,FS_BOLD,12);
	$graph->subtitle->Set("(".iconv('utf-8', 'gb2312',$subtitle).")");
	$graph->subtitle->SetFont(FF_SIMSUN,FS_NORMAL,10);
	// Setup X-axis
	$graph->xaxis->SetTickLabels($datax);
	$graph->xaxis->SetFont(FF_SIMSUN,FS_NORMAL,10);
	
	// Some extra margin looks nicer
	$graph->xaxis->SetLabelMargin(10);
	
	// Label align for X-axis
	$graph->xaxis->SetLabelAlign('right','center');
	
	// Add some grace to y-axis so the bars doesn't go
	// all the way to the end of the plot area
	$graph->yaxis->scale->SetGrace(10);
	
	// We don't want to display Y-axis
	$graph->yaxis->Hide();
	
	// Now create a bar pot
	$bplot = new BarPlot($datay);
	//$bplot->SetShadow();
	
	//You can change the width of the bars if you like
	//$bplot->SetWidth(0.5);
	
	// Set gradient fill for bars
	$bplot->SetFillGradient('blue','#0080C0',GRAD_HOR);
	
	// We want to display the value of each bar at the top
	$bplot->value->Show();
	$bplot->value->SetFont(FF_ARIAL,FS_NORMAL,7);
	$bplot->value->SetAlign('left','center');
	$bplot->value->SetColor("black");
	$bplot->value->SetFormat('%.0f');
	//$bplot->SetValuePos('max');
	
	// Add the bar to the graph
	$graph->Add($bplot);
	
	// Add some explanation text
	$txt = new Text('');
	$txt->SetPos(130,399,'center','bottom');
	$txt->SetFont(FF_COMIC,FS_NORMAL,8);
	$graph->Add($txt);
	
	// .. and stroke the graph
	$graph->Stroke();
  }
}