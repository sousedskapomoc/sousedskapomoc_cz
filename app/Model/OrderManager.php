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



    public function findAllLive()
    {
        return $this->database->table('posted_orders')->whereOr([
            'status' => [
                'assigned',
                'pickedUp',
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
}
