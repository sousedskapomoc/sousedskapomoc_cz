<?php

declare(strict_types=1);

namespace SousedskaPomoc\Model;

use Nette;
use Nette\Security\Passwords;
use SousedskaPomoc\Entities\Role;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\AddressRepository;
use SousedskaPomoc\Repository\RoleRepository;
use SousedskaPomoc\Repository\VolunteerRepository;

/**
 * Users management.
 */
final class UserManager
{

    use Nette\SmartObject;

    private const
        TABLE_NAME = 'volunteers',
        COLUMN_ID = 'id',
        COLUMN_NAME = 'personEmail',
        COLUMN_PASSWORD_HASH = 'password',
        COLUMN_EMAIL = 'personEmail',
        COLUMN_ROLE = 'role';

    /** @var Nette\Database\Context */
    private $database;

    /** @var Passwords */
    private $passwords;

    /** @var \SousedskaPomoc\Repository\VolunteerRepository */
    protected $volunteerRepository;

    /** @var RoleRepository */
    protected $roleRepository;

    /** @var AddressRepository */
    protected $addressRepository;



    public function __construct(
        Nette\Database\Context $database,
        Passwords $passwords,
        VolunteerRepository $volunteerRepository,
        RoleRepository $roleRepository,
        AddressRepository $addressRepository
    ) {
        $this->database = $database;
        $this->passwords = $passwords;
        $this->volunteerRepository = $volunteerRepository;
        $this->roleRepository = $roleRepository;
        $this->addressRepository = $addressRepository;
    }



    /**
     * Adds new user.
     *
     * @throws DuplicateNameException
     */
    public function add(string $username, string $email, string $password) : void
    {
        Nette\Utils\Validators::assert($email, 'email');
        try {
            $this->database->table(self::TABLE_NAME)->insert([
                self::COLUMN_NAME => $username,
                self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
                self::COLUMN_EMAIL => $email,
            ]);
        } catch (Nette\Database\UniqueConstraintViolationException $e) {
            throw new DuplicateNameException;
        }
    }



    public function register(Nette\Utils\ArrayHash $values)
    {
        return $this->database->table(self::TABLE_NAME)->insert($values);
    }



    public function update(Nette\Utils\ArrayHash $values)
    {
        return $this->database->table(self::TABLE_NAME)
            ->where('id', $values['id']) // must be called before update()
            ->update($values);
    }



    public function setPass($id, $password)
    {
        $this->volunteerRepository->setPass($id, $password);
    }



    public function getUserByEmailCode($emailCode)
    {
        return $this->volunteerRepository->getUserByHash($emailCode);
    }



    public function getUserByEmail($email)
    {
        return $this->volunteerRepository->getByEmail($email);
    }



    public function getUserById($id)
    {
        return $this->volunteerRepository->getById($id);
    }



    public function setUserCode($userId, $emailCode)
    {
        $this->setUserCode($userId, $emailCode);
    }



    public function check(string $field, $value)
    {
        return count($this->database->table(self::TABLE_NAME)->where([$field => $value]));
    }



    public function fetchAvailableCouriers()
    {
        return $this->database->table(self::TABLE_NAME)->where(['role' => 'courier'])->fetchAll();
    }



    public function fetchAllUsers()
    {
        return $this->database->table(self::TABLE_NAME)->where(['emailCode' => null])->fetchAll();
    }



    public function fetchAllUsersWithNoPass()
    {
        return $this->database->table(self::TABLE_NAME)->where(['password' => null])->fetchAll();
    }



    public function fetchCourierName($courierId)
    {
        $data = $this->database
            ->table(self::TABLE_NAME)
            ->select('personName')
            ->wherePrimary($courierId)
            ->fetch();

        return $data->personName ?? 'Nepřiřazen';
    }



    public function fetchTotalCount()
    {
        return $this->volunteerRepository->fetchTotalCount();
    }



    public function fetchCountByRole($role)
    {
        /** @var Role $role */
        $role = $this->roleRepository->getByName($role);

        return count($role->getUsers());
    }



    public function fetchCountBy($rule)
    {
        return $this->volunteerRepository->fetchCountBy($rule);
    }



    public function fetchUniqueTownsCount()
    {
        return $this->addressRepository->countUniqueTowns();
    }



    public function isOnline($userId)
    {
        return $this->volunteerRepository->isOnline($userId);
    }



    public function setOnline($userId, $active)
    {
        return $this->volunteerRepository->setOnline($userId, $active);
    }



    public function fetchAvailableCouriersInTown($town)
    {
        return $this->volunteerRepository->fetchAvailableCouriersInTown($town);
    }

    public function fetchNonAvailableCouriersInTown($town)
    {
        return $this->volunteerRepository->fetchNonAvailableCouriersInTown($town);
    }



    public function updateTown($selectedTown, $userId)
    {
        return $this->database->table(self::TABLE_NAME)->wherePrimary($userId)->update(['town' => $selectedTown]);
    }



    public function getTownForUser($userId)
    {
        return $this->volunteerRepository->getTownForUser($userId);
    }



    public function getTowns()
    {
        return $this->volunteerRepository->getTowns();
    }



    public function fetchAllUsersInRole($role = null)
    {
        return $this->volunteerRepository->fetchAllUsersInRole($role);
    }



    public function fetchPhoneNumber($courierId)
    {
        return $this->volunteerRepository->fetchPhoneNumber($courierId);
    }



    public function findAllOnlineUsers()
    {
        return $this->volunteerRepository->findAllOnlineUsers();
    }



    public function fetchAllUsersInRoleForGrid($role)
    {
        $dataset = [];

        /** @var Volunteer $volunteer */
        foreach ($this->fetchAllUsersInRole($role) as $volunteer) {
            if ($volunteer->getAddress() !== null) {
                $city = $volunteer->getAddress()->getCity();
            } else {
                $city = null;
            }

            $dataset[] = [
                'id' => $volunteer->getId(),
                'personName' => $volunteer->getPersonName(),
                'personEmail' => $volunteer->getPersonEmail(),
                'personPhone' => $volunteer->getPersonPhone(),
                'address' => $city,
                'active' => $volunteer->getActive(),
                'note' => $volunteer->getNote()
            ];

            $city = null;
        }

        return $dataset;
    }
}
