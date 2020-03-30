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

	public function findAllLiveByCourierByTown($town, $userId)
	{
		$sql = "SELECT * FROM dispatch_orders_by_town WHERE town LIKE '%$town%' AND status IN ('assigned','picking','delivering') AND courier_id = '$userId'";
		return $this->database->query($sql)->fetchAll();
	}


	public function findAllDelivered()
	{
		return $this->database->table('posted_orders')->where(['status' => 'delivered'])->fetchAll();
	}


	public function assignOrder($courier_id, $order_id, $operator_id, $status = "assigned")
	{
		$this->database->table('posted_orders')->wherePrimary($order_id)->update([
			'courier_id' => $courier_id,
			'status' => $status,
			'operator_id' => $operator_id
		]);
	}


	public function updateStatus($orderId, $orderStatus = null)
	{
		if ($orderStatus != null) {
			$this->database->table('posted_orders')->wherePrimary($orderId)->update([
				'status' => $orderStatus,
			]);
		}
	}


	public function fetchCount()
	{
		return $this->database->table('posted_orders')->count();
	}

	public function findAllNewInTown($town)
	{
		$sql = "SELECT
				*
				FROM
				dispatch_orders_by_town
				WHERE
				town LIKE '%$town%'
				AND
				status = 'new'
				";

		return $this->database->query("$sql")->fetchAll();
	}

	public function findAllLiveInTown($town, $operatorId)
	{
		$sql = "SELECT * FROM dispatch_orders_by_town WHERE town LIKE '%$town%' AND operator_id = $operatorId AND status IN ('assigned','picking','delivering')";
		return $this->database->query($sql)->fetchAll();
	}

	public function findAllDeliveredInTown($town, $operatorId)
	{
		$sql = "SELECT * FROM dispatch_orders_by_town WHERE town LIKE '%$town%' AND operator_id = $operatorId AND status = 'delivered'";
		return $this->database->query("$sql")->fetchAll();
	}

	public function saveDemand($demand)
	{
		$volunteerPlaceholder = [
			'personName' => 'poptávka z webu',
			'personPhone' => 0,
			'personEmail' => 'info@sousedskapomoc.cz',
			'town' => $demand->address
		];

		$data = $this->database->table("volunteers")->insert($volunteerPlaceholder);

		$output = [
			'id_volunteers' => $data->id,
			'status' => 'waiting',
			'delivery_address' => $demand->deliveryAddress ?? 'neznámá adresa',
			'delivery_phone' => $demand->deliveryPhone,
			'note' => "[Z WEBU] Poptávka pro: " . $demand->deliveryPerson,
			'order_items' => $demand->orderItems,
		];

		$this->database->table("posted_orders")->insert($output);
	}

	public function fetchAllWebDemands()
	{
		$sql = "SELECT * FROM posted_orders WHERE status = 'waiting'";
		return $this->database->query($sql)->fetchAll();
	}

	public function findAll()
	{
		return $this->database->table('posted_orders')->fetchAll();
	}

	public function removeOperator($orderId)
	{
		$sql = "UPDATE posted_orders SET operator_id = null WHERE id = $orderId";
		return $this->database->query($sql);
	}

	public function removeCourier($orderId)
	{
		$sql = "UPDATE posted_orders SET courier_id = null WHERE id = $orderId";
		return $this->database->query($sql);
	}

	public function findAllOrdersData()
	{
		$sql = "SELECT * FROM dispatch_orders_by_town";
		return $this->database->query($sql)->fetchAll();
	}

	public function remove($id)
	{
		$this->database->table('posted_orders')->wherePrimary($id)->delete();
	}

	public function updateTown($orderId, $town)
	{
		if ($town != null) {
			$this->database->table('posted_orders')->wherePrimary($orderId)->update([
				'town' => $town,
			]);
		}
	}

	public function fetchDeliveredCount()
	{
		return $this->database->table('posted_orders')->where(['status' => 'delivered'])->count();
	}
}
