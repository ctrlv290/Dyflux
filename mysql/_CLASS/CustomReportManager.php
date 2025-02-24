<?php

require_once "DBConn.php";
require_once "CustomReportItem.php";

class CustomReportManager
{
	const ITEM_TYPE_SEARCH_CONDITION = "SEARCH_CONDITION";
	const ITEM_TYPE_X_GROUP = "DISPLAY_GROUP";
	const ITEM_TYPE_Y_GROUP = "AXIS_GROUP";
	const ITEM_TYPE_DISPLAY = "DISPLAY";

	private $item_types = [
		self::ITEM_TYPE_SEARCH_CONDITION,
		self::ITEM_TYPE_X_GROUP,
		self::ITEM_TYPE_Y_GROUP,
		self::ITEM_TYPE_DISPLAY
	];

	private $default_table = "DY_SETTLE";
	private $default_alias = "S";
	private $joined_aliases = [];

	private $conn;

	private $period_start_item;
	private $period_end_item;

	private $report_items;
	private $non_combine_targets;

	private $default_wheres = [];

	// private $report_union_item;

	private $report_special_items = ["8"/*그룹별 일 합*/]; //TODO: 자동화

	function __construct() {
		$this->conn = new DBConn();
		$this->conn->db_connect();

		$this->period_start_item = $this->createItem("1");
		$this->period_end_item = $this->createItem("2");

		$this->clearReport();

		$this->default_wheres[] = ["S", "settle_is_del = 'N'"];
	}

	function __destruct()
	{
		// TODO: Implement __destruct() method.
		$this->conn->db_close();
	}

	/**
	 * @param $index
	 * @return CustomReportItem
	 */
	private function getItem($index) : CustomReportItem {
		$item = null;

		foreach ($this->report_items as $type_items) {
			if(array_key_exists($index, $type_items)) {
				$item = $type_items[$index];
				break;
			}
		}

		return $item;
	}

	/**
	 * @param $index
	 * @return CustomReportGroupItem
	 */
	private function getGroupItem($index) : CustomReportGroupItem {
		$item = null;

		foreach ($this->report_items as $type => $type_items) {
			if($type != self::ITEM_TYPE_X_GROUP && $type != self::ITEM_TYPE_Y_GROUP)
				continue;

			if(array_key_exists($index, $type_items)) {
				$item = $type_items[$index];
				break;
			}
		}

		return $item;
	}

	private function getGroupItemFromNo(int $no) : CustomReportGroupItem{
		if(count($this->report_items[self::ITEM_TYPE_X_GROUP]) == 0)
			return null;

		$arr = array_values($this->report_items[self::ITEM_TYPE_X_GROUP]);
		$item = $arr[$no];
		return $item;
	}

	/**
	 * @param $index
	 * @param $type
	 * @return CustomReportItem
	 */
	private function getItemWithType($index, $type) {
		if(!array_search($type, $this->item_types)) return null;
		return $this->report_items[$type][$index];
	}

	/**
	 * 아이템 생성 함수
	 * @param $item_idx
	 * @return CustomReportItem|CustomReportGroupItem
	 */
	public function createItem($item_idx) {
		$data = $this->conn->execSqlOneRow("SELECT * FROM DY_CUSTOM_REPORT_ITEM WHERE idx = $item_idx");

		// UNION 과 그외 구별
		if($data["type"] == self::ITEM_TYPE_Y_GROUP || $data["type"] == self::ITEM_TYPE_X_GROUP) {
			$item = new CustomReportGroupItem($data["idx"]);
			$item->display_item = $data["display_item"];
			if($data["type"] == self::ITEM_TYPE_X_GROUP) $item->direction = "X";
		} else {
			$item = new CustomReportItem($data["idx"]);
		}

		$item->parent_idx = $data["parent_idx"];
		$item->name = $data["name"];
		$item->title = $data["title"];
		$item->table_name = $data["table_name"] ? $data["table_name"] : $this->default_table;
		$item->table_alias = $data["table_alias"] ? $data["table_alias"] : $this->default_alias;
		if($data["join_col"]) $item->join_col = $data["join_col"];
		if($data["join_target_col"]) $item->target_col = $data["join_target_col"];
		if($data["join_type"]) $item->join_type = $data["join_type"];
		$item->col_name = $data["col_name"];
		$item->type = $data["type"];
		$item->group_func = $data["group_func"];
		if($data["alias_yn"] == 'N') $item->alias_yn = false;
		if($data["denied_items"]) $item->denied_items = explode(",", $data["denied_items"]);
		if($data["subable_items"]) $item->workable_items = $data["subable_items"];

		$item->sql_type = $data["sql_type"];
		if($item->sql_type == null) {
			switch ($item->type) {
				case self::ITEM_TYPE_SEARCH_CONDITION: $item->sql_type = "WHERE"; break;
				case self::ITEM_TYPE_Y_GROUP: $item->sql_type = "GROUP BY"; break;
				case self::ITEM_TYPE_X_GROUP:
				case self::ITEM_TYPE_DISPLAY: $item->sql_type = "COLUMN"; break;
			}
		}

		$item->sql_format = $data["sql_format"];

		return $item;
	}

