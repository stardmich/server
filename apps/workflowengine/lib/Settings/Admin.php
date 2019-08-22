<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019 Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\WorkflowEngine\Settings;

use OCA\WorkflowEngine\AppInfo\Application;
use OCA\WorkflowEngine\Manager;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IInitialStateService;
use OCP\IL10N;
use OCP\Settings\ISettings;
use OCP\WorkflowEngine\IEntity;
use OCP\WorkflowEngine\IEntityEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Admin implements ISettings {

	/** @var IL10N */
	private $l10n;

	/** @var string */
	private $appName;

	/** @var EventDispatcherInterface */
	private $eventDispatcher;

	/** @var Manager */
	private $manager;

	/** @var IInitialStateService */
	private $initialStateService;

	/**
	 * @param string $appName
	 * @param IL10N $l
	 * @param EventDispatcherInterface $eventDispatcher
	 */
	public function __construct(
		$appName,
		IL10N $l,
		EventDispatcherInterface $eventDispatcher,
		Manager $manager,
		IInitialStateService $initialStateService
	) {
		$this->appName = $appName;
		$this->l10n = $l;
		$this->eventDispatcher = $eventDispatcher;
		$this->manager = $manager;
		$this->initialStateService = $initialStateService;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm() {
		$this->eventDispatcher->dispatch('OCP\WorkflowEngine::loadAdditionalSettingScripts');

		$entities = $this->manager->getEntitiesList();

		$this->initialStateService->provideInitialState(
			Application::APP_ID,
			'entities',
			$this->entitiesToArray($entities)
		);

		return new TemplateResponse(Application::APP_ID, 'admin', [], 'blank');
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 */
	public function getSection() {
		return 'workflow';
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 */
	public function getPriority() {
		return 0;
	}

	private function entitiesToArray(array $entities) {
		return array_map(function (IEntity $entity) {
			$events = array_map(function(IEntityEvent $entityEvent) {
				return [
					'eventName' => $entityEvent->getEventName(),
					'displayName' => $entityEvent->getDisplayName()
				];
			}, $entity->getEvents());

			return [
				'id' => $entity->getId(),
				'icon' => $entity->getIcon(),
				'name' => $entity->getName(),
				'events' => $events,
			];
		}, $entities);
	}

}
