<?php

class ConditionFilterComponent extends RFFilterComponentCore
{
	/**
	 * Add a text filter item. 
	 *
	 * If the text is **modified** by the user, the condition is applied to target
	 * components.
	 *
	 * All instaces of {{value}} in the condition are replaced by the text value.
	 *
	 * For example:
	 *
	 * We wish to apply a condition on name on a target component. Call:
	 *
	 * addTextCondition("Enter Name", "username = '{{value}}'");
	 *
	 * If the user enters "John" in the filter, the condition "username = 'John'" is applied
	 *
	 * Notes:
	 * The SQL is automatically escaped. It is not required to escape or sanitize the value.
	 * 
	 * @param strin $caption   the caption
	 * @param string $condition condition
	 */
	public function addTextCondition($caption, $condition, $defaultValue = "")
	{
		$key = $this->getKey();
		$this->addTextItem($key, $caption, $defaultValue);

		$this->conditions [$key] = array(
			'type' => 'text',
			'condition' => $condition
		);
	}

	public function addTextContainsCondition($caption, $condition, $defaultValue = "")
	{
		$key = $this->getKey();
		$this->addTextItem($key, $caption, $defaultValue);

		$this->conditions [$key] = array(
			'type' => 'text',
			'condition' => $condition,
			'valueMap' => '%{{value}}%' // internally change it into the "%string%" format
		);
	}

	/**
	 * Add a checkbox item with the condition being evaluated
	 *
	 * If the checkbox is selected, the condition is applied to the component
	 * 
	 * @param string $caption   the caption
	 * @param string $condition the condition
	 */
	public function addCheckboxCondition($caption, $condition, $defaultValue = false)
	{
		$key = $this->getKey();
		$this->addBooleanItem($key, $caption, $defaultValue);

		$this->conditions [$key] = array(
			'type' => 'bool',
			'condition' => $condition
		);

	}

	/**
	 * Adds a drop down menu to the filter.
	 *
	 * Example:
	 *
	 * $options = array(
	 * 		"In Stock",
	 * 		"Out of Stock"
	 * );
	 *
	 * $conditions = array(
	 * 		"quantity > 0",
	 * 		"quantity == 0"
	 * );
	 *
	 * addSelectCondition ("Status", $options, $conditions, 0);
	 * 
	 * @param string  $caption      The caption
	 * @param array  $options      the options to show in the drop down menu
	 * @param array  $conditions   The corresponding conditions to each option
	 * @param integer $defaultValue The index of the default select
	 */
	public function addSelectCondition($caption, $options, $conditions, $defaultValue = 0)
	{
		$key = $this->getKey();
		$this->addSelectItem($key, $caption, $options, $defaultValue);

		$this->conditions [$key] = array(
			'type' => 'select',
			'options' => $options,
			'conditions' => $conditions
		);

	}

	/**
	 * Add a multiple select condition:
	 *
	 * Example:
	 *
	 * $options = array(
	 * 		"New",
	 * 		"Unresolved",
	 * 		"Resolved"
	 * );
	 *
	 * $conditions = array(
	 * 		"status = 0", // for new
	 * 		"status = 1", // for unresolved
	 * 		"status = 2"  // for resolved
	 * );
	 *
	 * addMultiSelectColumn($status, $options, $conditions, array(0, 1, 2));
	 * 
	 * @param string  $caption      The caption
	 * @param array  $options      the options to show in the drop down menu
	 * @param array  $conditions   The corresponding conditions to each option
	 * @param integer $defaultValue The index of the default select
	 */
	public function addMultiSelectCondition ($caption, $options, $conditions, $defaultValues = array())
	{
		$key = $this->getKey();
		$this->addMultiSelectItem($key, $caption, $options, $defaultValues);

		$this->conditions [$key] = array(
			'type' => 'multiselect',
			'options' => $options,
			'conditions' => $conditions
		);

	}

	/**
	 * Add a time range condition
	 *
	 * Example:
	 *
	 * addTimeRangeCondition ("Sales Between", "saletime > {{start}} AND saletime < {{end}}", array(3445, 554));
	 * 
	 * @param string $caption       the caption
	 * @param string $condition     the condition
	 * @param array  $defaultValues array(startTime, endTime)
	 */
	public function addTimeRangeCondition ($caption, $condition, $defaultValues = array())
	{
		$formatString = "%m/%d/%Y";
		if(count($defaultValues) === 0)
		{
			$defaultValues = array(0, time());
		}
		else {
			$defaultValues = array(strtotime($defaultValues[0]), strtotime($defaultValues[1]));
		}

		$defaultValues = array(
				strftime($formatString, $defaultValues[0]),
				strftime($formatString, $defaultValues[1])
			);

		$key = $this->getKey();
		$this->addTimeRangeItem($key, $caption, $defaultValues);

		$this->conditions [$key] = array(
			'type' => 'timerange',
			'condition' => $condition
		);
	}