	private function clearReport() {
		foreach ($this->item_types as $type) {
			$this->report_items[$type] = [];
		}

		// $this->report_union_item = null;

		$today_str = date("Y-m-d");
		$this->period_start_item->value = $today_str;
		$this->period_end_item->value = $today_str;
		$this->non_combine_targets = [];
	}

	/**
	 * @return CustomReportItem
	 */
	private function getPeriodStartItem() {
		return $this->period_start_item;
	}

	/**
	 * @return CustomReportItem
	 */
	private function getPeriodEndItem() {
		return $this->period_end_item;
	}

//	/**
//	 * @return CustomReportAxisItem
//	 */
//	private function getUnionItem() {
//		return $this->report_union_item;
//	}

//	private function setUnionItem(CustomReportAxisItem $item) {
//		if($item) $this->report_union_item = $item;
//	}

	public function setPeriodFromValue($start_value, $end_value) {
		$period_item_start = $this->getPeriodStartItem();
		$period_item_end = $this->getPeriodEndItem();

		$period_item_start->value = $start_value;
		$period_item_end->value = $end_value;
	}

	public function createReport($item_list) {
		if(!$this->getPeriodStartItem() || !$this->getPeriodEndItem())
			return false;

		if(array_key_exists("1", $item_list)) {
			$start_date = $item_list["1"];
			$this->getPeriodStartItem()->value = $start_date;
			unset($item_list["1"]);
		}

		if(array_key_exists("2", $item_list)) {
			$end_date = $item_list["2"];
			$this->getPeriodEndItem()->value = $end_date;
			unset($item_list["2"]);
		}

		foreach($item_list as $item_idx => $value) {
			$item = $this->createItem($item_idx);
			$item->value = $value;

			if($item->type == self::ITEM_TYPE_Y_GROUP && $item->display_item) {
				// AXIS GROUP 이라면 노출항목에 관련 정보를 표시해야한다.
				$display_item = $this->createItem($item->display_item);
				$this->report_items[self::ITEM_TYPE_DISPLAY][$display_item->getIndex()] = $display_item;
				$this->non_combine_targets[] = $display_item->getIndex();
			}

			$this->report_items[$item->type][$item->getIndex()] = $item;
		}

		foreach ($this->report_items as $type => $type_items) {
			foreach($type_items as $item) {
				$this->specialOperation($item);
			}
		}

		$sql = $this->createSelectQry();
		$list = $this->conn->execSqlList($sql);
		return $list;
	}

	private function specialOperation(CustomReportItem &$item) {
		if($item->getIndex() == "34" || $item->getIndex() == "35" || $item->getIndex() == "41") {

			if($item->getIndex() == "34") $period_type = "m";
			elseif($item->getIndex() == "41") $period_type = "y";
			else $period_type = "d";

			$period = getPeriodList($period_type, $this->getPeriodStartItem()->value, $this->getPeriodEndItem()->value);
			foreach ($period as $date) {
				$child = new CustomReportItem("0");
				$child->name = $date;
				$child->title = $date;
				$child->value = $date;
				$item->appendChild($child);
			}
		} else {
			$sp_data = $this->conn->execSqlOneRow("SELECT * FROM DY_CUSTOM_REPORT_SPECIAL_OPER WHERE item_idx = ".$item->getIndex());
			if(!$sp_data) return;

			$child_list = $this->conn->execSqlList($sp_data["select_child_sql"]);
			foreach ($child_list as $child_data) {
				$child = new CustomReportItem("0");
				if($sp_data["child_name"])
					$child->name = $child_data[$sp_data["child_name"]];
				//$child->table_name = $child_data["child_table_name"];
				if($sp_data["child_title"])
					$child->title = $child_data[$sp_data["child_title"]];
				if($sp_data["child_value"])
					$child->value = $child_data[$sp_data["child_value"]];
				if($sp_data["child_col_name"])
					$child->col_name = $child_data[$sp_data["child_col_name"]];
				if($sp_data["child_type"])
					$child->type = $child_data[$sp_data["child_type"]];

				$item->appendChild($child);
			}
		}
	}

