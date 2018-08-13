<?php
/**
 *    秘密信息
 *    
 *    后台配置
 */
global $config;

//菜单
$config['menu'] = array(
	array(
		'title' => '管理员',
		'child' => array(
			array(
				'action' => 'member',
				'title' => '权限管理',
				'url' => 'index.php?action=member'
			),
			array(
				'action' => 'adminlog',
				'title' => '管理员日志',
				'url' => 'index.php?action=adminlog'
			)
		)
	)
	,
	array(
		'title' => '投票管理',
		'child' => array(
			array(
				'action' => 'player',
				'title' => '电视主持人',
				'url' => 'index.php?action=player'
			),
			array(
				'action' => 'player_br',
				'title' => '广播主持人',
				'url' => 'index.php?action=player_br'
			)
		)
	)
);
$config['user_group'] = array(
	"1"=>"电视主持人",
	"2"=>"广播主持人"
);
//允许访问的IP范围
$config['allow_ip'] = array();
?>