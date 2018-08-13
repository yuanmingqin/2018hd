<?php
$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : ''; 
$allow_origin = array(  
    'http://dyxc.scms.sztv.com.cn',  
    'https://dyxc.scms.sztv.com.cn',  
    'http://static.scms.sztv.com.cn',  
    'https://static.scms.sztv.com.cn'  
);  
if(in_array($origin, $allow_origin)){  
    header('Access-Control-Allow-Origin:'.$origin);       
} 
require_once '../source/class/class_core.php';
$kldapp = C::app();
$kldapp->reject_robot();
$kldapp->init();
require_once 'function_API.php';
$result = array('errcode'=>0,'errmsg'=>'','is_xxcy'=>0);
//$prize_arr，id用来标识不同的奖项，min表示圆盘中各奖项区间对应的最小角度，max表示最大角度，如一等奖对应的最小角度：0，最大角度30，这里我们设置max值为1、max值为29，是为了避免抽奖后指针指向两个相邻奖项的中线。由于圆盘中设置了多个七等奖，所以我们在数组中设置每个七等奖对应的角度范围。prize表示奖项内容，v表示中奖几率，我们会发现，数组中七个奖项的v的总和为100，如果v的值为1，则代表中奖几率为1%，依此类推。
//$_COOKIE['openid'] ='ofyfs1IUUccteXQjJKpXHVa3pHA8';
$openid = $_COOKIE['openid'] ;
//$subscribe = $_COOKIE['subscribe'] = 1;
if(!$openid){
	$result['errcode'] = -1;
//}else if(!$subscribe || $subscribe != 1){
	//$result['errcode'] = -11;
}else{
	$isexist = C::t('user')->fetch_by_flag($openid);
	if(!$isexist){
		$result['errcode'] = -3;
	}else{
		$id = intval($_POST['id']);
		$zhuanfa = $_COOKIE['zhuanfa'];
		$date = date("Y-m-d");
		$count = C::t('zhongjiang')->check_is_chou($openid,$date,$id);
		if($count>=2 && !$zhuanfa){
			$result['errcode'] = -2;
		}else if($count>=3){
			$result['errcode'] = -22;
		}else{
			//活动ID
			$dat = array(
				'openid'=> $openid,
				'type'=> 2,
				'ip'=> $_G['clientip'],
				'zhongjiang_time'=>time(),
				'huodong_id'=>$id
			);
			$insert_id = C::t('zhongjiang')->insert($dat);
			$info = C::t('huodong')->fetch_by_id($id);
			$prize_arr = json_to_array(unserialize($info['msvalue']));
			$iszhongjiang  = C::t('zhongjiang')->check_is_zhongjiang($openid,$id);
			$Xxum = getXxcy($prize_arr);
			//$iszhongjiang = false;
			//判断是否中奖过
			if(($iszhongjiang && $Xxum >=0) || !$insert_id){
				$arr_order = $Xxum;
			}else{
				$arr = array();
				foreach ($prize_arr as $key => $val) {
					if($val['prize_num'] > 0){
						$arr[$key] = $val['prize_gailv'];
					}
				}
				$arr_order = getRand($arr); //根据概率获取奖项id 
			}
			$res = $prize_arr[$arr_order]; //中奖项 
			$min = $res['min']; 
			$max = $res['max'];
			$result['angle'] = mt_rand($min,$max);
			$result['result_src'] = $res['prize_resut_img'];		
			if($res['is_xxcy']== '1'){ //谢谢参与奖 
				$result['is_xxcy'] = 1;
				//addIntegral($_COOKIE['mobile']);
				$result['zhongjiang_id'] = '';				
			}else{ 
				$up = array(
					'prize_name'=> $res['prize_name']
				);
				C::t('zhongjiang')->update($up,array('id'=>$insert_id));
				if($res['prize_num']>0){
					$res['prize_num']--;
					$prize_arr[$arr_order]['prize_num'] = $res['prize_num'];
					$r_arr = array(
						'msvalue'=> serialize(json_encode($prize_arr))
					);
					C::t('huodong')->update($r_arr,array('id'=>$id));
				}
				$result['prize_name'] = $res['prize_name'];
				$result['zhongjiang_id'] = $insert_id;			
			}
		}
	}	
}
echo json_encode($result);
function getXxcy($prize_arr){
	foreach($prize_arr as $key=>$v){
		if($v['is_xxcy'] == 1){
			return $key;
		}
	}
	return false;
} 
function getRand($proArr) { 
    $result = ''; 
 
    //概率数组的总概率精度 
    $proSum = array_sum($proArr); 
 
    //概率数组循环 
    foreach ($proArr as $key => $proCur) { 
        $randNum = mt_rand(1, $proSum); 
        if ($randNum <= $proCur) { 
            $result = $key; 
            break; 
        } else { 
            $proSum -= $proCur; 
        } 
    } 
    unset ($proArr); 
 
    return $result; 
}  
?>