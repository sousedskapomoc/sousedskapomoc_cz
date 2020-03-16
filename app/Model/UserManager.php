<?php

declare(strict_types=1);

namespace SousedskaPomoc\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
final class UserManager implements Nette\Security\IAuthenticator
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


	public function __construct(Nette\Database\Context $database, Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}


	/**
	 * Performs an authentication.
	 *
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		[$username, $password] = $credentials;

		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_NAME, $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!$this->passwords->verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif ($this->passwords->needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
			$row->update([
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
			]);
		}

		$roles = explode(";", $row->role ?? 'user');

		$arr = $row->toArray();
		unset($arr[self::COLUMN_PASSWORD_HASH]);


		return new Nette\Security\Identity($row[self::COLUMN_ID], $roles, $arr);
	}


	/**
	 * Adds new user.
	 *
	 * @throws DuplicateNameException
	 */
	public function add(string $username, string $email, string $password): void
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
		return $this->database->table(self::TABLE_NAME)
			->where('id', $id) // must be called before update()
			->update(['password' => $password]);
	}


	public function getUserByEmailCode($emailCode)
	{
		return $this->database->table(self::TABLE_NAME)
			->where('emailCode', $emailCode)
			->fetch();
	}


	public function getUserByEmail($email)
	{
		return $this->database->table(self::TABLE_NAME)
			->where('personEmail', $email);
	}


	public function setUserCode($userId, $emailCode)
	{
		return $this->database->table(self::TABLE_NAME)
			->where('id', $userId)
			->update(['emailCode' => $emailCode]);
	}


	public function check(string $field, $value)
	{
		return count($this->database->table(SELF::TABLE_NAME)->where([$field => $value]));
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
		return $this->database->table(self::TABLE_NAME)->count();
	}


	public function fetchCountBy($rule)
	{
		return $this->database->table(self::TABLE_NAME)->where($rule)->count();
	}


	public function fetchUniqueTownsCount()
	{
		$sql = "SELECT COUNT(DISTINCT(town)) AS uniqueTownsCount FROM volunteers";
		$data = $this->database->query($sql)->fetch();

		return $data['uniqueTownsCount'];
	}

	public function isOnline($userId)
	{
		return $this->database->table(self::TABLE_NAME)->select('active')->wherePrimary($userId)->fetch();
	}

	public function setOnline($userId, $active)
	{
		return $this->database->table(self::TABLE_NAME)->wherePrimary($userId)->update(['active' => $active]);
	}
}


class DuplicateNameException extends \Exception
{
}