	private function createSelectQry($union_idx = 0) {
		if(!count($this->report_items)) return "";
		if(!$this->getPeriodStartItem() || !$this->getPeriodEndItem()) return "";

		$sql = "";

		$sql_cols = [];
		$sql_joins = [];
		$sql_wheres = [];
		$sql_group_bys = [];

		$x_group_yn = false;
		if(count($this->report_items[self::ITEM_TYPE_X_GROUP])) {
			$x_group_yn = true;
		}

		$y_group_yn = false;
		if(count($this->report_items[self::ITEM_TYPE_Y_GROUP])) {
			$y_group_yn = true;
		}

		$sql_wheres[] = $this->getPeriodStartItem()->toSQLRegion();
		$sql_wheres[] = $this->getPeriodEndItem()->toSQLRegion();

		foreach ($this->report_items as $type => $type_items) {
			if($x_group_yn && $type == self::ITEM_TYPE_X_GROUP) continue;

			foreach ($type_items as $key => $value) {
				$item = $this->getItem($key);

				if ($this->default_table != $item->table_name && !in_array($item->table_alias, $this->joined_aliases)) {
					if ($item->target_col && $item->join_col) {
						$this->joined_aliases[] = $item->table_alias;
						$sql_joins[] = "$item->join_type $item->table_name $item->table_alias ON $this->default_alias.$item->target_col = $item->table_alias.$item->join_col";
					}
				}

				$sql_type = $item->sql_type;

				switch ($sql_type) {
					case "COLUMN":
						if($item->group_func != "") {
							if($y_group_yn) {
								$sql_cols[] = $item->toSQLRegionGroup();
							} else {
								$sql_cols[] = $item->toSQLRegion();
							}
						} else {
							$sql_cols[] = $item->toSQLRegion();
						}
						break;
					case "WHERE":
						$sql_wheres[] = $item->toSQLRegion();
						break;
					case "GROUP BY":
						$sql_group_bys[] = $item->toSQLRegion();
						break;
				}
			}
		}

		if($x_group_yn) {
			$x_group = $this->xGroupColSQLs();
			if(count($x_group)) $sql_cols = array_merge($sql_cols, $x_group);
		}

		if(count($sql_cols)) {
			$sql .= "SELECT\n\t";
			$sql .= join("\n\t, ", $sql_cols);
		} else {
			return "";
		}

		$sql .= "\nFROM $this->default_table $this->default_alias\n";
		$sql .= join("\n\t", $sql_joins);

		foreach ($this->default_wheres as $default_where) {
			$sql_wheres[] = $default_where[0].".".$default_where[1];
		}

		if(count($sql_wheres)) {
			$sql .= "\nWHERE\n\t";
			$sql .= join("\n\tAND ", $sql_wheres);
		}

		if(count($sql_group_bys)) {
			$sql .= "\nGROUP BY ";
			$sql .= join(", ", $sql_group_bys);
		}

		$sql .= $this->getOrderBySQL();

		return $sql;
	}

	private function xGroupColSQLs() :array {
		$combined_sql_list = $this->combineXGroup();
		if(count($combined_sql_list) == 0 || count($this->report_items[self::ITEM_TYPE_DISPLAY]) == 0) return [];

		$result_list = [];

		foreach ($this->report_items[self::ITEM_TYPE_DISPLAY] as $key => $val) {
			if($this->isNonCombineTarget($key)) continue;

			$item = $this->getItem($key);

			if($item == null || $item->group_func == "") continue;

			$group_alias = $item->title;
			if($item->group_func == "SUM") {
				$group_alias .= " 합";
			} elseif($item->group_func == "AVG") {
				$group_alias .= " 평균";
			}

			$sql_format = $item->group_func."(CASE WHEN #CONDITION# THEN ".$item->col_name." ELSE 0 END) AS ";

			foreach ($combined_sql_list as $combined_sql) {
				if(strpos($combined_sql["workable_items"], $item->getIndex()) === false) continue 2;
				$alias = "'".$combined_sql["alias"]." ".$group_alias."'";
				$result_list[] = str_replace("#CONDITION#", $combined_sql["condition"], $sql_format).$alias;
			}
		}

		return $result_list;
	}

//	private function combineXConditions(array $conditions) :array {
//		if(!count($conditions)) return [];
//
//		$cur_conditions = $conditions[0];
//		array_shift($conditions);
//
//		$sub_conditions = $this->combineXConditions($conditions);
//
//		$return_array = [];
//
//		foreach ($cur_conditions as $cur_condition) {
//			if(count($conditions) == 0) {
//				$return_array[] = $cur_condition;
//			} else {
//				foreach ($sub_conditions as $sub_condition) {
//					$return_array[] = $cur_condition.", ".$sub_condition;
//				}
//			}
//		}
//
//		return $return_array;
//	}

