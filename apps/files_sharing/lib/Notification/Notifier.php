<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2019, Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
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

namespace OCA\Files_Sharing\Notification;

use OC\Share\Share;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Notification\AlreadyProcessedException;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCP\Share\Exceptions\ShareNotFound;
use OCP\Share\IManager;

class Notifier implements INotifier {

	/** @var IFactory */
	protected $l10nFactory;
	/** @var IManager  */
	protected $shareManager;
	/** @var IURLGenerator */
	protected $url;

	public function __construct(
		IFactory $l10nFactory,
		IManager $shareManager,
		IURLGenerator $url
	) {
		$this->l10nFactory = $l10nFactory;
		$this->shareManager = $shareManager;
		$this->url = $url;
	}

	/**
	 * Identifier of the notifier, only use [a-z0-9_]
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getID(): string {
		return 'files_sharing';
	}

	/**
	 * Human readable name describing the notifier
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getName(): string {
		return $this->l10nFactory->get('files_sharing')->t('File sharing');
	}

	/**
	 * @param INotification $notification
	 * @param string $languageCode The code of the language that should be used to prepare the notification
	 * @return INotification
	 * @throws \InvalidArgumentException When the notification was not prepared by a notifier
	 * @throws AlreadyProcessedException When the notification is not needed anymore and should be deleted
	 * @since 9.0.0
	 */
	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== 'files_sharing') {
			throw new \InvalidArgumentException();
		}
		try {
			$share = $this->shareManager->getShareById($notification->getObjectId());
		} catch(ShareNotFound $e) {
			throw new AlreadyProcessedException();
		}

		if ($share->getShareType() === Share::SHARE_TYPE_USER) {
			if ($share->getSharedWith() !== $notification->getUser()) {
				throw new AlreadyProcessedException();
			}
		}

		$l = $this->l10nFactory->get('files_sharing', $languageCode);
		switch ($notification->getSubject()) {
			case 'incoming_user_share':
				$subject = $l->t('You received {share} as a share from {user}');
				$subjectParameters = [
					'share' => [
						'type' => 'highlight',
						'id' => $notification->getObjectId(),
						'name' => $share->getNode()->getName(),
					],
					'user' =>  [
						'type' => 'user',
						'id' => $share->getShareOwner(),
						'name' => $share->getShareOwner(),
					],
				];

				$placeholders = $replacements = [];
				foreach ($subjectParameters as $placeholder => $parameter) {
					$placeholders[] = '{' . $placeholder . '}';
					$replacements[] = $parameter['name'];
				}

				$notification->setParsedSubject(str_replace($placeholders, $replacements, $subject))
					->setRichSubject($subject, $subjectParameters)
					->setIcon($this->url->getAbsoluteURL($this->url->imagePath('core', 'actions/share.svg')));

				$acceptAction = $notification->createAction();
				$acceptAction->setParsedLabel($l->t('Accept'))
					->setLink($this->url->linkToOCSRouteAbsolute('files_sharing.ShareAPI.acceptShare', ['id' => $share->getId()]), 'POST')
					->setPrimary(true);
				$notification->addParsedAction($acceptAction);

				$rejectAction = $notification->createAction();
				$rejectAction->setParsedLabel($l->t('Reject'))
					->setLink($this->url->linkToOCSRouteAbsolute('files_sharing.ShareAPI.deleteShare', ['id' => $share->getId()]), 'DELETE')
					->setPrimary(false);
				$notification->addParsedAction($rejectAction);

				return $notification;
				break;

			default:
				throw new \InvalidArgumentException('Invalid subject');
		}
	}
}
