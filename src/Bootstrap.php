<?php
namespace ReneGeuze\SmarterRevisions;

use DateInterval;
use DateTime;

class Bootstrap
{
	private $amountKeepLatest = 5;
	private $amountKeepMinimum = 10;
	private $amountHoursKeepAll = 48;

	public function __construct()
	{
		add_filter('wp_revisions_to_keep', function($cur) {
			// Setting to -1 to keep all revisions until we run our own function
			return -1;
		}, 90);

		// Action fires when a revision is made
		// To clean revisions more often we should hook to a cron or page view
		add_action('_wp_put_post_revision', function($revisionId) {
			$revisions = $this->getPostRevisionsByRevisionId($revisionId);
			$this->deleteRevisions($revisions);
		});
	}

	/**
	 * Amount of latest revisions that should be kept regardless of date
	 * @todo  get from setting
	 * @return int
	 */
	public function getAmountKeepLatest()
	{
		return $this->amountKeepLatest;
	}

	/**
	 * Amount of revisions needed before this plugin runs
	 * @todo  get from setting
	 * @return int
	 */
	public function getAmountKeepMinimum()
	{
		return $this->amountKeepMinimum;
	}

	/**
	 * Amount of latest revisions that should be kept regardless of date
	 * @todo  get from setting
	 * @return int
	 */
	public function getAmountHoursKeepAll()
	{
		return $this->amountHoursKeepAll;
	}

	/**
	 * [getPostRevisionsByRevisionId description]
	 * @param  int $revisionId
	 * @return array of WP_Post
	 */
	public function getPostRevisionsByRevisionId($revisionId)
	{
		$revision = get_post($revisionId);
		return $this->getPostRevisionsByPostId($revision->post_parent);
	}

	/**
	 * [getPostRevisionsByPostId description]
	 * @param  int $postId
	 * @return array of WP_Post
	 */
	public function getPostRevisionsByPostId($postId)
	{
		return wp_get_post_revisions($postId, [
			'order' => 'DESC',
		]);
	}

	/**
	 * [getIntervals description]
	 * @return array List of interval types
	 */
	public function getIntervals()
	{
		return [
			[
				'name' => '1 day',
				'interval' => new DateInterval('P1D'),
				'max' => 7,
				'found' => 0,
			],
			[
				'name' => '1 week',
				'interval' => new DateInterval('P1W'),
				'max' => 4,
				'found' => 0,
			],
			[
				'name' => '1 month',
				'interval' => new DateInterval('P1M'),
				'max' => 6,
				'found' => 0,
			],
			[
				'name' => '1 year',
				'interval' => new DateInterval('P1Y'),
				'max' => null,
				'found' => 0,
			],
		];
	}

	public function deleteRevisions($revisions)
	{
		$totalRevisions = count($revisions);

		$amountKeepLatest = $this->getAmountKeepLatest();
		$amountKeepMinimum = $this->getAmountKeepMinimum();

		$amountHoursKeepAll = $this->getAmountHoursKeepAll();
		$intervals = $this->getIntervals();

		$intervalPointer = -1;
		$interval = null;

		if ($totalRevisions <= $amountKeepMinimum) {
			return;
		}
		$revisions = array_slice($revisions, $amountKeepLatest);

		$toDelete = [];

		$timePointer = new DateTime();
		$timePointer->sub(new DateInterval("PT{$amountHoursKeepAll}H"));

		foreach ($revisions as $revision) {
			if (false !== strpos($revision->post_name, 'autosave')) {
				continue;
			}
			$revTime = new DateTime($revision->post_modified);

			// This is the -keep all- interval
			if ($interval === null) {
				if ($revTime >= $timePointer) {
					continue;
				} else {
					$interval = &$intervals[++$intervalPointer];
					// Move timepointer to oldest set of the -keep all-
					$timePointer = $revTime;
				}
			}

			// Move interval type up if needed
			if (
				$interval['max'] !== null &&
				$interval['found'] >= $interval['max'] &&
				isset($intervals[$intervalPointer + 1])
			) {
				$interval = &$intervals[++$intervalPointer];
			}

			// If not yet to max
			if (
				$interval['max'] === null ||
				$interval['found'] < $interval['max']
			) {
				if ($revTime <= $timePointer) {
					$interval['found']++;
					$timePointer->sub($interval['interval']);
					continue;
				}
			}

			$toDelete[] = $revision;
		}

		$maxDelete = $totalRevisions - $amountKeepMinimum;

		// Check and make sure we stay above minimum
		if (count($toDelete) > $maxDelete) {
			$toDelete = array_slice($toDelete, count($toDelete) - $maxDelete);
		}

		foreach ($toDelete as $revision) {
			wp_delete_post_revision($revision->ID);
		}
	}
}
