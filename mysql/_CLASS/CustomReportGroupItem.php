<?php


/**
 * Class CustomReportAxisItem
 */
class CustomReportGroupItem extends CustomReportItem
{
	private $rep_count = 0;
	private $union_column_name = "";

	public $display_item = ""; // CustomReportItem Index
	public $direction = "Y";

	/**
	 * CustomReportUnionItem constructor.
	 * @param $index
	 */
	public function __construct($index)
	{
		parent::__construct($index);
	}

	public function getChildSQLList() {
		if($this->direction == "Y") return [];

		$sql_list = [];

		foreach ($this->child_items as $key => $child_item) {
			$sql_format = $this->sql_format;

			$sql = preg_replace("/(?<=\/)i(?=})/", $key, $sql_format);

			preg_match_all("/(?<={)[^{}]+(?=})/", $sql, $format_objs);

			foreach ($format_objs[0] as $format_obj) {
				$args = explode("/", $format_obj);
				$args_count = count($args);

				if($args_count < 2 || $args_count > 3) continue;

				$target = $args[0] == "this" ? $this : $child_item;
				$field = $args[1];
				$index = $args_count == 3 ? $args[2] : "0";

				$replace_value = $this->getFormatFieldValue($target, $field, $index);

				$sql = str_replace("{".$format_obj."}", $replace_value, $sql);
			}

			$sql_list[] = array("alias" => $child_item->title, "condition" => $sql, "workable_items" => $this->workable_items);
		}

		return $sql_list;
	}
}