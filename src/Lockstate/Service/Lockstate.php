<?php

namespace Lockstate\Service;

use Lockstate\Entity\GuestCode;
use Lockstate\Entity\Location;
use Lockstate\Entity\Lock;
use Lockstate\Entity\Schedule;
use Lockstate\Entity\UserCode;
use Lockstate\Entity\WebHook;
use Zend\Http\Client;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Json\Json;

class Lockstate
{
    const END_POINT_LOCKS        = 'locks';
    const END_POINT_ACCESS_CODES = 'access_codes';
    const END_POINT_SCHEDULES    = 'access_schedules';
    const END_POINT_DEVICES      = 'devices';
    const END_POINT_EVENTS       = 'events';
    const END_POINT_GUEST_CODES  = 'guest_codes';
    const END_POINT_USER_CODES   = 'user_codes';
    const END_POINT_LOCATIONS    = 'locations';
    const END_POINT_WEBHOOKS     = 'webhooks';

    const END_POINT_AUTH  = 'oauth';
    const END_POINT_TOKEN = 'token';

    const END_POINT_LOCK   = 'lock';
    const END_POINT_UNLOCK = 'unlock';

    const PAGE       = 'page';
    const ATTRIBUTES = 'attributes';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var string
     */
    private $authUri;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var integer
     */
    private $tokenExpireTime;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @param Client $httpClient
     * @param string $baseUri
     * @param string $authUri
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($httpClient, $baseUri, $authUri, $appId, $appSecret)
    {
        $this->client       = $httpClient;
        $this->baseUri      = $baseUri;
        $this->authUri      = $authUri;
        $this->appId        = $appId;
        $this->appSecret    = $appSecret;

        $this->generateAuthToken();
    }

    /**
     * @param string $token
     */
    public function setAuthToken($token)
    {
        $this->authToken = $token;
    }

    /**
     * @return string $token
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @return int
     */
    public function getTokenExpireTime()
    {
        return $this->tokenExpireTime;
    }

    /**
     * @param int $tokenExpireTime
     */
    public function setTokenExpireTime($tokenExpireTime)
    {
        $this->tokenExpireTime = $tokenExpireTime;
    }


    /**
     * @return bool
     * @throws \Exception
     */
    public function generateAuthToken()
    {
        $this->client
            ->setUri(implode('/', [$this->authUri, self::END_POINT_AUTH, self::END_POINT_TOKEN]))
            ->setMethod(Request::METHOD_POST)
            ->setHeaders(['Content-Type' => 'application/x-www-form-urlencoded'])
            ->setParameterPost([
                'client_id' => $this->appId,
                'client_secret' => $this->appSecret,
                'grant_type' => 'client_credentials',
            ]);

        $response = $this->client->send();

        $responseBody = Json::decode($response->getBody());
        $this->setAuthToken($responseBody->access_token);
        $this->setTokenExpireTime(time() + $responseBody->expires_in);

        return true;
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getAccessCodes($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_ACCESS_CODES,
            [self::PAGE => $page]
        );
    }

