<?php

namespace SousedskaPomoc\Model;

use Nette;

final class OrderManager
{
	use Nette\SmartObject;

	/** @var \Nette\Database\Context */
	protected $database;


	/**
	 * OrderManager constructor.
	 *
	 * @param \Nette\Database\Context $database
	 */
	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * @param $values
	 *
	 * @return bool|int|\Nette\Database\Table\ActiveRow
	 */
	public function create($values)
	{
		return $this->database->table('posted_orders')->insert($values);
	}


	/**
	 * @param $userId
	 *
	 * @return array|\Nette\Database\Table\IRow[]
	 */
	public function findAllForUser($userId)
	{
		return $this->database->table('posted_orders')->where('id_volunteers', $userId)->fetchAll();
	}


	public function findAllForCourier($userId)
	{
		return $this->database->table('posted_orders')->where('courier_id', $userId)->fetchAll();
	}


	/**
	 * @param $id
	 *
	 * @return \Nette\Database\IRow|\Nette\Database\Table\ActiveRow|null
	 */
	public function find($id)
	{
		return $this->database->table('posted_orders')->wherePrimary($id)->fetch();
	}


	public function findAllNew()
	{
		return $this->database->table('posted_orders')->where(['status' => 'new'])->fetchAll();
	}


	public function changeStatus($orderId, $status)
	{
		return $this->database->table('posted_orders')->wherePrimary($orderId)->update(['status' => $status]);
	}


	public function updateNote($orderId, $note)
	{
		return $this->database->table('posted_orders')->wherePrimary($orderId)->update(['courier_note' => $note]);
	}


	public function findAllLive()
	{
		return $this->database->table('posted_orders')->whereOr([
			'status' => [
				'assigned',
				'picking',
				'delivering',
			],
		])->fetchAll();
	}


	public function findAllDelivered()
	{
		return $this->database->table('posted_orders')->where(['status' => 'delivered'])->fetchAll();
	}


	public function assignOrder($courier_id, $order_id)
	{
		$this->database->table('posted_orders')->wherePrimary($order_id)->update([
			'courier_id' => $courier_id,
			'status' => 'assigned',
		]);
	}


	public function updateStatus($orderId, $orderStatus)
	{
		$this->database->table('posted_orders')->wherePrimary($orderId)->update([
			'status' => $orderStatus,
		]);
	}


	public function fetchCount()
	{
		return $this->database->table('posted_orders')->count();
	}

	public function findAllNewInTown($userData)
	{
		$sql = "SELECT
				posted_orders.*,
				volunteers.town AS limitMesto
				FROM
				posted_orders,
				volunteers
				WHERE
				posted_orders.id_volunteers = volunteers.id
				AND
				volunteers.town LIKE '%Praha%'
				AND
				posted_orders.status = 'new'
				";

		return $this->database->query("$sql")->fetchAll();
	}

	public function findAllLiveInTown($userData)
	{
		$sql = "SELECT * FROM dispatch_orders_by_town WHERE town LIKE '%$userData[town]%' AND status IN ('assigned','picking','delivering')";
		return $this->database->query($sql)->fetchAll();
	}

	public function findAllDeliveredInTown($userData)
	{
		$sql = "SELECT * FROM dispatch_orders_by_town WHERE town LIKE '%$userData[town]%' AND status = 'delivered'";
		return $this->database->query("$sql")->fetchAll();
	}
}
