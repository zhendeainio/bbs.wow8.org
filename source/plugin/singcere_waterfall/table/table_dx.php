<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/*
 * DX系统表数据读取操作
 * */
class table_dx extends discuz_table {
	public function __construct() {
		$this->_table 	= '';
		$this->_pk    	= '';
		parent::__construct();
	}
	
	public function fetch_thread_replies($count, $tid) {
		require_once libfile('function/discuzcode');
		require_once './source/plugin/singcere_waterfall/singcere_waterfall.func.php';
		$list = array();
		$query = DB::query("SELECT * FROM %t WHERE first != 1 AND tid = %d AND invisible >= 0 ORDER BY dateline DESC LIMIT 0, %d", array('forum_post', $tid, $count));
		while(($result = DB::fetch($query)) != false) {
			preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $result['message'], $matchaids);
			$attachs = parseattach($result['tid'], $result['pid'], $matchaids, $result['message'], $skipaids);
			$k = $result['message'] = preg_replace("/\[attach\](\d+)\[\/attach\]/ ", "\$attachs[\\1]", $result['message']);
			eval("\$k=\"$k\";");
			$k = discuzcode($k, FALSE, FALSE);
			$result['message'] = $k;
			$result['message'] = html2txt(($result['message']));
			$list[] = $result;
		}
		return $list;
	}
	
	public function fetch_all_replies_by_tids($tids, $count) {
		return DB::fetch_all("SELECT * FROM %t WHERE tid IN(%n) AND first != 1 AND invisible >=0 ORDER BY dateline DESC LIMIT %d", array('forum_post', $tids, $count));
	}
	
	public function fetch_all_threads($tids, $type, $fids, $sid, $havecover, $withpost, $start, $limit) {
		if(empty($tids)) {
			switch ($type) {
				case 'hot' :
					$addsql = ' AND heats>= 3';
					break;
				case 'digest' :
					$addsql = ' AND digest> 0';
					break;
				case 'top' :
					$addsql = ' AND displayorder >= 1';
					break;
				default :
					$addsql = ' AND 1';
			}
			if($sid) {
				$addsql .= ' AND special = '.$sid;
			}
			if($fids) {
				$flimit = " AND thread.fid in (".implode(',', $fids).")";
			}
			if($havecover) {
				$addsql .= " AND thread.cover != 0";
			}
			if(getglobal('setting/followforumid')) {
				$addsql .= ' AND thread.fid <>'. getglobal('setting/followforumid');
			}
		} else {
			$addsql = " AND thread.tid IN(".implode(',', $tids).")";
		}
		
		if($withpost) {
			$addsql .= " AND post.first = 1 AND displayorder>=0 ".$flimit." ORDER BY dateline DESC LIMIT $start, $limit";
			return DB::fetch_all("SELECT DISTINCT thread.*, pre.relay, coll.collection, post.message FROM "
				.DB::table('forum_thread')." thread LEFT JOIN "
				.DB::table('forum_collectionrelated')." coll ON coll.tid = thread.tid LEFT JOIN "
				.DB::table('forum_threadpreview')." pre ON pre.tid = thread.tid LEFT JOIN "
				.DB::table('forum_post')." post ON post.tid = thread.tid WHERE thread.displayorder >= 0 ".$addsql);
		} else {
			$addsql .= " AND displayorder>=0 ".$flimit." ORDER BY dateline DESC LIMIT $start, $limit";
			return DB::fetch_all("SELECT DISTINCT thread.*, pre.relay, coll.collection FROM "
				.DB::table('forum_thread')." thread LEFT JOIN "
				.DB::table('forum_collectionrelated')." coll ON coll.tid = thread.tid LEFT JOIN "
				.DB::table('forum_threadpreview')." pre ON pre.tid = thread.tid WHERE thread.displayorder >= 0 ".$addsql);
		}
		
	}
	
	public function fetch_all_tid_by_tagid($tagid, $type, $sid, $havecover, $start, $count) {
		$addsql = '';
		switch($type) {
			case 'hot' :
				$addsql = ' AND heats>= 3';
				break;
			case 'digest' :
				$addsql = ' AND digest> 0';
				break;
			case 'top' :
				$addsql = ' AND displayorder >= 1';
				break;
			default :
				$addsql = ' AND 1';
		}
		if($havecover) {
			$addsql .= " AND thread.cover != 0";
		}
		if($sid) {
			$addsql .= " AND special = $sid";
		}
		return DB::fetch_all("SELECT DISTINCT thread.tid FROM %t tagitem LEFT JOIN %t thread ON tagitem.itemid = thread.tid WHERE tagid IN(%n) AND thread.displayorder >= 0 %i LIMIT %d, %d", array('common_tagitem', 'forum_thread', $tagid, $addsql, $start, $count));
	}
	
	public function fetch_first_post_by_tid($tid) {
		return DB::fetch_first("SELECT * FROM %t WHERE tid = %d AND first = 1", array('forum_post', $tid));
	}
	
	public function count_thread($type, $minvalue, $fids, $sid, $hc = 0) {
		switch ($type) {
			case 'hot' :
				$addsql = ' AND heats>'.$minvalue;
				break;
			case 'digest' :
				$addsql = ' AND digest>'.$minvalue;
				break;
			default :
				$addsql = ' AND 1';
		}
		if($sid) {
			$addsql .= ' AND special = '.$sid;
		}
		
		if($fids) {
			$addsql .= " AND fid in (".implode(',', $fids).")";
		}
		
		if($hc) {
			$addsql .= " AND cover != 0";
		}
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE displayorder >= 0  %i", array('forum_thread', $addsql));
	}
	
	public function get_hot_tag($count) {
		$list = array();
		$query = DB::query("SELECT tag.tagid, count(*), tag.tagname FROM %t tag LEFT JOIN %t item ON tag.tagid = item.tagid GROUP BY tag.tagid ORDER BY count(*) DESC LIMIT 0, %d ",
				array('common_tag', 'common_tagitem', $count));
		while($result = DB::fetch($query)) {
			$list[] = $result;
		}
		return $list;
	}
	
	public function get_threads_by_tagid($tagid, $start, $count) {
		return DB::fetch_all("SELECT thread.* FROM %t tagitem LEFT JOIN %t thread ON tagitem.itemid = thread.tid WHERE tagitem.tagid = %d AND thread.displayorder >= 0 ORDER BY dateline DESC LIMIT %d, %d", array('common_tagitem', 'forum_thread', $tagid, $start, $count));
	}
	
	public function fetch_all_tag_by_tagname($tag) {
		if(is_array($tag)) {
			return DB::fetch_all("SELECT * FROM %t WHERE tagname IN(%n) AND status = 0", array('common_tag', $tag), 'tagid');
		} else {
			return DB::fetch_first("SELECT * FROM %t WHERE tagname = %s", array('common_tag', $tag), 'tagid');
		}
	}
	//www.Caogen8.Co
}