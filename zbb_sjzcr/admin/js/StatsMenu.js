(function(){
    StatsMenu = {
        //flash图表公共方法
        setFlashChart: function(data){
			var title = data.title;
			var xTitle = data.xTitle;
			var yTitle = data.yTitle;
			var arr = [];								
			arr.push('<anychart>');
            arr.push('  <settings><animation enabled="True"/>');
            arr.push('  </settings>');
            arr.push('	<charts>');
            arr.push('       <chart plot_type="Scatter">');
            arr.push('			<chart_settings>');
            arr.push('				<title enabled="true"><text>' + title + '</text></title>'); //设置头部标题
            arr.push('				<legend enabled="true" position="right" align="Near" ignore_auto_item="false">');
            arr.push('					<format><![CDATA[{%Icon}{%Name}]]></format>');
            arr.push('					<template></template>');
            arr.push('					<title enabled="true"><text>图例</text></title>');
            arr.push('					<columns_separator enabled="false"/>');
            arr.push('					<background><inside_margin left="10" right="10"/></background>');
            arr.push('					<items></items>');
            arr.push('				</legend>');
            arr.push('				<axes>');
			arr.push('					<x_axis tickmarks_placement="Center"><scale major_interval="2" minor_interval="1" /><title enabled="true"><text>' + xTitle + '</text></title><labels><format>{%Value}{numDecimals:0}</format></labels></x_axis>');//设置X轴标题		
            arr.push('					<y_axis><title enabled="true"><text>' + yTitle + '</text></title><labels><format>{%Value}{numDecimals:0}</format></labels></y_axis>'); //设置Y轴标题
            arr.push('				</axes>');
            arr.push('			</chart_settings>');
            arr.push('			<data_plot_settings default_series_type="Line">');
            arr.push('				<line_series point_padding="0.2" group_padding="1">');
            arr.push('					<tooltip_settings enabled="true">');
			arr.push('                      <format><![CDATA[Value: {%YValue}{numDecimals:0}]]></format>');           
            arr.push('						<background><border type="Solid" color="DarkColor(%Color)"/></background>');
            arr.push('						<font color="DarkColor(%Color)"/>');
            arr.push('					</tooltip_settings>');
            arr.push('					<marker_settings enabled="true"/>');
            arr.push('					<line_style><line thickness="3"/></line_style>');
            arr.push('				</line_series>');
            arr.push('			</data_plot_settings>');
            arr.push('			<data>');
			var sdata = data.data;
			if(sdata){
				for(var i in sdata){
					var pdata = sdata[i];
					if(pdata[0] && pdata[0].menu_name){
						arr.push('		<series name="'+pdata[0].menu_name+'使用人数">');
						for(var j in pdata){
							arr.push('		<point name="'+pdata[j].x+'" y="'+pdata[j].person+'"/>');
						}
						arr.push('		</series>');
						arr.push('		<series name="'+pdata[0].menu_name+'使用次数">');
						for(var k in pdata){
							arr.push('		<point name="'+pdata[k].x+'" y="'+pdata[k].counts+'"/>');
						}
						arr.push('		</series>');
					}
				}
			}			
			arr.push('			</data>');
            arr.push('		</chart>');
            arr.push('	</charts>');
            arr.push('</anychart>');
			jQuery("#"+data.id).css('height',"400px");
			chart = new AnyChart('./swf/AnyChart.swf', './swf/Preloader.swf');
			chart.width = "100%";
			chart.height = "100%";
			chart.setData(arr.join(""));
            chart.write(data.id);
            delete arr;
            arr = null;
            
        }
	};
})()