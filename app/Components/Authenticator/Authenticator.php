<?php


namespace SousedskaPomoc\Components;

use Nette\Security\IAuthenticator;
use SousedskaPomoc\Entities\Volunteer;
use SousedskaPomoc\Repository\VolunteerRepository;
use Nette\Security\Passwords;
use Nette\Security\IIdentity;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Tracy\Debugger;

class Authenticator implements IAuthenticator
{
    /** @var VolunteerRepository */
    private $volunteerRepository;

    /** @var Passwords */
    private $passwords;

    public function __construct(VolunteerRepository $volunteerRepository,  Passwords $passwords)
    {
        $this->volunteerRepository = $volunteerRepository;
        $this->passwords = $passwords;
    }

    public function authenticate(array $credentials): IIdentity
    {
        [$personEmail, $password] = $credentials;

        /** @var \SousedskaPomoc\Entities\Volunteer $user */
        $user = $this->volunteerRepository->getByEmail($personEmail);

        if (!($user instanceof Volunteer)) {
            throw new AuthenticationException('User not found.');
        }
        if (!$this->passwords->verify($password, $user->getPassword())) {
            throw new AuthenticationException('Invalid password.');
        }

        return new Identity($user->getId(), $user->getRole()->getName(), ['personEmail' => $user->getPersonEmail(), 'personPhone'=>$user->getPersonPhone(), 'personName'=>$user->getPersonName(), 'city'=>$user->getAddress()->getCity()]);
    }
}