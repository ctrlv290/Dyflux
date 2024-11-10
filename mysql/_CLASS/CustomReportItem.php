<?php


class CustomReportItem
{
	private $index;
	public $parent_idx;

	public $name;
	public $title = "";

	public $col_name = "";
	public $table_name = "";
	public $table_alias = "";

	public $join_type = "JOIN";
	public $join_col = "";
	public $target_col = "";

	public $type = "";
	public $value = null;

	public $child_items = []; // CustomReportItem - summable
	public $denied_items = []; // CustomReportItem index
	public $workable_items = "";

	public $sql_type = null;
	public $sql_format = "";
	public $group_func = "";
	public $alias_yn = true;

	/**
	 * CustomReportItem constructor.
	 * @param $index
	 */
	public function __construct($index)
	{
		$this->index = $index;
	}

	public function getIndex() {
		return $this->index;
	}

	public function isSummable() {
		return $this->group_func == "SUM";
	}

	public function appendChild(CustomReportItem $item) {
		$this->child_items[] = $item;
	}

	protected function getFormatFieldValue(CustomReportItem &$target, string $field_str, string $index_str = "0") {
		switch ($field_str) {
			case "col_name":
				$replace_value = $target->col_name;
				break;
			case "value":
				$replace_value = $target->value;
				break;
			case "name":
				$replace_value = $target->name;
				break;
			case "table_name":
				$replace_value = $target->table_name;
				break;
			case "table_alias":
				$replace_value = $target->table_alias;
				break;
			default:
				$replace_value = null;
		}

		return $replace_value;
	}

	public function toSQLRegion() {
		$sql = $this->sql_format;

		preg_match_all("/(?<={)[^{}]+(?=})/", $sql, $format_objs);

		foreach ($format_objs[0] as $format_obj) {
			$args = explode("/", $format_obj);
			$args_count = count($args);

			if($args_count < 2 || $args_count > 3) continue;

			$field = $args[1];
			$index = $args_count == 3 ? $args[2] : "0";

			$replace_value = $this->getFormatFieldValue($this, $field, $index);

			$sql = str_replace("{".$format_obj."}", $replace_value, $sql);
		}

		if($this->sql_type == "COLUMN" && $this->alias_yn) {
			$sql .= " as '".$this->title."'";
		}

		return $sql;
	}

	public function toSQLRegionGroup(){
		$sql = $this->toSQLRegion();

		if($this->sql_type != "COLUMN") return $sql;

		if($this->group_func) {
			$sql_list = explode(" as ", $sql);

			if(count($sql_list)) {
				$sql_col = $sql_list[0];
				$sql = $this->group_func."(".$sql_col.")";

				if($this->sql_type == "COLUMN" && $this->alias_yn) {
					$sql_alias = str_replace("'", "", $sql_list[1]);
					$sql .= " as '".$sql_alias." 합계'";
				}
			}
		}

		return $sql;
	}
}
