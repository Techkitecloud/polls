<?php
/**
 * @copyright Copyright (c) 2017 René Gieling <github@dartcafe.de>
 *
 * @author René Gieling <github@dartcafe.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Polls\Migration;

use OCP\DB\ISchemaWrapper;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version0108Date20210307130009 extends SimpleMigrationStep {

	/** @var IDBConnection */
	protected $connection;

	/** @var IConfig */
	protected $config;

	public function __construct(IDBConnection $connection, IConfig $config) {
		$this->connection = $connection;
		$this->config = $config;
	}

	public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if ($schema->hasTable('polls_options')) {
			$table = $schema->getTable('polls_options');
			if (!$table->hasIndex('UNIQ_options')) {
				$table->addUniqueIndex(['poll_id', 'poll_option_text', 'timestamp'], 'UNIQ_options');
			}
		}

		if ($schema->hasTable('polls_log')) {
			$table = $schema->getTable('polls_log');
			if (!$table->hasIndex('UNIQ_unprocessed')) {
				$table->addUniqueIndex(['processed', 'poll_id', 'user_id', 'message_id'], 'UNIQ_unprocessed');
			}
		}

		if ($schema->hasTable('polls_notif')) {
			$table = $schema->getTable('polls_notif');
			if (!$table->hasIndex('UNIQ_subscription')) {
				$table->addUniqueIndex(['poll_id', 'user_id'], 'UNIQ_subscription');
			}
		}

		if ($schema->hasTable('polls_share')) {
			$table = $schema->getTable('polls_share');
			if (!$table->hasIndex('UNIQ_shares')) {
				$table->addUniqueIndex(['poll_id', 'user_id'], 'UNIQ_shares');
			}
		}

		if ($schema->hasTable('polls_votes')) {
			$table = $schema->getTable('polls_votes');
			if (!$table->hasIndex('UNIQ_votes')) {
				$table->addUniqueIndex(['poll_id', 'user_id', 'vote_option_text'], 'UNIQ_votes');
			}
		}

		if ($schema->hasTable('polls_preferences')) {
			$table = $schema->getTable('polls_preferences');
			if (!$table->hasIndex('UNIQ_preferences')) {
				$table->addUniqueIndex(['user_id'], 'UNIQ_preferences');
			}
		}

		if ($schema->hasTable('polls_watch')) {
			$table = $schema->getTable('polls_watch');
			if (!$table->hasIndex('UNIQ_watch')) {
				$table->addUniqueIndex(['poll_id', 'table'], 'UNIQ_watch');
			}
		}

		return $schema;
	}
}