    /**
     * @param int $lockId
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getAccessCodesOnLock($lockId, $page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_ACCESS_CODES]),
            [self::PAGE => $page]
        );
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getAccessSchedules($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_SCHEDULES,
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $scheduleId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getAccessSchedule($scheduleId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_SCHEDULES, $scheduleId])
        );
    }

    /**
     * @param string $lockId
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getAccessSchedulesOnLock($lockId, $page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_SCHEDULES]),
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $lockId
     * @param string $scheduleId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getAccessScheduleOnLock($lockId, $scheduleId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_SCHEDULES, $scheduleId])
        );
    }

    /**
     * @param string $lockId
     * @param Schedule $schedule
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function createAccessScheduleOnLock($lockId, Schedule $schedule)
    {
        return $this->send(
            Request::METHOD_POST,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_SCHEDULES]),
            [self::ATTRIBUTES => $schedule->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @param string $scheduleId
     * @param Schedule $schedule
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function updateAccessScheduleOnLock($lockId, $scheduleId, Schedule $schedule)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_SCHEDULES, $scheduleId]),
            [self::ATTRIBUTES => $schedule->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @param string $scheduleId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function deleteAccessScheduleOnLock($lockId, $scheduleId)
    {
        $result = $this->send(
            Request::METHOD_DELETE,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_SCHEDULES, $scheduleId])
        );

        return ($result !== false);
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getDevices($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_DEVICES,
            [self::PAGE => $page]
        );
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getEvents($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_EVENTS,
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $eventId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getEvent($eventId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_EVENTS, $eventId])
        );
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getGuestCodes($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_GUEST_CODES,
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $guestCodeId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getGuestCode($guestCodeId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_GUEST_CODES, $guestCodeId])
        );
    }

    /**
     * @param string $lockId
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getGuestCodesOnLock($lockId, $page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_GUEST_CODES]),
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $lockId
     * @param string $guestCodeId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getGuestCodeOnLock($lockId, $guestCodeId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_GUEST_CODES, $guestCodeId])
        );
    }

    /**
     * @param string $lockId
     * @param GuestCode $guestCode
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function createGuestCodeOnLock($lockId, GuestCode $guestCode)
    {
        if (is_null($guestCode->getFirstName())) {
            throw new \Exception('First name is required');
        }

        if (is_null($guestCode->getValue())) {
            throw new \Exception('Value is required');
        }

        if (is_null($guestCode->getEndsAt())) {
            throw new \Exception('Start time is required');
        }

        if (is_null($guestCode->getStartsAt())) {
            throw new \Exception('End time is required');
        }

        return $this->send(
            Request::METHOD_POST,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_GUEST_CODES]),
            [self::ATTRIBUTES => $guestCode->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @param string $guestCodeId
     * @param GuestCode $guestCode
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function updateGuestCodeOnLock($lockId, $guestCodeId, GuestCode $guestCode)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_GUEST_CODES, $guestCodeId]),
            [self::ATTRIBUTES => $guestCode->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @param string $guestCodeId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function deleteGuestCodeOnLock($lockId, $guestCodeId)
    {
        $result = $this->send(
            Request::METHOD_DELETE,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_GUEST_CODES, $guestCodeId])
        );

        return ($result !== false);
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getLocations($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_LOCATIONS,
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $locationId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getLocation($locationId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCATIONS, $locationId])
        );
    }

    /**
     * @param string $locationId
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getDevicesOnLocation($locationId, $page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCATIONS, $locationId, self::END_POINT_DEVICES])
        );
    }

    /**
     * @param Location $location
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function createLocation(Location $location)
    {
        if (is_null($location->getName())) {
            throw new \Exception('Name is required');
        }

        if (is_null($location->getTimeZone())) {
            throw new \Exception('Time zone is required');
        }

        return $this->send(
            Request::METHOD_POST,
            '/' . self::END_POINT_LOCATIONS,
            [self::ATTRIBUTES => $location->getArrayCopy()]
        );
    }

    /**
     * @param string $locationId
     * @param Location $location
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function updateLocation($locationId, Location $location)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_LOCATIONS, $locationId]),
            [self::ATTRIBUTES => $location->getArrayCopy()]
        );
    }

    /**
     * @param string $locationId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function deleteLocation($locationId)
    {
        $result = $this->send(
            Request::METHOD_DELETE,
            '/' . implode('/', [self::END_POINT_LOCATIONS, $locationId])
        );

        return ($result !== false);
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getLocks($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_LOCKS,
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $lockId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getLock($lockId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId])
        );
    }

    /**
     * @param Lock $lock
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function createLock(Lock $lock)
    {
        if (is_null($lock->getName())) {
            throw new \Exception('Name is required');
        }

        if (is_null($lock->getSerialNumber())) {
            throw new \Exception('Serial number is required');
        }

        if (is_null($lock->getSerialNumber())) {
            throw new \Exception('Location id is required');
        }

        return $this->send(
            Request::METHOD_POST,
            '/' . self::END_POINT_LOCKS,
            [self::ATTRIBUTES => $lock->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @param Lock $lock
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function updateLock($lockId, Lock $lock)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId]),
            [self::ATTRIBUTES => $lock->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function deleteLock($lockId)
    {
        $result = $this->send(
            Request::METHOD_DELETE,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId])
        );

        return ($result !== false);
    }

    /**
     * @param string $lockId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function lockTheLock($lockId)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_LOCK])
        );
    }

    /**
     * @param string $lockId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function unlockTheLock($lockId)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_UNLOCK])
        );
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getUserCodes($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_USER_CODES,
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $userCodeId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getUserCode($userCodeId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_USER_CODES, $userCodeId])
        );
    }

    /**
     * @param string $lockId
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getUserCodesOnLock($lockId, $page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_USER_CODES]),
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $lockId
     * @param string $userCodeId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getUserCodeOnLock($lockId, $userCodeId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_USER_CODES, $userCodeId])
        );
    }

    /**
     * @param string $lockId
     * @param UserCode $userCode
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function createUserCodeOnLock($lockId, UserCode $userCode)
    {
        if (is_null($userCode->getFirstName())) {
            throw new \Exception('First name is required');
        }

        if (is_null($userCode->getValue())) {
            throw new \Exception('Value is required');
        }

        return $this->send(
            Request::METHOD_POST,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_USER_CODES]),
            [self::ATTRIBUTES => $userCode->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @param string $userCodeId
     * @param UserCode $userCode
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function updateUserCodeOnLock($lockId, $userCodeId, UserCode $userCode)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_USER_CODES, $userCodeId]),
            [self::ATTRIBUTES => $userCode->getArrayCopy()]
        );
    }

    /**
     * @param string $lockId
     * @param string $userCodeId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function deleteUserCodeOnLock($lockId, $userCodeId)
    {
        $result = $this->send(
            Request::METHOD_DELETE,
            '/' . implode('/', [self::END_POINT_LOCKS, $lockId, self::END_POINT_USER_CODES, $userCodeId])
        );

        return ($result !== false);
    }

    /**
     * @param int $page
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getWebHooks($page = 1)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . self::END_POINT_WEBHOOKS,
            [self::PAGE => $page]
        );
    }

    /**
     * @param string $webHookId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function getWebHook($webHookId)
    {
        return $this->send(
            Request::METHOD_GET,
            '/' . implode('/', [self::END_POINT_WEBHOOKS, $webHookId])
        );
    }

    /**
     * @param WebHook $webHook
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function createWebHook(WebHook $webHook)
    {
//        var_dump($webHook->getArrayCopy()); exit();
        return $this->send(
            Request::METHOD_POST,
            '/' . self::END_POINT_WEBHOOKS,
            [self::ATTRIBUTES => $webHook->getArrayCopy()]
        );
    }

    /**
     * @param string $webHookId
     * @param WebHook $webHook
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function updateWebHook($webHookId, WebHook $webHook)
    {
        return $this->send(
            Request::METHOD_PUT,
            '/' . implode('/', [self::END_POINT_WEBHOOKS, $webHookId]),
            [self::ATTRIBUTES => $webHook->getArrayCopy()]
        );
    }

    /**
     * @param string $webHookId
     * @return bool|\stdClass
     * @throws \Exception
     */
    public function deleteWebHook($webHookId)
    {
        $result = $this->send(
            Request::METHOD_DELETE,
            '/' . implode('/', [self::END_POINT_WEBHOOKS, $webHookId])
        );

        return ($result !== false);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $data
     * @return \stdClass|boolean
     * @throws \Exception
     */
    public function send($method, $uri, $data = [])
    {
        $this->client->resetParameters();

        if ($this->getTokenExpireTime() <= time()) {
            $this->generateAuthToken();
        }

        $headers = [
            'Authorization' => 'Bearer ' . $this->authToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.lockstate.v1+json'
        ];

        if ($method == Request::METHOD_GET) {
            $this->client->setParameterGet($data);
        } else {
            $this->client->setRawBody(Json::encode($data));
        }

        $this->client
            ->setUri($this->baseUri . $uri)
            ->setMethod($method)
            ->setHeaders($headers);

        $response = $this->client->send();

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return Json::decode($response->getBody());
        } else if ($response->isNotFound()) {
            return false;
        } else if ($response->getStatusCode() === Response::STATUS_CODE_401) {
            if ($this->generateAuthToken()) {
                $this->send($method, $uri, $data);
            } else {
                throw new \Exception('Authentication Failed', Response::STATUS_CODE_401);
            }
        } else {
            throw new \Exception($response->getReasonPhrase(), $response->getStatusCode());
        }
    }
}
