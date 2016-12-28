<?php

namespace Admin\Controller;
use Common\Controller\CoreController;

class FilesUpdataController extends CoreController {
	// 编辑器上传配置
	public function upload() {
		$uid = I ( 'post.uid' ); // 上传标识
		if (empty ( $uid )) {
			$return ['error'] = 1;
			$return ['message'] = '没有登录不允许上传';
			exit ( json_encode ( $return ) );
		}
		/* 返回标准数据 */
		$return = array (
				'error' => 0 
		);
		$dir = I ( 'get.dir' ); // 上传类型image、flash、media、file
		                     // 上传配置
		$ext_arr = array (
				'image' => array (
						'gif',
						'jpg',
						'jpeg',
						'png',
						'bmp' 
				),
				'flash' => array (
						'swf',
						'flv' 
				),
				'media' => array (
						'swf',
						'flv',
						'mp3',
						'wav',
						'wma',
						'wmv',
						'mid',
						'avi',
						'mpg',
						'asf',
						'rm',
						'rmvb' 
				),
				'file' => array (
						'doc',
						'docx',
						'xls',
						'xlsx',
						'ppt',
						'htm',
						'html',
						'txt',
						'zip',
						'rar',
						'gz',
						'bz2' 
				) 
		);
		$upload = new \Think\Upload (); // 实例化上传类
		$upload->maxSize = 3145728; // 设置附件上传大小
		$upload->exts = $ext_arr [$dir]; // 设置附件上传类型
		$upload->rootPath = './Uploads/';
		$upload->savePath = $uid . '/' . $dir . '/';
		$info = $upload->uploadOne ( $_FILES ['imgFile'] );
		if ($info) {
			$return ['url'] = $upload->rootPath . $info ['savepath'] . $info ['savename'];
		} else {
			$return ['error'] = 1;
			$return ['message'] = $upload->getError ();
		}
		exit ( json_encode ( $return ) );
	}
	// 编辑器浏览器设置
	public function filemanager() {
		$uid = session ( 'Uid' ); // 上传标识
		if (empty ( $uid )) {
			$return ['error'] = 1;
			$return ['message'] = '没有登录不允许上传';
			exit ( json_encode ( $return ) );
		}
		// 根目录路径，可以指定绝对路径，比如 /var/www/attached/
		$root_path = './Uploads/' . $uid . '/';
		// 根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
		$root_url = './Uploads/' . $uid . '/';
		// 图片扩展名
		$ext_arr = array (
				'gif',
				'jpg',
				'jpeg',
				'png',
				'bmp' 
		);
		// 目录名
		$dir_name = empty ( $_GET ['dir'] ) ? '' : trim ( $_GET ['dir'] );
		// 不在上传目录退出
		if (! in_array ( $dir_name, array (
				'',
				'image',
				'flash',
				'media',
				'file' 
		) )) {
			echo "Invalid Directory name.";
			exit ();
		}
		if ($dir_name !== '') {
			$root_path .= $dir_name . "/";
			$root_url .= $dir_name . "/";
			if (! file_exists ( $root_path )) {
				mkdir ( $root_path );
			}
		}
		// 根据path参数，设置各路径和URL
		if (empty ( $_GET ['path'] )) {
			$current_path = realpath ( $root_path ) . '/';
			$current_url = $root_url;
			$current_dir_path = '';
			$moveup_dir_path = '';
		} else {
			$current_path = realpath ( $root_path ) . '/' . $_GET ['path'];
			$current_url = $root_url . $_GET ['path'];
			$current_dir_path = $_GET ['path'];
			$moveup_dir_path = preg_replace ( '/(.*?)[^\/]+\/$/', '$1', $current_dir_path );
		}
		// echo realpath($root_path);
		// 不允许使用..移动到上一级目录
		if (preg_match ( '/\.\./', $current_path )) {
			echo 'Access is not allowed.';
			exit ();
		}
		// 最后一个字符不是/
		if (! preg_match ( '/\/$/', $current_path )) {
			echo 'Parameter is not valid.';
			exit ();
		}
		// 目录不存在或不是目录
		if (! file_exists ( $current_path ) || ! is_dir ( $current_path )) {
			echo 'Directory does not exist.';
			exit ();
		}
		// 遍历目录取得文件信息
		$file_list = array ();
		if ($handle = opendir ( $current_path )) {
			$i = 0;
			while ( false !== ($filename = readdir ( $handle )) ) {
				if ($filename {0} == '.')
					continue;
				$file = $current_path . $filename;
				if (is_dir ( $file )) {
					$file_list [$i] ['is_dir'] = true; // 是否文件夹
					$file_list [$i] ['has_file'] = (count ( scandir ( $file ) ) > 2); // 文件夹是否包含文件
					$file_list [$i] ['filesize'] = 0; // 文件大小
					$file_list [$i] ['is_photo'] = false; // 是否图片
					$file_list [$i] ['filetype'] = ''; // 文件类别，用扩展名判断
				} else {
					$file_list [$i] ['is_dir'] = false;
					$file_list [$i] ['has_file'] = false;
					$file_list [$i] ['filesize'] = filesize ( $file );
					$file_list [$i] ['dir_path'] = '';
					$file_ext = strtolower ( pathinfo ( $file, PATHINFO_EXTENSION ) );
					$file_list [$i] ['is_photo'] = in_array ( $file_ext, $ext_arr );
					$file_list [$i] ['filetype'] = $file_ext;
				}
				$file_list [$i] ['filename'] = $filename; // 文件名，包含扩展名
				$file_list [$i] ['datetime'] = date ( 'Y-m-d H:i:s', filemtime ( $file ) ); // 文件最后修改时间
				$i ++;
			}
			closedir ( $handle );
		}
		// 排序形式，name or size or type
		$order = empty ( $_GET ['order'] ) ? 'name' : strtolower ( $_GET ['order'] );
		$sorts = array ();
		foreach ( $file_list as $row ) {
			$sorts ['size'] [] = $row ['filesize'];
			$sorts ['type'] [] = $row ['filetype'];
			$sorts ['name'] [] = $row ['filename'];
			// $sorts['datetime'][] = $row['datetime']; //时间排序
		}
		/*
		 * if($order=='datetime'){
		 * array_multisort($sorts['datetime'], SORT_ASC, $file_list);
		 * }
		 *
		 */
		if ($order == 'name') {
			array_multisort ( $sorts ['name'], SORT_ASC, $file_list );
		}
		if ($order == 'size') {
			array_multisort ( $sorts ['size'], SORT_DESC, $file_list );
		}
		if ($order == 'type') {
			array_multisort ( $sorts ['type'], SORT_ASC, $file_list );
		}
		
		$result = array ();
		// 相对于根目录的上一级目录
		$result ['moveup_dir_path'] = $moveup_dir_path;
		// 相对于根目录的当前目录
		$result ['current_dir_path'] = $current_dir_path;
		// 当前目录的URL
		$result ['current_url'] = $current_url;
		// 文件数
		$result ['total_count'] = count ( $file_list );
		// 文件列表数组
		$result ['file_list'] = $file_list;
		// 输出JSON字符串
		header ( 'Content-type: application/json; charset=UTF-8' );
		exit ( json_encode ( $result ) );
	}
}
