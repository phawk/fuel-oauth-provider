<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package		Fuel
 * @version		1.0
 * @author		Fuel Development Team
 * @license		MIT License
 * @copyright	2010 - 2011 Fuel Development Team
 * @link		http://fuelphp.com
 */

namespace Orm;

class HasOne extends Relation {

	protected $singular = true;

	public function __construct($from, $name, array $config)
	{
		$this->name        = $name;
		$this->model_from  = $from;
		$this->model_to    = array_key_exists('model_to', $config) ? $config['model_to'] : \Inflector::get_namespace($from).'Model_'.\Inflector::classify($name);
		$this->key_from    = array_key_exists('key_from', $config) ? (array) $config['key_from'] : $this->key_from;
		$this->key_to      = array_key_exists('key_to', $config) ? (array) $config['key_to'] : (array) \Inflector::foreign_key($this->model_from);
		$this->conditions  = array_key_exists('conditions', $config) ? (array) $config['conditions'] : array();

		$this->cascade_save    = array_key_exists('cascade_save', $config) ? $config['cascade_save'] : $this->cascade_save;
		$this->cascade_delete  = array_key_exists('cascade_delete', $config) ? $config['cascade_delete'] : $this->cascade_delete;

		if ( ! class_exists($this->model_to))
		{
			throw new \Fuel_Exception('Related model not found by Has_One relation "'.$this->name.'": '.$this->model_to);
		}
	}

	public function get(Model $from)
	{
		$query = call_user_func(array($this->model_to, 'find'));
		reset($this->key_to);
		foreach ($this->key_from as $key)
		{
			$query->where(current($this->key_to), $from->{$key});
			next($this->key_to);
		}
		return $query->get_one();
	}

	public function join($alias_from, $rel_name, $alias_to_nr, $conditions = array())
	{
		$conditions = array_merge($this->conditions, $conditions);

		$alias_to = 't'.$alias_to_nr;
		$model = array(
			'model'        => $this->model_to,
			'connection'   => call_user_func(array($this->model_to, 'connection')),
			'table'        => array(call_user_func(array($this->model_to, 'table')), $alias_to),
			'primary_key'  => call_user_func(array($this->model_to, 'primary_key')),
			'join_type'    => 'left',
			'join_on'      => array(),
			'columns'      => $this->select($alias_to),
			'rel_name'     => strpos($rel_name, '.') ? substr($rel_name, strrpos($rel_name, '.') + 1) : $rel_name,
			'relation'     => $this,
			'where'        => array_key_exists('where', $conditions)    ? $conditions['where']    : array(),
			'order_by'     => array_key_exists('order_by', $conditions) ? $conditions['order_by'] : array(),
		);

		reset($this->key_to);
		foreach ($this->key_from as $key)
		{
			$model['join_on'][] = array($alias_from.'.'.$key, '=', $alias_to.'.'.current($this->key_to));
			next($this->key_to);
		}

		return array($rel_name => $model);
	}

	public function save($model_from, $model_to, $original_model_id, $parent_saved, $cascade)
	{
		if ( ! $parent_saved)
		{
			return;
		}

		if ( ! $model_to instanceof $this->model_to and $model_to !== null)
		{
			throw new \Fuel_Exception('Invalid Model instance added to relations in this model.');
		}

		$current_model_id = ($model_to and ! $model_to->is_new()) ? $model_to->implode_pk($model_to) : null;
		// Check if there was another model assigned (this supersedes any change to the foreign key(s))
		if (($model_to and $model_to->is_new()) or $current_model_id != $original_model_id)
		{
			// assign this object to the new objects foreign keys
			if ( ! empty($model_to))
			{
				reset($this->key_to);
				$frozen = $model_to->frozen(); // only unfreeze/refreeze when it was frozen
				$frozen and $model_to->unfreeze();
				foreach ($this->key_from as $pk)
				{
					$model_to->{current($this->key_to)} = $model_from->{$pk};
					next($this->key_to);
				}
				$frozen and $model_to->freeze();
			}

			// if still loaded set this object's old relation's foreign keys to null
			if ($original_model_id and $obj = call_user_func(array($this->model_to, 'find'),
				count($this->key_to) == 1 ? array($original_model_id) : explode('][', substr($original_model_id, 1, -1))))
			{
				// check whether the object still refers to this model_from
				$changed = false;
				reset($this->key_to);
				foreach ($this->key_from as $pk)
				{
					if ($obj->{current($this->key_to)} != $model_from->{$pk})
					{
						$changed = true;
					}
					next($this->key_to);
				}

				// when it still refers to this object, reset the foreign key(s)
				if ( ! $changed)
				{
					$frozen = $obj->frozen(); // only unfreeze/refreeze when it was frozen
					$frozen and $obj->unfreeze();
					foreach ($this->key_to as $fk)
					{
						$obj->{$fk} = null;
					}
					$frozen and $obj->freeze();

					// cascading this change won't work here, save just the object with cascading switched off
					$obj->save(false);
				}
			}
		}
		// if not empty check the model_to's foreign_keys, when empty nothing changed
		elseif ( ! empty($model_to))
		{
			// check if model_to still refers to this model_from
			$changed = false;
			reset($this->key_to);
			foreach ($this->key_from as $pk)
			{
				if ($model_to->{current($this->key_to)} != $model_from->{$pk})
				{
					$changed = true;
				}
				next($this->key_to);
			}

			// if any of the keys changed, the relationship was broken - remove model_to from loaded objects
			if ($changed)
			{
				// Remove the model_to from the relationships of model_from
				$model_from->unfreeze();
				$rel = $model_from->_relate();
				$rel[$this->name] = null;
				$model_from->_relate($rel);
				$model_from->freeze();
			}
		}

		$cascade = is_null($cascade) ? $this->cascade_save : (bool) $cascade;
		if ($cascade and ! empty($model_to))
		{
			$model_to->save();
		}
	}

	public function delete($model_from, $model_to, $parent_deleted, $cascade)
	{
		if ( ! $parent_deleted)
		{
			return;
		}

		// break current relations
		$model_from->unfreeze();
		$rels = $model_from->_relate();
		$rels[$this->name] = null;
		$model_from->_relate($rels);
		$model_from->freeze();

		if ( ! $model_to->frozen())
		{
			foreach ($this->key_to as $fk)
			{
				$model_to->{$fk} = null;
			}
		}

		$cascade = is_null($cascade) ? $this->cascade_delete : (bool) $cascade;
		if ($cascade and ! empty($model_to))
		{
			$model_to->delete();
		}
	}
}

/* End of file hasone.php */