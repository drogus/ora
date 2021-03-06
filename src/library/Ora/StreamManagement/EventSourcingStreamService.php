<?php

namespace Ora\StreamManagement;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\Aggregate\AggregateRepository;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\Stream\SingleStreamStrategy;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Doctrine\ORM\EntityManager;
use Ora\User\User;
use Ora\Organization\Organization;
use Rhumsaa\Uuid\Uuid;

/**
 * @author Giannotti Fabio
 */
class EventSourcingStreamService extends AggregateRepository implements StreamService
{
	/**
	 *
	 * @var EntityManager
	 */
	private $entityManager;
	
	public function __construct(EventStore $eventStore, EntityManager $entityManager) {
		parent::__construct($eventStore, new AggregateTranslator(), new SingleStreamStrategy($eventStore), new AggregateType('Ora\StreamManagement\Stream'));
		$this->entityManager = $entityManager;
	}
    	
	public function createStream(Organization $organization, $subject, User $createdBy)
	{		
		$this->eventStore->beginTransaction();
		try {
		    $rv = Stream::create($organization, $subject, $createdBy);
		    $this->addAggregateRoot($rv);
			$this->eventStore->commit();
		} catch (\Exception $e) {
			$this->eventStore->rollback();
			throw $e;
		}
		return $rv;
	}

	public function getStream($id)
	{
		$sId = $id instanceof Uuid ? $id->toString() : $id;
		try {
		    $stream = $this->getAggregateRoot($this->aggregateType, $sId);
		    return $stream;
		} catch (\RuntimeException $e) {
			return null;
		}
	}
		
	public function findStream($id)
	{
		return $this->entityManager->find('Ora\ReadModel\Stream', $id);
	}
} 