	private function combineXGroup(int $depth = 0) :array {
		if($depth < 0 || ($depth > count($this->report_items[self::ITEM_TYPE_X_GROUP]) - 1)) return [];

		$cur_item = $this->getGroupItemFromNo($depth);
		if(!$cur_item) return [];

		$return_array = [];

		$child_sql_list = $cur_item->getChildSQLList();
		if($depth == (count($this->report_items[self::ITEM_TYPE_X_GROUP]) - 1)) {
			return $child_sql_list;
		} else {
			$next_child_sql_list = $this->combineXGroup($depth + 1);

			if(!count($next_child_sql_list)) return $child_sql_list;

			foreach ($child_sql_list as $child_sql) {
				foreach ($next_child_sql_list as $n_child_sql) {
					$arr = array("alias" => "", "condition" => "", "workable_items" => $child_sql_list["workable_items"]);
					$arr["alias"] = $child_sql["alias"]." ".$n_child_sql["alias"];
					$arr["condition"] = $child_sql["condition"].", ".$n_child_sql;
					$return_array[] = $arr;
				}
			}
		}

		return $return_array;
	}

	private function isNonCombineTarget($index) : bool {
		return array_search($index, $this->non_combine_targets) !== false;
	}

	/**
	 * 레포트 정렬 방식
	 * 1. 그룹이 있다면 그룹
	 * 2. 없다면 처음 노출항목
	 * @return string
	 */
	private function getOrderBySQL() {
		$sql = "";
		$order_by_list = [];

		$type_items = $this->report_items[self::ITEM_TYPE_Y_GROUP];
		foreach ($type_items as $key => $value) {
			$item = $this->getGroupItem($key);
			if($item) {
				if($item->table_name || $item->col_name)
					$order_by_list[] = $item->table_alias.".".$item->col_name;
			}
		}

		if(count($order_by_list)) {
			$sql .= "\nORDER BY ".join(", ", $order_by_list);
		} else {
			$type_items = $this->report_items[self::ITEM_TYPE_DISPLAY];
			if(count($type_items)) {
				$arr = array_keys($type_items);
				$item = $this->getItem($arr[0]);
				$sql .= "\nORDER BY ".$item->table_alias.".".$item->col_name;
			}
		}

		return $sql;
	}

	private function parseItemSQL($item_idx) {

	}

	public function saveTemplate($name, $report_data, $index = null) {
		global $GL_Member;
		$member_idx = $GL_Member["member_idx"];

		if($index) {
			if(!$this->templatePermissionCheck($index, $member_idx))
				return ["result" => false, "msg" => "수정 권한이 없습니다. 자신이 저장한 내역만 수정할 수 있습니다."];
		}

		$template_pk = "";

		$template_data = [];
		if($index) {
			$template_pk = "idx";
			$template_data["idx"] = $index;
		}
		$template_data["name"] = $name;
		$template_data["owner_member"] = $member_idx;
		$template_data["items"] = json_encode($report_data, true);
		$template_data["reg_date"] = "NOW()";

		$result = $this->conn->insertFromArray($template_data, "DY_CUSTOM_REPORT_TEMPLATE", $template_pk);

		if($result) {
			return ["result" => true];
		} else {
			return ["result" => false, "msg" => "DB 저장에 실패하였습니다."];
		}
	}

	public function deleteTemplate($index) {
		global $GL_Member;
		$member_idx = $GL_Member["member_idx"];

		if(!$this->templatePermissionCheck($index, $member_idx))
			return ["result" => false, "msg" => "삭제 권한이 없습니다. 자신이 저장한 내역만 삭제할 수 있습니다."];


		$template_data = [];
		$template_data["idx"] = $index;
		$template_data["is_del"] = 'Y';
		$result = $this->conn->insertFromArray($template_data, "DY_CUSTOM_REPORT_TEMPLATE", "idx");
		if($result) {
			return ["result" => true];
		} else {
			return ["result" => false, "msg" => "DB 저장에 실패하였습니다."];
		}
	}

	private function templatePermissionCheck($index, $member) {
		$list = $this->conn->execSqlOneCol("SELECT owner_member FROM DY_CUSTOM_REPORT_TEMPLATE WHERE idx = ".$index);
		if(count($list) == 0 || !$list) return false;

		if($list != $member) return false;

		return true;
	}
}