	/**
	 * Add a numeric range condition
	 *
	 * Example:
	 *
	 * addNumericRangeCondition ("Sales Between", "saletime > {{start}} AND saletime < {{end}}", array(3445, 554));
	 * 
	 * @param string $caption       the caption
	 * @param string $condition     the condition
	 * @param array  $defaultValues array(startTime, endTime)
	 */
	public function addNumericRangeCondition ($caption, $condition, $defaultValues = array())
	{
		if(count($defaultValues) === 0)
		{
			$defaultValues = array(0, 100);
		}


		$key = $this->getKey();
		$this->addNumericRangeItem($key, $caption, $defaultValues);

		$this->conditions [$key] = array(
			'type' => 'numericrange',
			'condition' => $condition
		);
	}

	/**
	 * Add the filter to a component
	 * @param Component $component The target component
	 */
	public function addFilterTo ($component)
	{
		$this->targets []= $component;
	}


	/**
	 * A counter to store the number of items
	 * @var integer
	 */
	protected $nItems = -1;

	/**
	 * An array to hold all the conditions that can be applied later
	 * @var array
	 */
	protected $conditions = array();

	/**
	 * The target components that will be filtered
	 * @var array
	 */
	protected $targets = array();

	/**
	 * Utility function to get a unique numeric key
	 * @return string A unique key
	 */
	protected function getKey(){
		$this->nItems++;
		return "items_".$this->nItems;
	}

	protected function onActionTriggered($actionName, $params)
	{
		if($actionName !== "filterApply")
		{
			// invalid action
			RFAssert::Exception("Invalid action $actionName");
		}

		foreach($this->targets as $target) {
			foreach($params as $key => $value) {
				if(!isset($this->conditions[$key]))
					RFAssert::Exception("The key $key is not recognized by the filter");

				RFLog::log("Handling $key => ", $value);

				$condition = $this->conditions[$key];

				switch($condition['type'])
				{
					case 'text':
						// TODO SECURITY: Sanitize value
						if(isset($condition['valueMap']))
						{
							$value = str_replace("{{value}}", $value, $condition['valueMap']);
						}
						$key = ":".$target->randbind($value);
						$clause = str_replace("{{value}}", $key, $condition['condition']);
						$target->addSQLWhere($clause, "AND");
					break;


					case 'bool':
						if($value == TRUE)
						{
							$target->addSQLWhere($condition['condition'], "AND");
						}
						break;


					case 'select':
						// find the index of $value in the array
						$index = array_search($value, $condition['options']);
						if($index === FALSE)
							RFAssert::Exception ("The value for the filter was not found in the list of conditions");

						$target->addSQLWhere($condition['conditions'][$index], "AND");
						break;

					case 'timerange':
					case 'numericrange':
						$start = $value[0];
						$end = $value[1];

						if($condition['type'] === "timerange")
						{
							$start = ":".$target->randbind(strftime("%Y-%m-%d", strtotime($start)));
							$end = ":".$target->randbind(strftime("%Y-%m-%d", strtotime($end)));
						}
						else {
							$start = ":".$target->randbind(floatval($start));
							$end = ":".$target->randbind(floatval($end));
						}

						$clause = $condition['condition'];
						$clause = str_replace("{{start}}", $start, $clause);
						$clause = str_replace("{{end}}", $end, $clause);

						$target->addSQLWhere($clause, "AND");
						break;


					case 'multiselect':
						if(count($value) > 0)
						{
							$clause = "(";
							$multiSelConds = array();

							$options = $condition['options'];
							$optionLookup = array_flip($options);

							$indices = array();

							foreach($value as $item)
							{
								if(!isset($optionLookup[$item]))
								{
									RFAssert::Exception("Error, while performing an option lookup - $item wasn't found");
								}
								$indices []= $optionLookup[$item];
							}


							foreach($indices as $index)
								$multiSelConds []= $condition['conditions'][$index];
							$clause = $clause.implode(" OR ", $multiSelConds). ")";
							
							$target->addSQLWhere($clause, "AND");
							
						}
					break;
				}
			}
		}

		// foreach($whereClauses  as $value)
		// {
		// 	RFLog::log("Adding an $key clause", $value);
		// 	foreach($this->targets as $target)
		// 	{
		// 		$target->addCondition($value['clause'], $value['op']);
		// 	}
		// }

		return $this->targets;
	}

	public function initialize () {
		$init = parent::initialize();
		if($init)
			return $init;

		// Don't do anything here. The real functionality is activated when an action is triggered

		return false;
	}
}