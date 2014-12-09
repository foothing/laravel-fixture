<?php
namespace Foothing\LaravelFixture;

use Illuminate\Database\Eloquent\Model;

class Fixture {
	// Static fixtures cache.
	protected static $fixtures = array();

	/**
	 *  Retrieve the desired object. If not found in database it
	 *  will be created.
	 *  @param string $model
	 *  	Model class name.
	 *  @param array $data
	 *  	Array of object properties.
	 *  @return Model
	 *  	The model instance.
	 */
	public static function need($model, $data = array()) {
		// Try get him.
		// We assume that we are after an unique entity,
		// so we get the first row.
		if ($qb = self::find($model, $data)) {
			if ($instance = $qb->first()) {
				return $instance;
			}
		}

		// No luck, we create it.
		$instance = new $model();

		// Prevent mass-assignment issues.
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$instance->{$key} = $value;
			}
		}

		// Save and store the fixture for later deletion.
		if ($instance->save()) {
			$key = spl_object_hash($instance);
			return self::$fixtures[$key] = $instance;
		} else {
			// We better raise an exception here to avoid fake errors.
			throw new \Exception ("Fixture exception: cannot save model.");
		}
	}

	/**
	 *  Deelte the specified instance.
	 *  @param string $model
	 *  	Model class name.
	 *  @param array $data
	 *  	Array of object properties to match.
	 */
	public static function needNot($model, $where = array()) {
		if ($qb = self::find($model, $where)) {
			$qb->delete();
		}
	}

	/**
	 *  Helper function. It performs a whereRaw query
	 *  to search against the given $where clause.
	 *  @param unknown_type $model
	 *  @param unknown_type $where
	 */
	protected static function find($model, $where = array()) {
		if (!empty($where)) {
			foreach ($where as $column => $value) {
				$clauses[] = "$column=?";
				$values[] = $value;
			}
			$rawSql = implode(' AND ', $clauses);
			return $model::whereRaw($rawSql, $values);
		} else {
			return NULL;
		}
	}

	/**
	 *  Cleanup fixtures.
	 */
	public static function tearDown() {
		foreach (self::$fixtures as $fixture) {
			if ($fixture->exists) {
				try {
					// Deletion may fail in case of integrity constraints,
					// we can't handle this right now.
					$fixture->delete();
				} catch (\Exception $ex) {
					continue;
				}
			}
		}
	}
}